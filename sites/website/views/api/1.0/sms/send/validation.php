<?php

   /**
    * API for sending a validation code
    * to a cell nr. It first checks if 
    * it's a valid cellnr then sends code
    * as a SMS to user. User later has to input
    * this to validate cell nr.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'validate.phone.cell' );
   import( 'sms.validation' );
   model( 'site.sms' );
   model( 'user.smsservices' );

   class APISendValidation extends JSONPage implements IValidatedView {
      
      
      /**
       * Validate params and post
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
               )
            )
         );
         
      }
      
      
      /**
       * Validate cellphone number and send SMS
       *
       * @param string $nr
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * @api-name sms.send.validation
       * @api-auth required
       * @api-param number Cellphone number
       * @api-result error String SMS validation result
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */   
      public function Execute( $nr = '' ) {
         
         $this->result = false;
         $this->message = 'Number is not valid';
         if( !ValidateCellPhone::validate( $nr ) ) return false;
         
         
         // Create and setup a new SMS
         $sms = new SMSValidation();
         $sms->setToNumber( $nr );

         // Need to create a sequence for msgid?
         if( $sms->send() ) {
            
            // Update service table
            $this->updateServices( $nr, $sms->getValidationCode() );
            
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } else {
            
            // TODO:
            // Need to implement real error messages.
            // Perhaps make a switch-clause in SMS class
            // and translate errorcodes to text.
            $this->result = false;
            $this->message = 'Failed sending validation sms';
            $this->error = $sms->getResult();
            return false;
            
         }
         
      }
      

      /**
       * Update service table. Set user
       * as validation pending.
       *
       * @param string $nr
       * @param string $code
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function updateServices( $nr, $code ) {

         $service = new DBSmsServices();
         $service->uid = Login::userid();
         $service->cellnr = $nr;
         $service->validation_code = $code;
         $service->created = date( 'Y-m-d H:i:s' );
         $service->save();
         
      }
      
   }


?>