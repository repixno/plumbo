<?php

   /**
    * Order is finished. Display order id 
    * and other necessary information to customer.
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.cart' );
   
   class CheckoutFinished extends WebPage implements IView {
      
      public function Execute( $orderid = 0 ) {
         
         $cart = new Cart();
         $cart->clear();
         
         $this->setTemplate( 'order.complete' );
         
         if( isset( $orderid ) ) {
            UserSessionArray::clearItems( 'controlorderid' );
            
            $this->orderid = $orderid;
            $purchasedCart = UserSessionArray::getItem( 'purchased_cart', 0 );
            $this->purchasedcart = $purchasedCart;
            
            $today = date( 'Y-m-d' );
            
         }
         
      }
      
   }


?>