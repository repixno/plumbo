<?php


   /**
    * API for getting a price for given product.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.productset' );
   config( 'website.cart' );
   
   class APIPriceGet extends JSONPage implements NoAuthRequired,IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  'productoptionid' => VALIDATE_INTEGER,
                  'quantity' => VALIDATE_INTEGER,
               ),
               'post' => array(
                  'productoptionid' => VALIDATE_INTEGER,
                  'quantity' => VALIDATE_INTEGER,
               )
            ),
         );
         
      }
      /**
       * Get price by product option and quantity
       * 
       * @api-name prices.get
       * @api-auth required
       * @api-param-optional productoptionid Integer ID of the product option to fetch price for
       * @api-post-optional productoptionid Integer ID of the product option to fetch price for
       * @api-result price Integer Product price
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      
      public function Execute( $id = 0, $quantity = 1 ) {

         if( isset( $_POST['productoptionid'] ) )  {
            $id = (int) $_POST['productoptionid'];
         }
         
         if( isset( $_POST['quantity'] ) ) {
            $quantity = (int) $_POST['quantity'];
         }
         
         $this->result = false;
         $this->message = 'Required input parameter missing or invalid (productoptionid)';
         if( !$id > 0 ) return false;
         
         // Validate option id
         $this->result = false;
         $this->message = 'Required input parameter missing or invalid (productoptionid)';
         if( !isset( $id ) ) return false;
         
         // Validate loading of option id
         $this->result = false;
         $this->message = 'Failed to load product option';
         $productoption = new ProductOption( $id );
         if( !$productoption instanceof ProductOption || !$productoption->isLoaded() ) return false;
         
         // All's ok. Keep on trucking.
         if( ProductSet::isSetProduct( $id ) ) {
            $setquantity = (int) ProductSet::getSetQuantity( $id );
            $totalitemquantity = ( $setquantity * $quantity );
            $this->price = ( $productoption->getPrice( $totalitemquantity ) * $setquantity );
         } else {
            $this->price = $productoption->getPrice( $quantity );
         }
         $this->result = true;
         $this->message = 'OK';
         return true;
            
      }
      
   }

?>