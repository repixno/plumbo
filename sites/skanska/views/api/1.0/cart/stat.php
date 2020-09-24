<?php

   /**
    * Return json contents of user cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'pages.json' );
   import( 'website.cart' );
   
   class APICartStat extends JSONPage implements IView, NoAuthRequired {

      /**
       * Content of user cart
       * 
       * @api-name cart.stat
       * @api-auth required
       * @api-result totalprice The total price of the cart in NOK
       * @api-result totalitems The total number of items in the cart
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {
       
         try {
         	
         	$cart = new Cart();
            
         	$this->result = true;
            $this->message = "OK";
            
            $this->totalprice = $cart->getTotalPrice();
            $this->totalitems = $cart->getTotalItems();
            
         } catch( Exception $e ) {
            
         	$this->result = true;
            $this->message = "OK";
            
            $this->totalprice = 0;
            $this->totalitems = 0;
            
         }
         
      }
      
   }
   
?>