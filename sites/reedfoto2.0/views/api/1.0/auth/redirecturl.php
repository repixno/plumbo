<?PHP
   
   import( 'website.login.authkey' );
   import( 'website.login.authtoken' );
   import( 'pages.json' );
   
   class AuthRedirectUrlAPI extends JSONPage implements IView {
      
      /**
       * Returns a signed loginurl
       * 
       * @api-name auth.redirecturl
       * @api-post publickey String Your public key used to create the Auth token
       * @api-result url String Login URL to redirect the user to after authentication
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         try {
         
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
            
            // create an authtoken
            $authtoken = new AuthToken();
            $authtoken->authkeyid = $authkey->authkeyid;
            $authtoken->userid = Login::userid();
            $authtoken->ipaddress = $_SERVER['REMOTE_ADDR'];
            $authtoken->signed = 'now';
            $authtoken->save();
            
            // return the token and complete redirect URL
            $this->url = sprintf( '%s/login/bytoken/%s', WebsiteHelper::rootBaseUrl(), $authtoken->token );
            $this->message = 'OK';
            return $this->result = true;
            
         } catch( Exception $e ) {
            
            // return failure
            $this->message = 'Error - Unknown error';
            return $this->result = false;
            
         }
         
      }
      
   }

   
?>