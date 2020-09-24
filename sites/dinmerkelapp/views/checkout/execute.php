<?php

   import( 'website.order.default' );
   import( 'finance.currency' );
   
   import( 'session.usersessionarray' );
   
   model( 'finance.transactionlog' );
   model( 'finance.transaction' );

   import( 'finance.klarna.klarnainvoice' );
   
   class Execute extends UserPage implements IView {
      
      protected $template = '';
      
      public function Execute() {
         //
         // Set the order comments here
         //

         $comment = addslashes( $_POST["comment"] );
         
         $cart = new Cart();
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
         // Execute the order
         try {

            $userorder = new UserOrder();
            $portal = Dispatcher::getPortal();
            $userorder->setComment( $cart->getComment() );
            if( $orderid = $userorder->executeOrder() ) {
               UserSessionArray::clearItems( 'purchased_cart' );
               UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
               $cartarray = $cart->asArray();
               
               if( isset($cartarray['klarnaid']) && $cartarray['klarnaid'] ){
                  $klarnarray = unserialize($cartarray['klarnaid']);
                  
                  if( $klarnarray['paymentmethod'] == 'KLARNAINV' ){
                     $klarna = new KlarnaInvoiceEF();
                     $klarna->updateOrder($orderid, $klarnarray['reference'] );
                  }
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
            file_put_contents( '/home/toringe/debug/' . date("Y-m-d_H:i:s") . ".txt", $message  );
            //mail( 'tor.inge@eurofoto.no' , "User Order Failed! Reason23: ", $message  );
            //$userorder->deleteOrder();
            die();

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