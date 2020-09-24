<?php

   /**
    * Class for sending SMS via intelesms
    * 
    * TODO:
    * We might want to think about saving messages to db
    * for later reference. Perhaps use a param in setup
    * to indicate wether to save to db or not?
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */


   config( 'common.sms' );
   library( 'pear.http.request' );
   
   class SMS {
      
      private $tonumber;
      private $message;
      private $fromnumber;
      private $customerid;
      private $messageid;
      private $gateway;
      private $price = 0;
      private $serviceurl;
      private $auth = array();
      private $result = array();
      
      
      /**
       * Send the SMS
       *
       * @param string $tonumber
       * @param string $msg
       * @param int $price 
       * @param string $msgid
       * @return array
       */
      public function send( $tonumber = null, $msg = null, $price = null, $msgid = null ) {
         
         // Override som defaults if set
         // rawurlencode
         if( isset( $tonumber ) ) $this->setToNumber( $tonumber );
         if( isset( $msg ) ) $this->setMessage( $msg );
         if( isset( $price ) ) $this->setPrice( $price );
         if( isset( $msgid ) ) $this->setMessageId( $msgid );
         
         // Setup from configs
         $this->setFromNumber( $this->getSender() );
         $this->setGateway( Settings::Get( 'sms', 'gateway' ) );
         $this->setCustomerId( Settings::Get( 'sms', 'customerid' ) );
         $this->setPassword( Settings::Get( 'sms', 'password' ) );
         $this->setServiceUrl( Settings::Get( 'sms', 'serviceurl' ) );
         
           
         // create a HTTP request and send it
         // Return the result of request to
         // initiator.
         return $this->makeRequest();
         
      }
      
      
      /**
       * Create a HTTP request and send it
       *
       * @return bool
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function makeRequest() {

         if( $this->initialized() ) {
            
            $request = new HTTP_Request( $this->serviceurl, array( 'timeout' => 15 ) );
            $request->sendRequest();
            $responsecode = $request->getResponseCode();
            $responsebody = $request->getResponseBody();
            $result = array();
            
            if( $responsecode == 200 ) {
               
               $xml = new SimpleXMLElement( $responsebody );
      
               $errorcode = $xml->status->error_code;
               $statuscode = $xml->status->error == 1 ? 0 : 1;
               
               $this->result['errorcode'] = $errorcode;
               $this->result['statuscode'] = $statuscode;
               
               return true;
               
            } else {
               
               $errorcode = 4200;
               $statuscode = 0;
      
               $this->result['errorcode'] = $errorcode;
               $this->result['statuscode'] = $statuscode;
               
               return false;
               
            }
         
         } else {
            
            $this->result['errorcode'] = 0;
            $this->result['statuscode'] = 0;
            return false;
            
         }
         
      }
      
      
      /**
       * All necessary settings ok?
       *
       * @return bool
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function initialized() {
         
         if( isset( $this->serviceurl ) && 
               isset( $this->gateway ) && 
               isset( $this->customerid ) && 
               isset( $this->tonumber ) && 
               isset( $this->message ) && 
               isset( $this->fromnumber ) ) {
                  return true;
         }
         
         util::debug( $this->serviceurl );
         
         return false;
      }
      
      /**
       * Set number to send to
       *
       * @param string $toNumber
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setToNumber( $toNumber ) {
         $this->tonumber = rawurlencode( $toNumber );
      }
      
      
      /**
       * Set message to send in SMS
       *
       * @param string $msg
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function setMessage( $msg ) {
         $this->message = rawurlencode( $msg );
      }
      
      
      /**
       * Set number/Text to be displayed
       * as the messasge is sent from
       *
       * @param string $fromNumber
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setFromNumber( $fromNumber ) {
         $this->fromnumber = rawurlencode( $fromNumber );
      }
      
      
      /**
       * Set the message to be sent
       *
       * @param string $id
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function setMessageId( $id ) {
         $this->messageid = $id;
      }
      
      
      /**
       * Set the gateway to use
       *
       * @param int $gateway
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function setGateway( $gateway ) {
         $this->gateway = $gateway;
      }
      
      
      /**
       * Set the price for the SMS
       * Value is in norwegian øre
       * 
       * @param int $price
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setPrice( $price ) {
         $this->price = round( $price * 100 );
      }
      
      
      /**
       * Set customer id
       *
       * @param int $id
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function setCustomerId( $id ) {
         $this->customerid = $id;
         $this->auth['customerid'] = $id;
      }
      
      
      /**
       * Set the password
       *
       * @param string $password
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function setPassword( $password ) {
         $this->password = $password;
         $this->auth['password'] = $password;
      }
      
      
      /**
       * Set the url used by service
       *
       * @param string $url
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function setServiceUrl( $url ) {
         $this->serviceurl = sprintf( 
            $url, $this->gateway, 
            $this->auth['customerid'], 
            $this->auth['password'], 
            $this->tonumber, 
            $this->fromnumber, 
            $this->price, 
            $this->messageid, 
            $this->message 
         );
      }
      
      
      /**
       * Return the actual result of the
       * SMS send request
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function getResult() {
         return $this->result;
      }
      
      
      
      /**
       * Get sender greeting
       * 
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      protected function getSender() {
         
         $senders = Settings::Get( 'sms', 'sender' );
         $portal = Dispatcher::getPortal();
         if( $portal == '' ) {
            $portal = 'EF-997';
         }
         
         return $senders[$portal];
         
      }
      
   }


?>