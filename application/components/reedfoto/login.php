<?PHP
   
   // import required modules
   import( 'core.security' );
   import( 'core.session' );
   
   import( 'reedfoto.user' );
   
   /**
    * Handles login-related tasks
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Login {
      
      static $LoginFailure = false;
      
      /**
       * Login a user by portal, username/password
       *
       * @param string $username The username of the user to log in
       * @param string $password The password of the user to log in
       * @param boolean $usecookie Whether to set an autologin cookie
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function byUsernameAndPassword( $username, $password, $usecookie = false ) {
         
         // attempt to find the user based on username/password
         $user = RFUser::fromUsernameAndPassword( $username, $password, $portal );
         
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
            $user = new RFUser( $userid );
            
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
       * @param RFUser $user The user to login
       * @param boolean $usecookie Whether to set an autologin cookie
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function byUserObject( $user, $usecookie = false ) {
         
         // by default, all logins fail
         Login::$LoginFailure = true;
         
         // make sure this is a user
         if( is_null( $user ) || !$user instanceof RFUser ) {
            
            // return failure
            return false;
            
         } else {
            
            // the autologin key is created with the seedkey
            $seedkey = SecurityKeys::get( 'seedkey' );
            
            // retreive the number of days until it expires
            $logindays = Settings::Get( 'login', 'expiredays', 60 );
            
            // calculate the verification
            $localhash = sha1( sprintf( '%s-%s', $user->password, $seedkey ) );
            
            // calculate the autologin token
            $autologin = base64_encode( $user->userid.'|'.$localhash );
            
            // set cookies?
            if( $usecookie ) {
               
               // store the autologin cookie so we can log the user back in automatically
               setCookie( 'autologin', $autologin, time() + ( 86400 * $logindays ), '/' );
               
            }
            
            // is this an admin account?
            // TODO
            
            // set the login-data
            Session::set( 'logindata', array(
               'loggedin' => true,
               'userid' => $user->userid,
               'isadmin' => $user->type == 'admin',
               'username' => $user->username,
               'fullname' => $user->fullname,
               'sessionkey' => SecurityKeys::get( 'session' ),
            ) );
            
            // This login was successful!
            Login::$LoginFailure = false;
            
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
               $user = new RFUser( $userid );
               
               // did we find this userguid?
               if( $user->isLoaded() ) {
                  
                  // the autologin key is created with the seedkey
                  $seedkey = SecurityKeys::get( 'seedkey' );
                  
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
       * Logs out a logged in user
       * 
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function logout() {
         
         // delete the logindata from session
         Session::delete( 'logindata' );
         
         // clear any autologin cookies
         setcookie( 'autologin', "", time() - 3600, '/' );
         
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
      
      static function LoginFailed() {
         
         return Login::$LoginFailure ? true : false;
         
      }
      
      static function setAdmin( $admin ) {
         
         $data = Login::data();
         $data['isadmin'] = $admin ? true : false;
         Session::set( 'logindata', $data );
         return true;
         
      }
      
   }
   
?>