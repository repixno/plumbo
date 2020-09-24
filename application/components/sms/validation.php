<?php

   /**
    * SMS for validation of cell nrs
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'sms.send' );
   model( 'site.sms' );
   //model( 'user.smsservices' ); 
   
   
   class SMSValidation extends SMS {
      
      private $code;
      private $ending;
      
      public function __construct() {
         
         $this->code = $this->createValidationCode();
         $this->ending = $this->getSender();
         
         $message = new DBSiteSMS( 1 ); // Validation code sms
         
         if( $message instanceof DBSiteSMS && $message->isLoaded() ) {

            $this->setMessage( __( $message->message, $this->code, $this->ending ) ); // Translate text body
            $this->setPrice( (int) $message->price ); // Set price
            
         }
         
      }
      
      /**
       * Creates a "unique" validation code
       * to send to user.
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function createValidationCode() {
         
         return substr( md5( uniqid( 'validation_' ) ), 0,5 );
         
      }
      
      
      /**
       * Get the created validation code
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function getValidationCode() {
         return $this->code;
      }
      
      
   }


?>