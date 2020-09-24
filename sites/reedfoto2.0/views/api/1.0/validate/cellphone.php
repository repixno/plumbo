<?php

   /**
    * API for validating CellNr
    * Works with params and POST values
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'validate.phone.cell' );

   class CellPhone extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'number' => VALIDATE_STRING,
               )
            ),
         );
         
      }
      
      /**
       * Validate cellphone number
       * 
       * @api-name validate.cellphone
       * @api-auth required
       * @api-post-optional number Cellphone number
       * @api-param-optional number Cellphone number
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute( $nr = '' ) {
         
         // Get POST if any
         if( isset( $_POST['number'] ) ) {
            $nr = $_POST['number'];
         }
         
         $this->result = false;
         $this->message = 'Missing cell nr';
         if( strlen( $nr ) == 0 ) return false;
         
         // Try validating nr
         if( ValidateCellPhone::validate( $nr ) ) {
            
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } else { // Failed
            
            $this->result = false;
            $this->message = 'Invalid nr';
            return false;
         }
         
         $this->result = false;
         $this->message = 'Unknown error';
         return false;
         
      }
      
      
   }


?>