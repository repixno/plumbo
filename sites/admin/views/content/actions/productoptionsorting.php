<?php

   import( 'pages.json' );
   import( 'website.product' );
   
   class ProductOptionSorting extends JSONPage implements IValidatedView, AdminRequired {

      public function Validate() {
         return array(
            'execute' => array(
               'post' => array(
                  'productid' => VALIDATE_INTEGER,
                  'sorting'     => VALIDATE_STRING
               ),
            ),
         );
      }

      public function Execute( ) {
         
         $productid = $_POST['productid'];
         $sorting = $_POST['sorting'];

         if( $productid == 0 || $sorting == '' ) {

            $this->message = 'No product or sorting is defined';
            $this->result  = false;

            return false;
         }

         try {

            $options = array_unique( explode( ',', $sorting ) );
         
            $product = new Product( $productid );
            if( !$product->isLoaded() ) return false;
         
            $index = 1;

            foreach ( $options as $option ) {
            
               $productOption = new ProductOption( $option );
               $productOption->orderkey = $index++;
               $productOption->save();

            }

            $this->message = 'OK';
            $this->result = true;

         } catch (Exception $e) {

            $this->message = $e;
            $this->result = false;

         }
            
      }
      
   }

?>
