<?PHP
   
   import( 'pages.json' );
   import( 'xtci.xtci' );
   
   define('XTCI_LOG', false);
   define('XTCI_HTTP_PORT', 80 );
   
   class AuthLoginByXTCITokenAPI extends JSONPage implements IValidatedView, NoAuthRequired {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'username' => VALIDATE_STRING,
                  'token'    => VALIDATE_STRING,
                  'portal'   => VALIDATE_STRING,
               ),
               'get' => array(
                  'username' => VALIDATE_STRING,
                  'token'    => VALIDATE_STRING,
                  'portal'   => VALIDATE_STRING,
               ),
            ),
         
         );
         
      }

      /**
       * Authenticate by username, token and portal identifier
       * 
       * @api-name auth.byxtcitoken
       * @api-post-optional token String Token
       * @api-get-optional token String Token
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         $token = isset( $_POST['token'] ) ? $_POST['token'] : $_GET['token'];
         
         try {
         	
         	$server = array(
					// 'controller' => 'http://cewetest:APtH_uPyZce1@no.cewetest.novomind.com/fotobutikk/webapp/wcs/stores/servlet',
					'controller' => 'https://www.japanphoto.no/fotobutikk/webapp/wcs/stores/servlet',
					'dtd' => 'https://orderport01.photoprintit.de/xtci2.3/dtd'
				);
         	
				$token = str_replace( ' ', '+', $token );
				
         	list($data, $errorcode, $errortext, $response, $response ) = xtci2x_userinfo_request( $server, $token );
		$this->output = array( $data, $errorcode, $errortext, $response, $response  );
         	
         	if( $errorcode != 0 || !isset( $data[0]->content->email ) ) {
	            throw new Exception( 'Invalid login token. Please try again later!' );
         	}
         	
         	$content = $data[0]->content;
         	$email =  trim( (string) $content->email );
            
      		Session::set( 'xtcitoken', $token );
         	
            // attempt to find the userid in the database
            $userid = User::UserIDfromUsernameAndPortal( $email, '' );
			
            if( !$userid ) {
               
               // create a new user
               $user = new User();
               $user->portal = '';
               $user->username = $email;
               
            } else {
               
               // load the existing user
               $user = new User( $userid );
               
               // make sure the user is ours
               /*
               if( $user->portal != '' ) {
                  
                  $this->message = 'Error - User not yours (Duplicate identifier?)';
                  $this->result = false;
                  return false;
                  
               }
               */
               
            }
            
            // set the various fields on the user object
            $user->fullname   = trim( trim( $_POST['firstname'].' '.$_POST['middlename'] ).' '.$_POST['lastname'] );
            $user->firstname  = (string) $content->firstname;
            $user->middlename = '';
            $user->lastname   = (string) $content->lastname;
            
            if( (string) $content->street ) {
               $user->streetaddress = (string) $content->street;
            }
            if( (string) $content->zip ) {
               $user->zipcode = (string) $content->zip;
            }
            if( (string) $content->city ) {
               $user->city = (string) $content->city;
            }
            if( $email ) {
               $user->contactemail = $email;
            }
            if( (string) $content->phone ) {
               $user->phone  = (string) $content->phone;
               $user->phonenight  = (string) $content->phone;
            }
            if( (string) $content->cellularphone ) {
               $user->cellphone  = (string) $content->cellularphone;
            }
            /*
               $user->birthdate  = $_POST['birthdate'];
               $user->gender     = $_POST['gender'];
            */
            
            // find the countryid by mapping on 2char
            config( 'website.countries' );
            $countries = Settings::getSection( 'efcountries' );
            $user->country = 160; // default to Norway
            foreach( $countries as $countryid => $country ) {
               if( strtoupper( $country['2char'] ) == (string) $content->iso_country ) {
                  $user->country = $countryid; break;
               }
            }
            
            // save the user
            $user->save();
         	
         	if( $token = Login::byUserId( $user->userid ) ) {
	            $this->message = 'OK';
	            $this->result = true;
	            $this->token = $token;
	         } else {
	            throw new Exception( 'Invalid username/password' );
	         }
	         
         } catch( Exception $e ) {
         
            $this->message = $e->getMessage();
            $this->result = false;
            
         }
         
      }
      
   }
   
?>
