<?php


   /**
    * API for changing user password
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'pages.json' );
   import( 'website.user' );

   class APIUserSetPassword extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'oldpassword'     => VALIDATE_STRING,
                  'newpassword'     => VALIDATE_STRING,
                  'newpasswordrep'  => VALIDATE_STRING,
               ),
               'get' => array(
                  'oldpassword'     => VALIDATE_STRING,
                  'newpassword'     => VALIDATE_STRING,
                  'newpasswordrep'  => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      
      /**
       * Change user password via API
       * 
       * @api-name user.set.password
       * @api-post-optional oldpassword String Oldpassword
       * @api-post-optional newpassword String Newpassword
       * @api-post-optional newpasswordrep String Newpasswordrep
       * @api-get-optional oldpassword String Oldpassword
       * @api-get-optional newpassword String Newpassword
       * @api-get-optional newpasswordrep String Newpasswordrep
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         $oldpassword      = $_POST['oldpassword'] ? $_POST['oldpassword'] : $_GET['oldpassword'];
         $newpassword      = $_POST['newpassword'] ? $_POST['newpassword'] : $_GET['newpassword'];
         $newpasswordrep   = $_POST['newpasswordrep'] ? $_POST['newpasswordrep'] : $_GET['newpasswordrep'];
         
         if ( !empty( $oldpassword ) && !empty( $newpassword ) && !empty( $newpasswordrep ) ) {
            
            $user = new User( Login::userid() );
            
            if( User::validatePassword( $oldpassword, $user->password ) ) {
               
               if ( $newpassword == $newpasswordrep ) {

                  $this->result = true;
                  $this->message = 'OK';
                  return true;
                  //$user->password = crypt( $passwordNew );

               } else {

                  $this->result = false;
                  $this->message = 'The two new password entries do not match';
                  $this->errorcode = 1;
                  return false;

               }
               
            } else {
            
               $this->result = false;
               $this->message = 'Password mismatch';
               $this->errorcode = 2;
               return false;
               
            }
            
         } else {
            
            $this->result = false;
            $this->message = 'A required password is empty';
            $this->errorcode = 3;
            return false;
            
         }
         
      }
      
   }


?>