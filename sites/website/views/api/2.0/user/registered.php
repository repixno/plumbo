<?php

   /**
    * API to check if a username ( email )
    * is already registered at current portal.
    *
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'validate.email' );

   class APIUserRegistered extends JSONPage implements NoAuthRequired, IValidatedView {

      public function Validate() {

         return array(
            "execute" => array(
               "fields" => array(
                  VALIDATE_STRING,
               ),
               "get" => array(
                  "newEmail" => VALIDATE_STRING,
               )
            )
         );

      }

      /**
       * Check email availability
       * 
       * @api-name user.registered
       * @api-auth required
       * @api-param-optional email String The email address to check
       * @api-get-optional password String The email address to check
       * @api-result-singel Boolean true/false
       */ 
      public function Execute( $email = '' ) {

         if( isset( $_GET["newEmail"] ) ) {

            $email = $_GET["newEmail"];

         }

         //$this->result = false;
         //$this->message = "Missing username to check";
         if( empty( $email ) ) return $this->returnSingleValue( false );

         //$this->result = false;
         //$this->message = "Not a valid email address";
         if( !ValidateEmail::validate( $email ) ) {

            return $this->returnSingleValue( false );

         }
         
         if( User::hasNoPass( $email ) ) {
            
            return $this->returnSingleValue( true );
          
            
         }

         //$this->result = true;
         //$this->message = "false";
         //$this->newEmail = "false";
         if( !User::registered( $email, Dispatcher::getPortal() ) ) {

            return $this->returnSingleValue( true );

         }else{
            return $this->returnSingleValue( registered );
         }

         return $this->returnSingleValue( false );

         //$this->result = 'Is taken';
         //$this->newEmail = 'true';
         //$this->message = "true";



         //return true;

      }

   }


?>