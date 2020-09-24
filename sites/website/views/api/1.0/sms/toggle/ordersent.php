<?php

   /**
    * Toggle setting for SMS order sent notice
    * on or off.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   model( 'user.smsservices' );

   class APIToggleSMSOrderSent extends JSONPage implements IValidatedView {
      
      
      /**
       * Validate params
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING
               )
            )
         );
         
      }
      
      
      /**
       * Toggle order sent
       *
       * @param integer $setting
       * @return bool
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * @api-name sms.toggle.ordersent
       * @api-auth required
       * @api-param setting Boolean Toggle setting. true or false
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute( $setting = null ) {

         $this->result = false;
         $this->message = 'Required input parameter missing or invalid (setting)';
         if( is_null( $setting ) ) return false;
         
         $this->result = false;
         $this->message = 'User cell phone is not validated';
         $service = DBSmsServices::fromUserid( Login::userid() );
         if( !$service instanceof DBSmsServices || !$service->isLoaded() ) return false;
            
         if( $setting == "true" ) {
            
            if( is_null( $service->order_sent_notice ) && !is_null( $service->validated ) ) {
               $service->subscribeOrder();
               $service->save();
            }
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } else if( $setting == "false" ) {
            
            if( !is_null( $service->order_sent_notice ) && !is_null( $service->validated ) ) {
               $service->unSubscribeOrder();
               $service->save();
            }
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         }
         
         $this->result = false;
         $this->message = 'No such setting';
         return false;
         
      }
      
   }


?>