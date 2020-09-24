<?php

   /**
    * API to check if a password is correct for a userid.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'validate.email' );

   class APIUserCheckPass extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            "execute" => array(
               "fields" => array(
                  VALIDATE_STRING,
               ),
               "get" => array(
                  "password" => VALIDATE_STRING
                  )
            )
         );

      }


      /**
       * Validate user's password
       * 
       * @api-name user.checkpass
       * @api-auth required
       * @api-param-optional password String The password to validate
       * @api-get-optional password String The password to validate
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute( $password = '' ) {

         $getPass = $_GET[ 'password' ];
         if ( empty( $password ) ) {

            if ( !empty( $getPass) ) {

               $password = $getPass;

            } else {
               
               $this->result = false;
               $this->message = "Required input parameter missing or invalid (password)";

               return $this->returnSingleValue( false );

            }

         }

         $user = new User( Login::userid() );
         return $this->returnSingleValue( User::validatePassword( $password, $user->password ) );

      }

   }

?>
