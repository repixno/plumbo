<?PHP
   
   import( 'pages.json' );
   
   class AuthUpdateTokenAPI extends JSONPage implements IValidatedView, NoAuthRequired {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'token'    => VALIDATE_STRING,
                  'sign'   => VALIDATE_STRING,
               ),
               'get' => array(
                  'token'    => VALIDATE_STRING,
                  'sign'   => VALIDATE_STRING,
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
         	
         	if( $token ) {
         		Session::set( 'xtcitoken', $token );
         	}
         	
         	$this->message = 'OK';
         	$this->result = true;
         	
         } catch( Exception $e ) {
         
            $this->message = $e->getMessage();
            $this->result = false;
            
         }
         
      }
      
   }
   
?>