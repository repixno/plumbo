<?php

   import( 'pages.json' );
   import( 'website.cart' );

   class APISetItemQuantity extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(  
            "execute" => array(
               "post" => array(
                  "prodno" => VALIDATE_STRING,
                  "quantity" => VALIDATE_INTEGER,
                  "reference" => VALIDATE_INTEGER,
               ),
               "fields" => array(
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      
      /**
       * Set item quantity
       * 
       * @api-name cart.set
       * @api-auth required
       * @api-post-optional prodno String Product number
       * @api-param-optional prodno String Product number
       * @api-post-optional reference String Item reference id
       * @api-param-optional reference String Item reference id
       * @api-post-optional quantity Integer Number of items
       * @api-param-optional quantity Integer Number of items
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute( $prodno = '', $quantity = 0, $reference = '' ) {
         
         $this->result = false;
         $this->message = "Missing prodno";
         if( !isset( $prodno ) ) return false;
         
         $this->result = false;
         $this->message = "Quantity needs to be atleast 1";
         if( $quantity < 1 ) return false;
         
         $cart = new Cart();
         $cart->setItemQuantity( $prodno, $quantity, $reference );
         $cart->save();
         
         $this->result = true;
         $this->message = "OK";
         
         
      }
      
      
   }


?>