<?php

   /**
    *
    */

   import( 'website.cart' );
   import( 'finance.vipps.vipps' );
   
   class VippsPage extends WebPage implements IView {
      
      public function Execute( $orderid = 0 ) {
         
         $cart = new Cart();
         $cartArray = $cart->asArray();
         $vipps = new Vipps();        
         $relocateurl = $vipps->register( $cartArray );
        
         relocate( $relocateurl );
         exit;
         
      }
      
   }


?>