<?php

   /**
    * Return json contents of user cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'pages.json' );
   import( 'website.cart' );
   
   class APICartEnum extends JSONPage implements IView, NoAuthRequired {

      /**
       * Content of user cart
       * 
       * @api-name cart.enum
       * @api-auth required
       * @api-result items Array Items in user's cart
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {
       
         $cart = new Cart();
         $items = $cart->enumItems();
         
         if( count( $items ) > 0 ) {
            $this->result = true;
            $this->message = "OK";
            $this->items = $cart->enumItems();
            $this->totalprice = $cart->getTotalPrice();
            $this->totalitems = $cart->getTotalItems();
         } else {
            $this->result = false;
            $this->message = "Cart is empty";
         }
         
      }
      
   }
   
?>