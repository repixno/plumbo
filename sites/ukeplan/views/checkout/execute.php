<?php

   import( 'website.order.default' );
   import( 'finance.nets.netaxept' );
   import( 'finance.currency' );
   
   import( 'session.usersessionarray' );
   
   model( 'finance.transactionlog' );
   model( 'finance.transaction' );

   define( 'NETAXEPT_TRANSACTION_OK', 'OK' );
   define( 'NETAXEPT_TRANSACTION_CANCELLED', 'Cancel' );
   define( 'NETAXEPT_TRANSACTION_FAILED', '99' );
   
   config( 'finance.creditcard' );
   config( 'website.stores' );
   
   class Execute extends UserPage implements IView {
      
      protected $template = '';
      
      public function Execute() {
         //
         // Set the order comments here
         //

         $comment = addslashes( $_POST["comment"] );
         
         $cart = new Cart();
         
         /*if( UserSessionArray::getItem( 'controlorderid', 0 ) ){
            relocate( '/checkout/finished/'. UserSessionArray::getItem( 'controlorderid', 0 ) );
            die();
         }*/
         
         if( strlen($comment) > 0 ){
            $cart->setComment($comment);
            $cart->save();
         }
         
         
         // If no deliverytype is given previously
         // then we need to update with one.
         // Needed by EF 2.5
         /*if( $cart->serviceProductsOnly() ) {
            $cart->setDeliveryType( 1 );
            $cart->save();
         }*/
         
         
         // Check if cart is empty
         if( $cart->getTotalItems() == 0 ) {
            
            relocate( '/cart' );
            
         }
         
         $price = $cart->getTotalPrice();
         
         $customer = $cart->getContactInfo();
         
         
         
                  
         if( $_POST['kilde'] && $_POST['kilde'] != 'null' && !isset( $_GET['orderid'] ) ){
            
            $user = new User( Login::userid() );
            $userinfo = $user->asArray();
            $cartarray =$cart->asArray();
            
            foreach( $cartarray['items'] as $key=>$eitem ){
               
               if( $key == "ukeplan" ){
                  foreach( $eitem as $uitem ){
                     foreach( $uitem as $fitem ){
                        //Util::Debug($fitem);
                        $itemarray .=  $fitem['quantity'] . " stk. "  . $fitem['product']['title'] . "\n" ;   
                     } 
                  }
               }
               else if( $key == 'goods'){
                  foreach( $eitem as $gitem ){
                     $itemarray .=  $gitem['quantity'] . " stk. "  . $gitem['product']['title'] . "\n" ;   
                  }
               }
            }
            
            $ebody = "Kilde: " . $_POST['kilde'] . "\n\n";
            $ebody .= "Kunde email: " . $userinfo['email'] . "\n";
            $ebody .= $itemarray;
            $ebody .= "\n\n" . print_r( $cart->getContactInfo() , true ); 
            
            mail( 'anne@ugeplan.dk', "Ugeplan Kilde", $ebody );
                        
         }

         if( $this->isCreditcardOrder( $cart ) && $price > 0 ) {
            
            if( !isset( $_GET['orderid'] ) ) {
               
               $currency = FinanceCurrency::getCurrency();
               
               // Live mode
               $mode = 1; 
               
               $orderid = DB::query( "SELECT nextval( 'ordrenr' )" )->fetchSingle();//sql_singleExec("SELECT nextval('ordrenr')", 0 );
               $transactionid = DB::query( "SELECT nextval('finance_bbs_transaction_id')" )->fetchSingle();
               
               try {
                  
                  $netaxept = new NetAxept( 1 );
                  $registerresult = $netaxept->register( $price, $currency, $orderid, $transactionid, $customer );
                  
               } catch( Exception $e ) {
                  
                  //util::Debug( $e->getMessage() );
                  util::debug( "Failed to register transaction" );
                  die();
                  
               }
               
               // Was this successful? Go to the terminal
               if( isset( $registerresult->TransactionId ) && strlen( $registerresult->TransactionId ) > 0 && $transactionid == $registerresult->TransactionId ) {
                  
                  UserSessionArray::addItem( 'netaxeptorderid', $orderid );
                  $netaxept->goToTerminal();
                  die();
                  
               } else {
                  
                  relocate( '/cart' );
                  die();
                  
               }
            
            } else { // Returned from Netaxept
               
               $orderid = UserSessionArray::getItem( 'netaxeptorderid', 0 );
               
               // If user does not have stored orderid. Relocate to cart
               if( !isset( $orderid ) ) relocate( '/cart' );
               
               UserSessionArray::clearItems( 'netaxeptorderid' );
               
               // Matching the stored orderid, correct order
               if( $orderid == $_GET['orderid'] ) {
               
                  $parameters = Settings::Get( 'finance', 'netsparameters' );
                  
                  $merchantid = $parameters['merchantid'];
                  $transactionid = $_GET['transactionId'];
                  $responsecodetext = $_GET['responseCode'];
                  $sessionid = $_GET['sessionid'];
                  $responsesource = null;
                  $responsetext = null;
                  $responsecode = null;
                  
                  $operation = 'Setup';
                  
                  // Live mode
                  $mode = 1;
                  
                  switch( $responsecodetext ) {
                     
                     case NETAXEPT_TRANSACTION_OK:
                        try {
                           
                           $this->logTransaction( $transactionid, $orderid, $mode, $price, $merchantid );
                           $this->logSetup( $transactionid, $price, $responsesource, $responsecode, $responsetext, $operation, $responsecodetext );
                           
                        } catch( Exception $e ) {
                           //util::debug( $e->getMessage() );
                           util::debug( "Failed to log or setup transaction" );
                        }
                        
                        $netaxept = new NetAxept( 1 );
                        
                        if( $authresponse = $netaxept->process( $transactionid ) ) {
                           
                           $responsecode = $authresponse->ResponseCode;
                           $responsesource = $authresponse->ResponseSource;
                           $responsetext = $authresponse->ResponseText;
                           $operation = $authresponse->Operation;
                           
                           $this->logProcess( $transactionid, $price, $responsesource, $responsecode, $responsetext, $operation, $responsecodetext );
                           
                           // The money is was reserved on card
                           if( $authresponse->ResponseCode == 'OK' ) {
                              
                              // Execute the order
                              try {
                                 
                                 $userorder = new UserOrder();
                                 
                                 $orgstoreid = $cart->getStore();
                                 if( !is_null( $orgstoreid ) ) {
                                    $storegroupdata = explode( ":", $orgstoreid );
                                    $storeid = end( $storegroupdata );
                                    $storegroup = reset( $storegroupdata );
                                    $stores = Settings::Get( 'storedelivery', $storegroup );
                                    $stores = $stores['stores'];
                                 }
                                 
                                 if( isset( $storeid ) ) {
                                    $store = $stores[$storeid];
                                    $storeaddress = $store['address'];
                                    $storename = $store['name'];
                                    $userorder->setComment( $cart->getComment()."\n\nSENDES TIL BUTIKK:\n".$storename."\n".$storeaddress );
                                    $userorder->setStoreDelivery( true, $storename, $storeaddress );
                                    
                                 } else {
                                    $userorder->setComment( $cart->getComment() );
                                 }

                                 if( $orderidresult = $userorder->executeOrder( $orderid ) ) {
                                    UserSessionArray::clearItems( 'purchased_cart' );
                                    UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
                                    relocate( '/checkout/finished/'.$orderid );
                                    die();
                                 
                                 } else {
                                    
                                    relocate( '/checkout/error' );
                                    die();
                                    
                                 }
                                 die();
                                 
                              } catch( Exception $e ) {
                                 
                                 //util::debug( 'User Order Failed!' );
                                 util::debug( 'User Order Failed3! Reason: '.$e->getMessage() );
                                 $message =  $e->getMessage() . "\n" . $this->debugging( $cart );
                                 $message .= $this->debugging( $e );
                                 
                                 mail( 'tor.inge@eurofoto.no' , "User Order Failed! Creditcard Reason3: ", $message  );
                                 //$userorder->deleteOrder();
                                 die();
                                 
                              }
                              
                           } else {
                              
                              $queryresult = $netaxept->query( $transactionid );
                              $responsecode = $queryresult->Error->ResponseCode;
                              $responsesource = $queryresult->Error->ResponseSource;
                              $responsetext = $queryresult->Error->ResponseText;
                              $this->logSetup( $transactionid, $price, $responsesource, $responsecode, $responsetext, $operation, $responsecode );
                              relocate( '/checkout/error?error=1' );
                              die();
                              
                           }
                           
                        } else {

                           $tmpresponse = $netaxept->query( $transactionid );
                           $responsecode = $tmpresponse->Error->ResponseCode;
                           $responsesource = $tmpresponse->Error->ResponseSource;
                           $responsetext = $tmpresponse->Error->ResponseText;
                           $operation = $tmpresponse->Error->Operation;
                           
                           try {
                              
                              $this->logProcess( $transactionid, $price, $responsesource, $responsecode, $responsetext, $operation, $responsecodetext );
                              
                           } catch( Exception $e ) {
                              
                              //util::Debug( $e->getMessage() );
                              util::debug( "Failed to log process" );
                              
                           }
                           
                           relocate( '/checkout/error?error=1' );
                           die();
                           
                        }
                        //$this->logProcess( $transactionid, $orderid, $mode, $amount, $merchantid, $responsesource, $responsecode, $responsetext );
                        break;
                     case NETAXEPT_TRANSACTION_CANCELLED:
                        $netaxept = new NetAxept( 1 );
                        $queryresult = $netaxept->query( $transactionid );
                        $responsecode = $queryresult->Error->ResponseCode;
                        $responsesource = $queryresult->Error->ResponseSource;
                        $responsetext = $queryresult->Error->ResponseText;
                        $this->logTransaction( $transactionid, $orderid, $mode, $price, $merchantid );
                        $this->logSetup( $transactionid, $price, $responsesource, $responsecode, $responsetext, $operation, $responsecode );
                        relocate( '/checkout/error?error=2' );
                        die();
                        break;
                     case NETAXEPT_TRANSACTION_FAILED:
                        $netaxept = new NetAxept( 1 );
                        $queryresult = $netaxept->query( $transactionid );
                        $responsecode = $queryresult->Error->ResponseCode;
                        $responsesource = $queryresult->Error->ResponseSource;
                        $responsetext = $queryresult->Error->ResponseText;
                        $this->logTransaction( $transactionid, $orderid, $mode, $price, $merchantid );
                        $this->logSetup( $transactionid, $price, $responsesource, $responsecode, $responsetext, $operation, $responsecode );
                        relocate( '/checkout/error?error=1' );
                        die();
                        break;
                     default:
                        relocate( '/checkout/error' );
                        die();
                        break;
                     
                  }
                  
                  
               } else {
                  
                  relocate( '/cart' );
                  die();
                  
               }
               
               die();
               
            }
            
         } else {
            
            // Execute the order
            try {

               $userorder = new UserOrder();
               $portal = Dispatcher::getPortal();
               if( is_null($portal) || $portal == "" ) {
                  $portal = 'EF-997';
               }
               
               $orgstoreid = $cart->getStore();
               if( !is_null( $orgstoreid ) ) {
                  $storegroupdata = explode( ":", $orgstoreid );
                  $storeid = end( $storegroupdata );
                  $storegroup = reset( $storegroupdata );
                  $stores = Settings::Get( 'storedelivery', $storegroup );
                  $stores = $stores['stores'];
               }
               
               if( isset( $storeid ) ) {
                  $store = $stores[$storeid];
                  $storeaddress = $store['address'];
                  $storename = $store['name'];
                  $userorder->setComment( $cart->getComment()."\n\nSENDES TIL BUTIKK:\n".$storename."\n".$storeaddress );
                  $userorder->setStoreDelivery( true, $storename, $storeaddress );
                  
               } else {
                  $userorder->setComment( $cart->getComment() );
               }
               
               //$userorder->setComment( $cart->getComment() );
               
               if( $orderid = $userorder->executeOrder() ) {
                  UserSessionArray::clearItems( 'purchased_cart' );
                  UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
                  relocate( '/checkout/finished/'.$orderid );

               } else {
                  
                  relocate( '/checkout/error' );

               }
               
               die();

            } catch( Exception $e ) {

               //util::debug( "User order failed" );
               
               util::debug( 'User Order Failed! Reason23: '.$e->getMessage() );
               
               
               $message =  $e->getMessage() . "\n" . $this->debugging( $cart );
               $message .= $this->debugging( $e );
               
               mail( 'tor.inge@eurofoto.no' , "User Order Failed! Reason23: ", $message  );
               //$userorder->deleteOrder();
               die();

            }
            
         }
         
      }
      
      private function debugging(){
         
         
         $args = func_get_args();
         
         foreach( $args as $argument ) {
            
            $ret .= "<pre>".print_r( $argument, true )."</pre>\n";
         
         
         }
      
         return $ret;
         
      }
      
      /**
       * Log the registration setup to db
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       * @param integer $transactionid
       * @param integer $orderid
       * @param integer $mode
       * @param float $amount
       * @param string $merchantid
       */
      private function logTransaction( $transactionid, $orderid, $mode, $price, $merchantid ) {
   
         $transaction = new DBFinanceTransaction();
         $transaction->objectid = $transactionid;
         $transaction->orderid = $orderid;
         $transaction->mode = $mode;
         $transaction->amount = $price;
         $transaction->merchantid = $merchantid;
         $transaction->save();
      
      }
      
      

      /**
       * Log the transaction to db log
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param integer $transactionid
       * @param float $price
       * @param string $source
       * @param string $code
       * @param string $text
       * @param string $operation
       */
      private function logProcess( $transactionid, $price, $source, $code, $text, $operation, $responsecodetext ) {
         
         $transactionlog = new DBFinanceTransactionLog();
         $transactionlog->transactionid = $transactionid;
         $transactionlog->operation = strtolower( $operation );
         $transactionlog->amount = $price;
         $transactionlog->responsesource = $source;
         $transactionlog->responsecode = $code;
         $transactionlog->responsetext = $text;
         $transactionlog->save();
         
      }
      
      
      /**
       * Log the transaction to db log
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @param integer $transactionid
       * @param float $price
       * @param string $source
       * @param string $code
       * @param string $text
       * @param string $operation
       */
      private function logSetup( $transactionid, $price, $source, $code, $text, $operation, $responsecodetext ) {
         
         $transactionlog = new DBFinanceTransactionLog();
         $transactionlog->transactionid = $transactionid;
         $transactionlog->operation = strtolower( $operation );
         $transactionlog->amount = $price;
         $transactionlog->responsesource = $source;
         $transactionlog->responsecode = $responsecodetext;
         $transactionlog->responsetext = $text;
         $transactionlog->save();
         
      }
      
      
      /**
       * Has the user chosen to pay with a creditcard?
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       * @return boolean
       */
      private function isCreditcardOrder( $cart ) {
         
         $paymenttype = $cart->getPaymentType();
         $paymenttypeartnr = $paymenttype['artnr'];
         
         $creditcardartnr = Settings::Get( 'creditcard', 'artnr' );
         
         if( $creditcardartnr == $paymenttypeartnr ) return true;
         
         return false;
         
      }
      
   }


?>