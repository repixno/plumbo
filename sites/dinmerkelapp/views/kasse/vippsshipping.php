<?php

   /**
    *
    */

   import( 'website.cart' );
   import( 'finance.vipps' );
   
   class VippsShippingPage extends WebPage implements IView {
      
      public function Execute( $orderid = 0 ) {
        
        exit;
        $cart = new Cart();
        $cartArray = $cart->asArray();
        $vipps = new Vipps();        
        $relocateurl = $vipps->payments( $cartArray );
        
        relocate( $relocateurl );
        exit;
         
      }
      
   }


?>