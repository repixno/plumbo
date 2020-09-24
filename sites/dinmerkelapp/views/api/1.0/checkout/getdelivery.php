<?php

   /**
    * API for getting order delivery methods 
    * and order payment methods based on country
    * to deliver to.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'pages.json' );
   config( 'website.countries' );
   config( 'website.stores' );
   
   class APIDeliveryMethods extends JSONPage implements NoAuthRequired, IValidatedView {
      
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'countryid' => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      public function Execute() {
         
         $cart = new Cart();
         $options = $cart->getDeliveryAndPaymentOptions();
         
         Util::Debug($options);
         exit;

      }
      
   }


?>