<?PHP
   
   import( 'pages.json' );
   
   class AuthLogoutAPI extends JSONPage implements IValidatedView, NoAuthRequired {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
               ),
               'get' => array(
               ),
            ),
         
         );
         
      }

      /**
       * Authenticate by username, password and portal identifier
       * 
       * @api-name auth.logout
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
         
         Login::logout();
         
         $this->message = 'OK';
         $this->result = true;
      
      }
      
   }
   
?>