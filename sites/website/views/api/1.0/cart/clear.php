<?php

   /**
    * Empty the user cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */
   
   import( 'pages.json' );
   import( 'website.cart' );

   class APICartClear extends JSONPage implements NoAuthRequired, IView {

      /**
       * Empty the user's cart
       * 
       * @api-name cart.clear
       * @api-auth required
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         $cart = new Cart();
         $cart->clear();
         $cart->save();
         
         $this->result = true;
         $this->message = "OK";
         return true;
         
      }
      
      
   }


?>