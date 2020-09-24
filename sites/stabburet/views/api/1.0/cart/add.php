<?php

   /**
    * API to add different types of products to EF cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'pages.json' );
   import( 'website.cart' );
   
   class APICartAdd extends JSONPage implements NoAuthRequired, IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'quantity' => VALIDATE_INTEGER,
                  'attributes' => VALIDATE_ARRAY,
               ),
               'get' => array(
                  'prodno' => VALIDATE_STRING,
                  'quantity' => VALIDATE_INTEGER,
                  'attributes' => VALIDATE_ARRAY,
               ),
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
                  array(
                     'type' => VALIDATE_INTEGER,
                     'min' => 1,
                     'max' => 9999
                  ),
               ),
            ),
         );
         
      }

      /**
       * Add products to cart
       * 
       * @api-name cart.add
       * @api-auth required
       * @api-post-optional prodno String Product number
       * @api-post-optional quantity Integer Number of products
       * @api-post-optional attributes Array Product attributes
       * @api-get-optional prodno String Product number
       * @api-get-optional quantity Integer Number of products
       * @api-get-optional attributes Array Product attributes
       * @api-param-optional prodno String Product number
       * @api-param-optional quantity Integer Number of products
       * @api-param-optional attributes Array Product attributes
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $prodno = '', $quantity = 0, $attributes = array() ) {
         
         // Get post or get if they are set
         if( isset( $_GET['prodno'] ) ) $prodno = $_GET['prodno'];
         if( isset( $_GET['quantity'] ) ) $quantity = $_GET['quantity'];
         if( isset( $_GET['attributes'] ) ) $attributes = $_GET['attributes'];
         if( isset( $_POST['prodno'] ) ) $prodno = $_POST['prodno'];
         if( isset( $_POST['quantity'] ) ) $quantity = $_POST['quantity'];
         if( isset( $_POST['attributes'] ) ) $attributes = $_POST['attributes'];
         
         
         $this->result = false;
         $this->message = 'Not a valid quantity';
         if( $quantity < 1 ) return false;
         if( $quantity > 9999 ) return false;
         
         $this->message = "Product doesn't exist or is not correctly setup";
         $this->result = false;
         
         $product = ProductOption::fromProdNo( $prodno );
         if( !$product->isLoaded() || !$product instanceof ProductOption ) return false;
         
         // Everything's fine?
         if( !empty( $prodno ) && $quantity > 0 ) {
            
            $cart = new Cart();
            
            // Try to put in the cart
            $cart->addItem( $prodno, $quantity, $attributes );
            $cart->save();
            
            // store some local entries from the cart
            $this->totalprice = $cart->getTotalPrice();
            $this->totalitems = $cart->getTotalItems();
            $this->totalweight = $cart->getTotalWeight();
            
            // return successful!
            $this->result = true;
            $this->message = 'OK';
            
            return true;
            
               
         } else {
               
            $this->result = false;
            $this->message = "Missing or faulty params given.";
            return false;
               
         }
         
      }
      
   }

?>