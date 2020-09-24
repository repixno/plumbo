<?php

   /**
    * 
    * Check and validate a given validation code
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   model( 'user.smsservices' );


   class APIToggleValidation extends JSONPage implements IValidatedView {
      
      
      /**
       * Validate params
       * 
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function Validate() {
         
         return array(
            'execute' =>  array(
               'fields' => array(
                  VALIDATE_STRING,
               )
            )
         );
         
      }
      
      
      /**
       * Toggle validation
       *
       * @param string $code
       * @return bool
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * @api-name sms.toggle.validation
       * @api-auth required
       * @api-param code String Validation code
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $code = null ) {
         
         $this->result = false;
         $this->message = 'Missing param';
         if( is_null( $code ) ) return false;
         
         $service = DBSmsServices::fromUserid( Login::userid() );
         
         $this->result = false;
         $this->message = 'No pending validation for this user';
         if( !$service instanceof DBSmsServices || !$service->isLoaded() ) return false;
         
         if( $service->validation_code != $code ) {
            $this->result = false;
            $this->message = 'Validation code mismatch';
            return false;
            
         } else {
            
            $service->validated = date( 'Y-m-d H:i:s' );
            $service->save();
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         }
         
         
         
      }
      
   }


?>