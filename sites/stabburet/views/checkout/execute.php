<?php

   import( 'website.order.default' );

   import( 'session.usersessionarray' );
   
   config( 'website.stores' );
   

   import( 'finance.klarna.klarna' );
   
   class Execute extends UserPage implements IView {
      

      protected $template = 'checkout.execute';
      
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
         
         // Check if cart is empty
         if( $cart->getTotalItems() == 0 ) {
            relocate( '/cart' );
         }
         
         $price = $cart->getTotalPrice();
         $customer = $cart->getContactInfo();
         
         if(  $price > 0 ) {
            $cartArray = $cart->asArray();
            if( $cartArray['totalprice'] > 0 ){
               try{
                  $klarna = new KlarnaEF();
                  $klarna->register($cart, $cartArray['deliverytype'] );
                  $this->snippet = $klarna->snippet;
               }Catch( Exception $e) {
                  //location.reload();
                  Util::debug( $e );
                  exit;
               }
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
                  
                  $cartarray = $cart->asArray();
                  
                  $klarnarray = unserialize($cartarray['klarnaid']);                  
                  
                  if( $klarnarray['paymentmethod'] == 'KLARNAINV' ){
                     $klarna = new KlarnaInvoiceEF();
                     $klarna->updateOrder($orderid, $klarnarray['reference'] );
                  }
                  
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
               
               //mail( 'tor.inge@eurofoto.no' , "User Order Failed! Reason23: ", $message  );
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