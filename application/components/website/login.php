<?PHP
   
   // import required modules
   import( 'core.security' );
   import( 'core.session' );
   
   import( 'website.user' );
   import( 'website.admin' );
   import( 'website.uploadedimagesarray' );
   
   model( 'site.betatester' );
   model( 'site.sessionlog' );
   
   /**
    * Handles login-related tasks
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Login {
      
      static $LoginFailure = false;
      static $LoginFailureReason;
      
      /**
       * Login a user by portal, username/password
       *
       * @param string $portal The portal of the user to log in
       * @param string $username The username of the user to log in
       * @param string $password The password of the user to log in
       * @param boolean $usecookie Whether to set an autologin cookie
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function byPortalUsernameAndPassword( $portal, $username, $password, $usecookie = false ) {
         
         // make sure we have a username
         if( !$username ) return false;
         
         // make sure we have a password
         if( !$password ) return false;
         
         // attempt to find the user based on username/password
         $user = User::fromUsernameAndPassword( $username, $password, $portal );
         
         // Is there a login failure?
         if( is_array( $user ) && !$user['result'] ) {
            
            Login::$LoginFailure = true;
            Login::$LoginFailureReason = $user['reason'];
            return false;
            
         }
         
         // attempt to login using this user-object
         return Login::byUserObject( $user, $usecookie );
         
      }
      
      /**
       * Login a user by userid
       *
       * @param integer $userid The userid of the user to log in
       * @param boolean $usecookie Whether to set an autologin cookie
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function byUserId( $userid, $usecookie = false ) {
         
         try{
            
            // attempt to find user by id
            $user = new User( $userid );
            
            // if the user was not found, clear it
            if( !$user->isLoaded() ) $user = null;
            
         } catch ( Exception $e ) {
            
            $user = null;
            
         }
         
         // attempt to login using this user-object
         return Login::byUserObject( $user, $usecookie );
         
      }
      
      /**
       * Login an already found user object
       *
       * @param User $user The user to login
       * @param boolean $usecookie Whether to set an autologin cookie
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function byUserObject( $user, $usecookie = false ) {
         
         // If have basket. Save the not logged in basket
         Cart::setMergeBasket();
         
         // by default, all logins fail
         Login::$LoginFailure = true;
         
         // make sure this is a user
         if( is_null( $user ) || !$user instanceof User ) {
            
            // return failure
            Login::$LoginFailureReason = __( 'User not registered' );
            return false;
            
         } else {
            
            // EF 2.x aliasfor support
            if( $user->aliasfor ) {
               
               // create a new user object for this user.
               $user = new User( $user->aliasfor );
               
               // make sure this is a user
               if( is_null( $user ) || !$user instanceof User ) {
                  
                  Login::$LoginFailureReason = __( 'User not registered' );
                  
                  // return failure
                  return false;
                  
               } else {
                  
                  return Login::byUserObject( $user );
                  
               }
               
            }
            
            // If user is deleted, do not let them login
            if( !is_null( $user->deleted ) ) return false;
            
            // the autologin key is created with the seedkey
            $seedkey = SecurityKeys::get( SECURITY_KEY_SEEDKEY );
            
            // retreive the number of days until it expires
            $logindays = Settings::Get( 'login', 'expiredays', 60 );
            
            // calculate the verification
            $localhash = sha1( sprintf( '%s-%s', $user->password, $seedkey ) );
            
            // calculate the autologin token
            $autologin = base64_encode( $user->userid.'|'.$localhash );
            
            // set cookies?
            if( $usecookie ) {
               
               // store the autologin cookie so we can log the user back in automatically
               setCookie( 'ef3autologin', $autologin, time() + ( 86400 * $logindays ), '/' );
               
            }
            
            // is this an admin account?
            try {
               $admin = new limitedAdmin();
               $isadmin = $admin->islimitedAdmin();
            } catch( Exception $e ) {
               $isadmin = false;
            }
            
            try {
               $limitedadmin = new limitedAdmin( $user->uid );
               $islimitedadmin = $limitedadmin->islimitedAdmin();
            } catch( Exception $e ) {
               $islimitedadmin = false;
            }
            
            
            // beta tester support
            try {
               $betatester = new DBBetaTester( $user->uid );
               if( !$betatester->isLoaded() ) {
                  throw new Exception( 'Beta tester not found!' );
               }
            } catch ( Exception $e ) {
               $betatester = new DBBetaTester();
               $betatester->uid = $user->uid;
               $betatester->started = date( 'Y-m-d H:i:s' );
               $betatester->save();
            }
            $betatester->lastlogin = date( 'Y-m-d H:i:s' );
            $betatester->save();
            
            // log this successful login
            $sessionlog = new DBSessionLog();
            $sessionlog->timestamp = date( 'Y-m-d H:i:s' );
            $sessionlog->uid = $user->uid;
            $sessionlog->portal = 'EF30:'.Dispatcher::getPortal();
            $sessionlog->sessionid = Session::id();
            $sessionlog->hostname = $_SERVER['HTTP_HOST'];
            $sessionlog->useragent = $_SERVER['HTTP_USER_AGENT'];
            $sessionlog->remoteip = $_SERVER['REMOTE_ADDR'];
            $sessionlog->save();
            
            // log out the previous user
            Login::clear();
            
            // set the login-data
            Session::set( 'logindata', array(
               'loggedin' => true,
               'userid' => $user->uid,
               'isadmin' => $isadmin,
               'limitedisadmin' => $islimitedadmin,
               'email'    => $user->getEmailAddress(),
               'username' => $user->username,
               'fullname' => $user->fullname,
               'sessionkey' => SecurityKeys::get( 'session' ),
            ) );
            
            // does the user have a language set?
            if( $user->language ) {
               
               // if so, set this language as active
               i18n::setLanguage( $user->language );
               
            }
            
            // "ef 2.x compatibility layer"
            $GLOBALS['autentisert'] = $_SESSION['autentisert'] = true;
            $GLOBALS['uid'] = $_SESSION['uid'] = $user->uid;
            $GLOBALS['beta'] = $_SESSION['beta'] = 0;
            // $GLOBALS['lang'] = '';
            // $GLOBALS['skin'] = '';
            
            // Move any uploaded images when not logged in
            // To user's account. Create a new album to 
            // put them in.
            UploadedImagesArray::move();
            UserSessionArray::clearItems( 'prev_uploaded_images' );
            
            // This login was successful!
            Login::$LoginFailure = false;
            
            try {
               // Try merging the two baskets
               Cart::mergeBasket();
            } catch( Exception $e ) {}
            
            
            // return success!
            return $autologin;
            
         }
         
      }
      
      /**
       * Logs in using a secure token, such as a cookie
       *
       * @param string $securetoken The token to utilize
       * @return boolean True on login success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function bySecureToken( $securetoken ) {
         
         try {
         
            // do we have autologin enabled?
            if( $securetoken ) {
               
               // expand the key and retrieve the user guid and the token   
               list( $userid, $token ) = explode( '|', base64_decode( $securetoken ) );
               
               // find the user based on the GUID
               $user = new User( $userid );
               
               // did we find this userguid?
               if( $user->isLoaded() ) {
                  
                  // the autologin key is created with the seedkey
                  $seedkey = SecurityKeys::get( SECURITY_KEY_SEEDKEY );
                  
                  // calculate the verification
                  $localhash = sha1( sprintf( '%s-%s', $user->password, $seedkey ) );
                  
                  // does the tokens match?
                  if( $localhash == $token ) {
                     
                     // login the user
                     return Login::byUserObject( $user, true );
                     
                  }
                  
               }
               
            }
            
            // return failure
            return false;
            
         } catch ( Exception $e ) {
            
            // return failure
            return false;
            
         }
         
      }
      
      /**
       * Clears data about the logged in user, but does not clear uploadedimagesarray
       * 
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function clear() {
         
         // delete the logindata from session
         Session::delete( 'logindata' );
         
         // "ef 2.x compatibility layer"
         $GLOBALS['autentisert'] = $_SESSION['autentisert'] = false;
         $GLOBALS['uid'] = $_SESSION['uid'] = 0;
         
         if( Login::isLoggedIn() && ( !isset( $cart['totalitems'] ) || $cart['totalitems'] == 0 ) ) {
            
            try {
               $basket = new Basket( Login::userid() );
               $basket->save();
            } catch( Exception $e ) {}
            
         }
         
         // Clear cart and purchased cart
         UserSessionArray::clearItems( 'cart' );
         UserSessionArray::clearItems( 'purchased_cart' );
         
      }
      
      /**
       * Logs out a logged in user
       * 
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function logout() {
         
         // clear any autologin cookies
         setCookie( 'ef3autologin', "", time() - 3600, '/' );
         
         // Clear all uploaded images
         UploadedImagesArray::clear();
         
         // Clear all permissions
         PermissionManager::current()->clearCache();
         
         // clear logged in data
         Login::clear();
         
         if( Dispatcher::getPortalId() == 'TK-001' ){
            import( 'xtci.config');
            import( 'xtci.xtci');
            import( 'xtci.xtci_sso');
            $XTCiForSSO = new XTCiForSSO();
            $response = $XTCiForSSO->xtci_endsession();
            $XTCiForSSO->clear();
            
         }
         
      }
      
      /**
       * Returns the value of a session-logindata-field, or
       * the entire session-logindata set if $field = null.
       *
       * @param string $field Optional field to retreive
       * @return mixed An array of data if field=null, otherwise the mixed value of the given field
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function data( $field = null ) {
         
         // fetch the session security key
         $sessionkey = SecurityKeys::get( 'session' );
         
         // setup default data
         $defaultdata = array(
            'loggedin' => false,
            'userid' => false,
            'isadmin' => false,
            'email'    => '',
            'username' => '',
            'fullname' => '', 
            'sessionkey' => $sessionkey,
         );
         
         // attempt to fetch the current login-data from session
         $sessiondata = Session::get( 'logindata', $defaultdata );
         $sessiondata['sessionid'] = session_id();
         
         // make sure our sessionkey is valid
         if( $sessiondata['sessionkey'] != $sessionkey ) {
            
            // if it is, fetch the data from from the default-data
            return is_null( $field ) ? $defaultdata : ( isset( $defaultdata[$field] ) ? $defaultdata[$field] : '' );
            
         } else {
            
            // if it is, fetch the data from from the session-data
            return is_null( $field ) ? $sessiondata : ( isset( $sessiondata[$field] ) ? $sessiondata[$field] : '' );
            
         }
         
      }
      
      /**
       * Retreives the userid of the currently logged in user
       *
       * @return integer The userid of the logged in user
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function userid() {
         
         // return the userid from the login-data
         return (int) Login::data( 'userid' );
         
      }
      
      /**
       * Returns whether someone is logged in or not
       *
       * @return boolean True if logged in, otherwise false
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function isLoggedIn() {
         
         // retreive the login-data
         $sessiondata = Login::data();
         
         // return whether we're logged in or not
         return $sessiondata['loggedin'] == true ? true : false;
         
      }
      
      /**
       * Returns whether someone is an admin or not
       *
       * @return boolean True if an admin, otherwise false
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function isAdmin() {
         
         // retreive the login-data
         $sessiondata = Login::data();
         
         // return whether we're an admin or not
         return $sessiondata['isadmin'] == true ? true : false;
         
      }
      
      static function islimitedAdmin() {
         
         // retreive the login-data
         $sessiondata = Login::data();
         
         // return whether we're an admin or not
         return $sessiondata['limitedisadmin'] == true ? true : false;
         
      }
      
      static function LoginFailed() {
         
         return Login::$LoginFailure ? true : false;
         
      }
      
      static function setAdmin( $admin ) {
         
         $data = Login::data();
         $data['isadmin'] = $admin ? true : false;
         Session::set( 'logindata', $data );
         return true;
         
      }
      static function setlimitedAdmin( $admin ) {
         
         $data = Login::data();
         $data['limitedisadmin'] = $admin ? true : false;
         Session::set( 'logindata', $data );
         return true;
         
      }
      
   }
   
?>
