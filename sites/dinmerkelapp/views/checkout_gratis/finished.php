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
         
         $this->setTemplate( 'checkout_gratis.complete' );
         
         if( isset( $orderid ) ) {
            UserSessionArray::clearItems( 'controlorderid' );
            
            $this->orderid = $orderid;
            
            Login::logout();
            
         }
         
      }
      
   }


?>