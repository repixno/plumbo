<?php

   /**
    * Checkout complete
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'website.cart' );

   class CheckoutConfirm extends WebPage implements IView {
      
      protected $template = 'checkout.confirm';
      
      public function Execute() {

         $cart = new Cart();
         
         if( !count( $cart->enum() ) ) relocate( '/cart' );
         
         //$this->cart = $cart->enum();
         
         if( isset( $_POST["payment-method"] ) ) {
            
            $paymentmethod = $_POST["payment-method"];
            if( !$cart->setPaymentType( $paymentmethod ) ) {
               relocate( "/checkout/payment-method" );
            } else {
               $cart->save();
            }
            
         } else {
            $paymentmethod = $cart->getPaymentType();
         }

         if( !isset( $paymentmethod ) && !$paymentmethod > 0 ) {
            relocate( '/checkout/payment-method' );
         }
         
         $this->cart = $cart->enum();
         
         
      }
      
   }


?>