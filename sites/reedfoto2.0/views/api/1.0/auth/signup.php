<?PHP
   
   import( 'website.login.authkey' );
   import( 'website.login.authtoken' );
   import( 'website.user' );
   import( 'pages.json' );
   
   class AuthSignupAPI extends JSONPage implements IView, NoAuthRequired {
      
      /*
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'publickey' => VALIDATE_STRING,
                  'signature' => VALIDATE_STRING,
                  'identifier'=> VALIDATE_STRING,
                  'firstname' => VALIDATE_STRING,
                  'middlename'=> VALIDATE_STRING,
                  'lastname'  => VALIDATE_STRING,
                  'address1'  => VALIDATE_STRING,
                  'address2'  => VALIDATE_STRING,
                  'zipcode'   => VALIDATE_STRING,
                  'city'      => VALIDATE_STRING,
                  'country'   => VALIDATE_STRING,
                  'email'     => VALIDATE_STRING,
                  'phonework' => VALIDATE_STRING,
                  'phonehome' => VALIDATE_STRING,
                  'phonecell' => VALIDATE_STRING,
                  'birthdate' => VALIDATE_STRING,
                  'gender'    => VALIDATE_STRING,
               ),
            ),
         );
         
      }
      */
      
      /**
       * Sign up/in a remote user, get authorization token and signed loginurl in return
       * 
       * @api-name auth.signup
       * @api-post publickey String Your public key used to sign the data
       * @api-post signature String The signature of your private key and data
       * @api-post identifier String A unique identifier @your.host.name.eurofoto.no
       * @api-post firstname String The user's given name (firstname)
       * @api-post-optional middlename String The user's middle name
       * @api-post lastname String The user's family name (lastname)
       * @api-post-optional address1 String The user's address (line 1)
       * @api-post-optional address2 String The user's address (line 2)
       * @api-post-optional zipcode String The user's zipcode
       * @api-post-optional city String The user's city
       * @api-post-optional country String The user's country, ISO 3166-1-alpha-2 code
       * @api-post-optional email String The user's contact email address
       * @api-post-optional phonework String The user's daytime phone number
       * @api-post-optional phonehome String The user's nighttime phone number
       * @api-post-optional phonecell String The user's cellphone number
       * @api-post-optional birthdate String The user's birthdate in dd.mm.yyyy format
       * @api-post-optional gender String The user's gender as either m, f, male or female
       * @api-result token String Unique login-token
       * @api-result url String Login URL to redirect the user to after authentication
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         // attempt to load the auth key based on the public key given
         $authkey = AuthKey::fromFieldValue( array( 'publickey' => $_POST['publickey'] ), 'AuthKey', false );
         if( !$authkey || !$authkey->isLoaded() ) {
            $this->message = 'Access denied - Invalid Public Key';
            $this->result = false;
            return false;
         }
         
         // do a quick IP check
         if( $authkey->onlyips ) {
            if( strpos( $authkey->onlyips, $_SERVER['REMOTE_ADDR'] ) === false ) {
               $this->message = 'Access denied - IP address is not allowed';
               $this->result = false;
               return false;
            }
         }
         
         // find and unset posted signature
         $postsign = $_POST['signature'];
         unset( $_POST['signature'] );
         
         // calculate the signature
         $signature = sha1( $authkey->privatekey.
                            implode( '', $_POST )
                          );
         
         // validate it
         if( $signature != $postsign ) {
            
            $this->message = 'Access denied - Signature not valid';
            $this->result = false;
            return false;
            
         }
         
         // ensure the user identifier has enough entropy
         if( strlen( $_POST['identifier'] ) < 10 ) {
            $this->message = 'Error - The user identifier needs to be at least 10 characters long: '.$_POST['identifier'];
            $this->result = false;
            return false;
         }
         
         try {
            
            // attempt to find the userid in the database
            $userid = User::UserIDfromUsernameAndPortal( $_POST['identifier'] );
            if( !$userid ) {
               
               // create a new user
               $user = new User();
               $user->portal = $authkey->portal;
               $user->username = $_POST['identifier'];
               
            } else {
               
               // load the existing user
               $user = new User( $userid );
               
               // make sure the user is ours
               if( $user->portal != $authkey->portal ) {
                  
                  $this->message = 'Error - User not yours (Duplicate identifier?)';
                  $this->result = false;
                  return false;
                  
               }
               
            }
            
            // set the various fields on the user object
            $user->fullname   = trim( trim( $_POST['firstname'].' '.$_POST['middlename'] ).' '.$_POST['lastname'] );
            $user->firstname  = $_POST['firstname'];
            $user->middlename = $_POST['middlename'];
            $user->lastname   = $_POST['lastname'];
            
            if( isset( $_POST['address1'] ) ) {
               $user->streetaddress = $_POST['address1'];
            }
            if( isset( $_POST['address2'] ) ) {
               $user->streetaddress2 = $_POST['address2'];
            }
            if( isset( $_POST['zipcode'] ) ) {
               $user->zipcode    = $_POST['zipcode'];
            }
            if( isset( $_POST['city'] ) ) {
               $user->city       = $_POST['city'];
            }
            if( isset( $_POST['email'] ) ) {
               $user->contactemail = $_POST['email'];
            }
            if( isset( $_POST['phonework'] ) ) {
               $user->phone      = $_POST['phonework'];
            }
            if( isset( $_POST['phonehome'] ) ) {
               $user->phonenight = $_POST['phonehome'];
            }
            if( isset( $_POST['phonecell'] ) ) {
               $user->cellphone  = $_POST['phonecell'];
            }
            if( isset( $_POST['birthdate'] ) ) {
               $user->birthdate  = $_POST['birthdate'];
            }
            if( isset( $_POST['gender'] ) ) {
               $user->gender     = $_POST['gender'];
            }
            
            // find the countryid by mapping on 2char
            config( 'website.countries' );
            $countries = Settings::getSection( 'efcountries' );
            $user->country = 160; // default to Norway
            foreach( $countries as $countryid => $country ) {
               if( $country['2char'] == $_POST['country'] ) {
                  $user->country = $countryid; break;
               }
            }
            
            // save the user
            $user->save();
            
            // save any custom preference fields
            foreach( $_POST as $key => $value ) {
               if( substr( $key, 0, 9 ) == 'userpref_' ) {
                  list(,$key) = explode('_', $key, 2 );
                  $user->setPreference( $key, $value );
               }
            }
            
            // create an authtoken
            $authtoken = new AuthToken();
            $authtoken->authkeyid = $authkey->authkeyid;
            $authtoken->userid = $user->userid;
            $authtoken->ipaddress = $_SERVER['REMOTE_ADDR'];
            $authtoken->signed = 'now';
            $authtoken->save();
            
            // attempt to login as the user
            if( $token = Login::byUserId( $user->userid ) ) {
               
               // return the token and complete redirect URL
               $this->message = 'OK';
               $this->result = true;
               $this->token = $token;
               $this->url = sprintf( '%s/login/bytoken/%s', WebsiteHelper::rootBaseUrl(), $authtoken->token );
               return true;
               
            } else {
               
               // failed to assume identity of the user object
               $this->message = 'Error - Unable to assume identity of user object';
               $this->result = false;
               return false;
               
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Error - Unknown error: '.$e->getMessage();
            return false;
            
         }
         
      }
      
   }
   
?>