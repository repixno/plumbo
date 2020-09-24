<?php

   /**
    * Class for sending SMS when a user order is sent
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'sms.send' );
   model( 'site.sms' );
   model( 'user.smsservices' );

   class SMSOrderSent extends SMS {
      
      private $ordernr;
      private $ending;
      
      
      /**
       * Set the number to send SMS to on construction
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function __construct( $ordernr = null ) {
         
         $service = DBSmsServices::fromUserid( Login::userid() );
         if( $service instanceof DBSmsServices || !$service->isLoaded ) {
            $this->setToNumber( $service->cellnr );
            if( isset( $ordernr ) ) {
               
               $this->ordernr = $ordernr;
               $this->ending = 'Eurofoto';
         
               $message = new DBSiteSMS( 2 ); // Message order_sent_notice
               $this->setMessage( __( $message->message, $this->ordernr, $this->ending ) );
               $this->updateMessageId(); // Draw new message id
            }
            
         }
         
      }
      
      
      /**
       * Set the ordernr to display in SMS
       *
       * @param integer $ordernr
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function setOrderNr( $ordernr ) {
         
         $this->ordernr = $ordernr;
         $this->ending = 'Eurofoto';
         
         $message = new DBSiteSMS( 2 ); // Message order_sent_notice
         $this->setMessage( __( $message->message, $this->ordernr, $this->ending ) );
         $this->updateMessageId(); // Draw new message id
         
      }
      
      
      /**
       * Get a new id to use as messageid
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      private function updateMessageId() {
         
         $res = DB::query( "SELECT nextval('user_sms_services_history_id_seq')" );
         list( $messageid ) = $res->fetchRow();
         $this->setMessageId( $messageid );
         
      }
      
      
      /**
       * Check if a user has agreed to receive notices
       * of type order_sent_notice
       *
       * @return bool
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      static function isRecipient() {
         
         $service = DBSmsServices::fromUserid( Login::userid() );
         if( $service instanceof DBSmsServices || !$service->isLoaded ) {
            if( !is_null( $service->validated ) && !is_null( $service->order_sent_notice ) ) {
                  return true;
            }
         }
         
         return false;
         
      }
      
   }


?>