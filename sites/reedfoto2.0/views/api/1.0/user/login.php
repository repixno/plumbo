<?php

   /**
    * Try to login a user via api
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.user' );

   class UserLogin extends JSONPage implements NOAuthRequired, IValidatedView {
      
      
      /**
       * Validate the input data
       *
       * @return array
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'username' => VALIDATE_STRING,
                  'password' => VALIDATE_STRING,
               ),
            )
         );
         
      }
      
          
      /**
       * Login user via API
       * 
       * @api-name user.login
       * @api-auth required
       * @api-param-optional username String Username
       * @api-param-optional password String Password
       * @api-post-optional username String Username
       * @api-post-optional password String Password
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $username = null, $password = null ) {
         
         if( isset( $_POST['username'] ) ) {
            $username = $_POST['username'];
         }
         if( isset( $_POST['password'] ) ) {
            $password = $_POST['password'];
         }
         $portal = Dispatcher::getPortal();

         // We have all necessary params?
         $this->result = false;
         $this->message = 'Failed to login user';
         if( !isset( $username ) || !isset( $password ) ) return false;
         
         $this->result = false;
         $this->message = 'User already logged in';
         if( Login::isLoggedIn() ) return false;
         
         try {
            
            $refUser = User::fromUsernameAndPassword( $username, $password, $portal );
            
            // User set? Success!!!
            if( $refUser instanceof User && $refUser->isLoaded() ) {
               
               if( Login::byUserObject( $refUser ) ) {
                  $this->result = $refUser->asArray();
                  $this->message = 'OK';
                  return true;
               }
               
            }
            
            // Fail
            $this->result = false;
            $this->message = 'Failed to login user';
            return false;
            
         } catch( Exception $e ) {}
         
         // Fail
         $this->result = false;
         $this->message = 'Failed to login user';
         return false;
         
      }
      
   }


?>