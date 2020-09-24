<?php
// vim600: sw=3 ts=3 tw=78 fdm=marker
// +----------------------------------------------------------------------+
// | PHP Version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2008 �yvind Selbek                                     |
// +----------------------------------------------------------------------+
// | Author: �yvind Selbek <oyvind@selbek.com>                            |
// +----------------------------------------------------------------------+
// | THIS SOFTWARE PROGRAM IS UNDER COMERCIAL LICENSE AND SHALL NOT UNDER |
// | ANY CIRCUMSTANCES BE REDISTRIBUTED WITHOUT THE WRITTEN PERMISSION OF |
// | THE SOFTWARE AUTHOR AND COPYRIGHT HOLDERS(s) GIVEN ABOVE.            |
// +----------------------------------------------------------------------+
   
   require_once( 'SetupRequestClass.php' );

   define( 'BBSNETTERMINAL_MODE_TEST', 0x00 );
   define( 'BBSNETTERMINAL_MODE_PROD', 0x01 );
   
   define( 'BBSNETTERMINAL_TRANSACTION_OK',        'OK' );
   define( 'BBSNETTERMINAL_TRANSACTION_CANCELLED', '17' );
   
   if( !function_exists( 'getWSDLRootPath' ) ) {
      function getWSDLRootPath() {
         return dirname( __FILE__ );
      }
   }
   
   class BBSNetTerminal {
      
      private $wsdl;
      private $action;
      private $client;
      
      private $token = '';
      private $merchantid = 0;
      
      public function getAction() {
         
         return $this->action;
         
      }
      
      public function __construct( $merchantid, $token, $mode = BBSNETTERMINAL_MODE_TEST ) {
         
         switch( $mode ) {
            
            default:
            case BBSNETTERMINAL_MODE_TEST:
               $this->wsdl = sprintf( '%s/wsdl/bbsnorge/bbstest.wsdl', getWSDLRootPath() );
               $this->action = 'https://epay.bbs.no:9443/cgi/epay.pway';
               break;
            case BBSNETTERMINAL_MODE_PROD:
               $this->wsdl = sprintf( '%s/wsdl/bbsnorge/bbsprod.wsdl', getWSDLRootPath() );
               $this->action = 'https://epay.bbs.no:443/cgi/epay.pway';
               break;
            
         }
         
         // create the soapclient
         $this->client = new SoapClient( $this->wsdl, array( 'trace' => true, 'exceptions' => true ) );
         
         // store internal values
         $this->merchantid = $merchantid;
         $this->token = $token;
         
      }
      
      public function kickoff( SetupRequest $parameters ) {
         
         $params = array( 
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'request' => $parameters,
         );

         
         
         $response = $this->client->__call( 'Setup', array( 'parameters' => $params ) );
         return $response->SetupResult;
         
      }
      
      public function ProcessSetup( $transactionstring ) {
         
         $params = array(
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'transactionString' => $transactionstring,
         );
         
         try {
            
            $response = $this->client->__call( 'ProcessSetup', array( 'parameters' => $params ) );
            
            return array( 
               $response->ProcessSetupResult->TransactionId,
               $response->ProcessSetupResult->ResponseSource,
               $response->ProcessSetupResult->ResponseCode,
               $response->ProcessSetupResult->ResponseText,
               (int) $response->ProcessSetupResult->IssuerId
            );
            
         } catch( SoapFault $e ) {
            
            $exceptiontype = key( $e->detail );
            $exceptionobj = $e->detail->$exceptiontype;
            
            return array( 
               $exceptionobj->Result->TransactionId,
               $exceptionobj->Result->ResponseSource,
               $exceptionobj->Result->ResponseCode,
               $exceptionobj->Result->ResponseText,
               (int) $exceptionobj->Result->IssuerId
            );
            
         }
         
      }
      
      public function auth( $transactionid, $batchReconRef = null ) {
         
         $params = array(
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'transactionId' => $transactionid,
            'batchReconRef' => $batchReconRef,
         );
         
         try {
            
            $response = $this->client->__call( 'Auth', array( 'parameters' => $params ) );
            
            return array( 
               $response->AuthResult->TransactionId,
               $response->AuthResult->ResponseSource,
               $response->AuthResult->ResponseCode,
               $response->AuthResult->ResponseText,
            );
            
         } catch( SoapFault $e ) {
            
            $exceptiontype = key( $e->detail );
            $exceptionobj = $e->detail->$exceptiontype;
            
            return array( 
               $exceptionobj->Result->TransactionId,
               $exceptionobj->Result->ResponseSource,
               $exceptionobj->Result->ResponseCode,
               $exceptionobj->Result->ResponseText,
            );
            
         }
         
      }
      
      public function sale( $transactionid, $batchReconRef = null ) {
         
         $params = array(
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'transactionId' => $transactionid,
            'batchReconRef' => $batchReconRef,
         );
         
         try {
            
            $response = $this->client->__call( 'Sale', array( 'parameters' => $params ) );
            
            return array( 
               $response->SaleResult->TransactionId,
               $response->SaleResult->ResponseSource,
               $response->SaleResult->ResponseCode,
               $response->SaleResult->ResponseText,
            );
            
         } catch( SoapFault $e ) {
            
            $exceptiontype = key( $e->detail );
            $exceptionobj = $e->detail->$exceptiontype;
            
            return array( 
               $exceptionobj->Result->TransactionId,
               $exceptionobj->Result->ResponseSource,
               $exceptionobj->Result->ResponseCode,
               $exceptionobj->Result->ResponseText,
            );
            
         }
         
      }
      
      public function capture( $transactionid, $amount, $batchReconRef = null ) {
         
         $params = array(
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'description' => 'Capture',
            'transactionId' => $transactionid,
            'transactionAmount' => round( $amount * 100 ),
            'batchReconRef' => $batchReconRef,
         );
         
         try {
            
            $response = $this->client->__call( 'Capture', array( 'parameters' => $params ) );
            
            return array( 
               $response->CaptureResult->TransactionId,
               $response->CaptureResult->ResponseSource,
               $response->CaptureResult->ResponseCode,
               $response->CaptureResult->ResponseText,
            );
            
         } catch( SoapFault $e ) {
            
            $exceptiontype = key( $e->detail );
            $exceptionobj = $e->detail->$exceptiontype;
            
            return array( 
               $exceptionobj->Result->TransactionId,
               $exceptionobj->Result->ResponseSource,
               $exceptionobj->Result->ResponseCode,
               $exceptionobj->Result->ResponseText,
            );
            
         }
         
      }
      
      public function cancel( $transactionid, $amount, $batchReconRef = null ) {
         
         $params = array(
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'description' => 'Capture',
            'transactionId' => $transactionid,
            'transactionAmount' => round( $amount * 100 ),
            'batchReconRef' => $batchReconRef,
         );
         
         try {
            
            $response = $this->client->__call( 'Annul', array( 'parameters' => $params ) );
            
            return array( 
               $response->AnnulResult->TransactionId,
               $response->AnnulResult->ResponseSource,
               $response->AnnulResult->ResponseCode,
               $response->AnnulResult->ResponseText,
            );
            
         } catch( SoapFault $e ) {
            
            $exceptiontype = key( $e->detail );
            $exceptionobj = $e->detail->$exceptiontype;
            
            return array( 
               $exceptionobj->Result->TransactionId,
               $exceptionobj->Result->ResponseSource,
               $exceptionobj->Result->ResponseCode,
               $exceptionobj->Result->ResponseText,
            );
            
         }
         
      }
      
      public function credit( $transactionid, $amount, $batchReconRef = null ) {
         
         $params = array(
            'token' => $this->token, 
            'merchantId' => $this->merchantid,
            'description' => 'Credit',
            'transactionId' => $transactionid,
            'transactionAmount' => round( $amount * 100 ),
            'batchReconRef' => $batchReconRef,
         );
         
         try {
            
            $response = $this->client->__call( 'Credit', array( 'parameters' => $params ) );
            
            return array( 
               $response->CreditResult->TransactionId,
               $response->CreditResult->ResponseSource,
               $response->CreditResult->ResponseCode,
               $response->CreditResult->ResponseText,
            );
            
         } catch( SoapFault $e ) {
            
            $exceptiontype = key( $e->detail );
            $exceptionobj = $e->detail->$exceptiontype;
            
            return array( 
               $exceptionobj->Result->TransactionId,
               $exceptionobj->Result->ResponseSource,
               $exceptionobj->Result->ResponseCode,
               $exceptionobj->Result->ResponseText,
            );
            
         }
         
      }
      
   }
   
?>