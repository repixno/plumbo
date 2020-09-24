<?PHP
   
   import( 'pages.json' );
   
   class AuthLoginAPI extends JSONPage implements IValidatedView, NoAuthRequired {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'username' => VALIDATE_STRING,
                  'password' => VALIDATE_STRING,
                  'portal'   => VALIDATE_STRING,
               ),
               'get' => array(
                  'username' => VALIDATE_STRING,
                  'password' => VALIDATE_STRING,
                  'portal'   => VALIDATE_STRING,
               ),
            ),
         
         );
         
      }

      /**
       * Authenticate by username, password and portal identifier
       * 
       * @api-name auth.login
       * @api-post-optional username String Username
       * @api-post-optional password String Password
       * @api-post-optional portal String Portal identifier
       * @api-get-optional username String Username
       * @api-get-optional password String Password
       * @api-get-optional portal String Portal identifier
       * @api-result token String Unique login-token
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      public function Execute() {
         
         $username = isset( $_POST['username'] ) ? $_POST['username'] : $_GET['username'];
         $password = isset( $_POST['password'] ) ? $_POST['password'] : $_GET['password'];
         $portal   = isset( $_POST['portal'] )   ? $_POST['portal']   : $_GET['portal'];
         
         if( $token = Login::byPortalUsernameAndPassword( $portal, $username, $password ) ) {
            
            $this->message = 'OK';
            $this->result = true;
            $this->token = $token;
            
         } else {
            
            $this->message = 'Invalid username/password';
            $this->result = false;
            
         }
         
      }
      
   }
   
?>