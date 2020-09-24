<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   config( 'finance.nets.parameters' );
   
   import( 'finance.nets.order' );
   import( 'finance.nets.environment' );
   import( 'finance.nets.terminal' );
   import( 'finance.nets.customer' );
   import( 'finance.nets.registerrequest' );
   import( 'finance.nets.query' );
   import( 'finance.nets.process' );
   import( 'finance.nets.dnbnordirectpayment' );
   
   define( 'NETAXEPT_TEST_MODE', 0 );
   define( 'NETAXEPT_LIVE_MODE', 1 );
   
   class NetAxept {
      
      private $merchantid;
      private $token;
      private $wsdl;
      private $client;
      private $redirecturl;
      
      // Register properties
      private $amount;
      private $currencycode;  //The currency code, following ISO 4217. Typical examples include "NOK" and "USD".
      private $ordernumber;
      
      // Enviroment
      private $language = null;
      private $os = null;
      private $webserviceplatform = 'PHP5';

      // Terminal
      private $autoauth = null;
      private $paymentmethodlist = 'Visa,MasterCard'; //'Visa,MasterCard,Maestro,BankAxess,DnBNorDirectPayment';
      private $terminallanguage = null;
      private $orderdescription = null;
      private $redirectonerror = null;
      
      // RegisterRequest
      private $avtalegiro = null;
      private $cardinfo = null;
      private $customer = null;
      private $description = null;
      private $dnbnordirectpayment = null;
      private $enviroment = null;
      private $micropayment = null;
      private $servicetype = null;
      private $recurring = null;
      private $transactionid = null;
      private $transactionreconref = null;
      
      private $arrayofitem = null;
      
      public function __construct( $mode = NETAXEPT_TEST_MODE ) {
         
         $parameters = Settings::Get( 'finance', 'netsparameters' );
         
         // Get parameters depending on mode given
         switch( $mode ) {

            default:
            case NEXTAXEPT_TEST_MODE:
               $this->merchantid = $parameters['testmerchantid'];
               $this->token = $parameters['testtoken'];
               $this->wsdl = $parameters['testwsdl'];
               $this->terminal = $parameters['testterminal'];
               $this->redirecturl = $parameters['redirecturl'];
               $this->dnbkonto = $parameters['testdnbkonto'];
               break;
               
            case NETAXEPT_LIVE_MODE:
               $this->merchantid = $parameters['merchantid'];
               $this->token = $parameters['token'];
               $this->wsdl = $parameters['wsdl'];
               $this->terminal = $parameters['terminal'];
               $this->redirecturl = $parameters['redirecturl'];
               $this->dnbkonto = $parameters['dnbkonto'];
               break;
            
         }
         // Creating new client without proxy
         $this->client = new SoapClient( $this->wsdl, array( 'trace' => true, 'exceptions' => true ) );
         
      }
      
      
      /**
       * Register a transaction with Netaxept
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param float $amount
       * @param string $currency
       * @param integer $ordernumber
       * @param integer $transactionid
       * @return RegisterResult object or boolean false
       */
      public function register( $amount = null, $currency = null, $ordernumber = null, $transactionid = null, $contactinfo = array() ) {
         
         $this->amount = $amount * 100;
         $this->currencycode = $currency;
         $this->ordernumber = $ordernumber;
         $this->transactionid = $transactionid;
         $this->customer = $contactinfo;
         $this->redirecturl = $this->redirecturl."?orderid=".$ordernumber;
         //$this->force3dsecure = false;
         
         if( Dispatcher::getPortal() == 'VP-001'){
            $this->language = 'sv_SE';
	    $this->currencycode = "SEK";
         }
         if( Dispatcher::getPortal() == 'STU-SV'){
            $this->language = 'sv_SE';
            $this->currencycode = "SEK"; 
         }
         if( Dispatcher::getPortal() == 'DM-SV'){
            $this->language = 'sv_SE';
            $this->currencycode = "SEK"; 
         }
         if( Dispatcher::getPortal() == 'UP-DK'){
            $this->language = 'da_DK';
            $this->paymentmethodlist = "Dankort";
            $this->currencycode = "DKK"; 
         }
         
         $this->terminallanguage = $this->language;
         
        
         
         $enviroment = new Environment( $this->language, $this->os, $this->webserviceplatform );
         $terminal = new Terminal( $this->autoauth, $this->paymentmethodlist, $this->terminallanguage, $this->orderdescription, $this->redirectonerror, $this->redirecturl );
         $order = new NetAxeptOrder( $this->amount, $this->currencycode, $this->force3dsecure, $this->arrayofitem, $this->ordernumber, $this->updatestoredpaymentinfo );
         $dnbnordirectpayment = new DnBNorDirectPayment( null, null, $this->dnbkonto );
         $customer = new Customer( 
            $this->customer['address'], 
            null, 
            null, 
            null, 
            null, 
            null, 
            null, 
            $this->customer['firstname'],
            $this->customer['lastname'], 
            null, 
            $this->customer['zipcode'], 
            null, 
            $this->customer['city'] 
         );
         
         $registerrequest = new RegisterRequest( 
            $this->avtalegiro, 
            $this->cardinfo, 
            $customer,
            $this->description, 
            $dnbnordirectpayment,
            $enviroment,
            $this->micropayment,
            $order,
            $this->recurring,
            $this->servicetype,
            $terminal,
            $this->transactionid,
            $this->transactionreconref 
         );
         
         try {
            $requestresponse = $this->client->__call( 'Register' , array( 'parameters' => array( 'token' => $this->token, 'merchantId' => $this->merchantid, 'request' => $registerrequest ) ) );
            return $requestresponse->RegisterResult;
            
         } catch ( Exception $e ) {
            util::Debug( $e->getMessage() );
            die();
            return false;
            
         } 
         
         
         
      }
      
      
      /**
       * Query for the error log
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param integer $transactionid
       * @return object QueryResult
       */
      public function query( $transactionid = null ) {
         
         $this->transactionid = $transactionid;
         $queryrequest = new QueryRequest( $this->transactionid );
         
         try {
            
            $queryresponse = $this->client->__call( 'Query' , array( 'parameters' => array( 'token' => $this->token, 'merchantId' => $this->merchantid, 'request' => $queryrequest ) ) );
            return $queryresponse->QueryResult;
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
      
      /**
       * Register the transaction reserving the amount
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       * @param integer $transactionid
       * @return ProcessResult object or boolean false
       */
      public function process( $transactionid = null ) {
         
         $this->transactionid = $transactionid;
         
         $processrequest = new ProcessRequest( 'Reserving amount', 'AUTH', null, $this->transactionid, null );
         
         try {

            $processresponse = $this->client->__call( 'Process' , array( 'parameters' => array( 'token' => $this->token, 'merchantId' => $this->merchantid, 'request' => $processrequest ) ) );
            return $processresponse->ProcessResult;
            
         } catch( Exception $e ) {

            return false;
            
         }

      }
      
      
      /**
       * Set the enviroment variables as necessary
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param string $language
       * @param string $os
       * @param string $webserviceplatform
       */
      public function setEnviroment( $language = null, $os = null, $webserviceplatform = 'PHP5' ) {
         
         $this->language = $language;
         $this->os = $os;
         $this->webserviceplatform = $webserviceplatform;
         
      }
      
      
      /**
       * Set the terminal variables as necessary
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param string $autoauth
       * @param string $paymentmethodlist
       * @param string $language
       * @param string $orderdescription
       * @param string $redirectonerror
       * @param string $redirecturl
       */
      public function setTerminal( $autoauth = null, $paymentmethodlist = null, $language = null, $orderdescription = null, $redirectonerror = null, $redirecturl = null ) {
         
         $this->autoauth = $autoauth;
         $this->paymentmethodlist = $paymentmethodlist;
         $this->terminallanguage = $language;
         $this->orderdescription = $orderdescription;
         $this->redirectonerror = $redirectonerror;
         $this->redirecturl = $redirecturl;
         
      }
      
      
      /**
       * Set the orderdata if/as necessary
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param float $amount
       * @param string $currency
       * @param integer $ordernumber
       * @param integer $transactionid
       */
      public function setOrderData( $amount = null, $currency = null, $ordernumber = null, $transactionid = null ) {
         
         $this->amount = $amount * 100;
         $this->currencycode = $currency;
         $this->ordernumber = $ordernumber;
         $this->transactionid = $transactionid;
         
      }
      
      
      /**
       * Relocate webbrowser to the netaxept terminal
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function goToTerminal() {
         
         relocate( $this->terminal."?merchantId=".$this->merchantid."&transactionId=".$this->transactionid );
         
      }
      
      
      /**
       * Get the merchantid
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @return string
       */
      public function getMerchantId() {
         
         return $this->merchantid;
         
      }
      
   }


?>
