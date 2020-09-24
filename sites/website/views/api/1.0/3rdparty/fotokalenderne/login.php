<?PHP
   
   import( 'pages.json' );
   
   class FotokalenderneAuthLoginAPI extends JSONPage implements IView, NoAuthRequired {
      
      /**
       * Authenticate by username and password against 3rdparty backend API
       * 
       * @api-name 3rdparty.fotokalenderne.login
       * @api-post-optional username String Username
       * @api-post-optional password String Password
       * @api-get-optional username String Username
       * @api-get-optional password String Password
       * @api-result token String Unique login-token
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      public function execute( $username = '', $password = '' ) {
         
         $username = isset( $_POST['username'] ) ? $_POST['username'] : $username;
         $password = isset( $_POST['password'] ) ? $_POST['password'] : $password;
         
         try {
            $xmldocument = sprintf( '<?xml version="1.0" encoding="utf-8"?>
                                     <registration>
                                       <mandatory>
                                          <email>%s</email>
                                          <pwd>%s</pwd>
                                       </mandatory>
                                     </registration>',
                                     $username,
                                     $password
                                  );
            
            $url = 'http://www.fotokalenderne.no/api/signup/sign.php';  // Test URL will replace with actual
            $clientURL = curl_init();    
            $PHP_Header[] = "";
            
            curl_setopt( $clientURL, CURLOPT_POST, 1 );
            curl_setopt( $clientURL, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $clientURL, CURLOPT_CUSTOMREQUEST, 'POST' );
            curl_setopt( $clientURL, CURLOPT_URL, $url );
            curl_setopt( $clientURL, CURLOPT_REFERER,'http://www.fotokalenderne.no' );
            curl_setopt( $clientURL, CURLOPT_TIMEOUT, 60 );
            curl_setopt( $clientURL, CURLOPT_HTTPHEADER, $PHP_Header );
            curl_setopt( $clientURL, CURLOPT_POSTFIELDS, $xmldocument );
            
            $response = curl_exec( $clientURL );
            curl_close( $clientURL );
            
            $response = simplexml_load_string( $response );
            
            if( (int) $response->error == 0 ) {
               
               $identifier = (string) $response->identifier;
               $redirecturl = (string) $response->redirecturl;
               $token = (string) $response->token;
               
               $user = User::fromUsername( $identifier );
               if( !$user instanceof DBUser || !$user->isLoaded() ) {
                  throw new Exception( 'Login failed. Local user not found!' );
               }
               
               $user = new User( $user->uid );
               if( !$user instanceof User || !$user->isLoaded() ) {
                  throw new Exception( 'Login failed. Local user not valid!' );
               }
               
               if( !Login::byUserObject( $user ) ) {
                  throw new Exception( 'Login failed. Local user not valid!' );
               }
               
               $user->setPreference( 'fotokalenderne_token', $token );
               $user->setPreference( 'fotokalenderne_redirect', $redirecturl );
               
               $this->token = $token;
               $this->result = true;
               $this->message = 'OK';
               
            } else {
               
               $this->result = false;
               $this->message = (string) $response->description;
               
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
   }
   
?>