<?php

   /**
    * Order is finished. Display order id 
    * and other necessary information to customer.
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.cart' );
   import( 'finance.klarna.klarna' );
   
   class CheckoutFinished extends WebPage implements IView {
      
      public function Execute( $orderid = 0 ) {
         
         $cart = new Cart();
         $cart->clear();
         
         $this->setTemplate( 'kasse.complete' );
         
         $klarna = new KlarnaEF();
         $klarna->fetch();
         $this->snippet = $klarna->snippet;
         
         if( isset( $orderid ) ) {
            UserSessionArray::clearItems( 'controlorderid' );
            
            $this->orderid = $orderid;
            $purchasedCart = UserSessionArray::getItem( 'purchased_cart', 0 );
            $this->purchasedcart = $purchasedCart;
            
            if( isset( $purchasedCart['giftcard'] ) && count( $purchasedCart['giftcard'] ) ) {
               $giftcardid = $purchasedCart['giftcard']['giftcardid'];
               if( isset( $giftcardid ) && $giftcardid > 0 ) {
                  
                  try {
                     
                     $giftcard = new Giftcard( $giftcardid );
                     $newvalue = $giftcard->value - $purchasedCart['giftcard']['usedvalue'];
                     $giftcard->value = $newvalue;
                     $giftcard->changed = date( 'Y-m-d H:i:s' );
                     
                     $orderids = $giftcard->usedorderid;
                     if( isset( $orderids ) ) {
                        $orderids = unserialize( $orderids );
                     }
                     
                     if( is_array( $orderids ) ) {
                        $orderids []= $orderid;
                     } else {
                        $orderids = array();
                        $orderids []= $orderid;
                     }
                     
                     $orderids = serialize( $orderids );
                     $giftcard->usedorderid = $orderids;
                     
                     $giftcard->save();
                     
                  } catch( Exception $e ) {
                     
                  }
                  
               }
               
            }
            
            // Is this a giftcard purchase?
            if( DB::query( "SELECT count(*) FROM giftcard WHERE orderid = ? AND buyerid = ?", $orderid, Login::userid() )->fetchSingle() > 0 ) {
               $this->giftcard = true;
            }
            
            if( Session::get('utm_source') == "kelkoono" ){
               //Change to information provided by TradeDoubler/Kelkoo
               $this->kelkoo = array("org_id" => 1542373,
                                     "orderValue" => $purchasedCart['totalprice'],
                                     "orderNumber" => $orderid,
                                     "event" => 213844,
                                     "currency" => "NOK"
                                     );
            }

            
            
         }
         
      }
      
   }


?>