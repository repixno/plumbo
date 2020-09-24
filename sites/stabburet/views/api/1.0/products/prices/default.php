<?php


   /**
    * Get the prices for a given product and quantity
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'website.product' );
   import( 'pages.json' );

   class APIProductsPricesGet extends JSONPage implements NoAuthRequired, IValidatedView {
      

      /**
       * Validate the incoming data
       *
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'quantity' => array(
                     'type' => VALIDATE_INTEGER,
                     'min' => 1,
                     'max' => 9999
                  ),
               ),
               'fields' => array(
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
       * Get prices for a given product and quantity
       * 
       * @api-name products.prices
       * @api-auth required
       * @api-post-optional prodno String Product number
       * @api-param-optional quantity Array Quantity (type, min, max)
       * @api-post-optional prodno Array Quantity (type, min, max)
       * @api-param-optional prodno String Product number
       * @api-result price Price Price object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $prodno = '', $quantity = 0 ) {

         $product = ProductOption::fromProdNo( $prodno );
         
         $this->result = false;
         $this->message = "Product doesn't exist or is not correctly setup";
         
         if( !$product->isLoaded() || !$product instanceof ProductOption ) return false;
         $price = $product->getPrice( $quantity );
         
         if( !empty( $price ) ) {
            
            $this->message = "OK";
            
            $this->result = true;
            
            $this->price = $price;
            return true;
            
         }
            
         $this->message = "Couldn't get the price for this product";
         $this->result = false;
            
      }
      
      
      
      
   }

?>