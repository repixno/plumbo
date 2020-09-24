<?php

   /**
    * Return json contents of user cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'pages.json' );
   import( 'website.cart' );
   
   class APICartDefault extends JSONPage implements IView, NoAuthRequired {
      
      /**
       * Returns content of user cart
       * 
       * @api-name cart
       * @api-auth required
       * @api-result items Array Items in user's cart
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute() {
       
         if( Cart::count() > 0 ) {

            $this->result = true;
            $this->message = "OK";
            $this->items = Cart::enumItems();
            
         } else {
            
            $this->result = false;
            $this->message = "Cart is empty";
            return false;
            
         }
         
      }
      
   }
   
?>