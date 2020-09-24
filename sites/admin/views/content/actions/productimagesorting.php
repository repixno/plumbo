<?php

   import( 'pages.json' );
   import( 'website.product' );
   
   class ProductImageSorting extends JSONPage implements IView {
      
      public function Execute( $productid = 0, $sorting = '' ) {
         
         if( $productid == 0 || $sorting == '' ) {
            $this->message = 'No product defined';
            $this->result  = false;
            return false;
         }

         $sort = array_unique( explode( ',', $sorting ) );
         
         $product = new Product( $productid );
         if( !$product->isLoaded() ) return false;
         
         $images = explode( ',', $product->images );
         
         // Make sure all images are defined
         if( count( $images ) <> count( $sort ) ) {
            $this->message = 'Image count mismatch';
            $this->result  = false;
            return false;
         }
         
         if( is_array( $images ) && count( $images ) > 0 ) {
            
            foreach( $images as $image ) {
               if( !empty( $image ) ) {
                  $savedimages[] = $image;
               } else {
                  $this->message = 'No images defined for product.';
                  $this->result = true;
                  return false;
               }
               
               
            }
            
            foreach( $savedimages as $key => $ordervalue ) {
               
               $neworder[$key] = $savedimages[$sort[$key]];
               
            }
            
            
            $product->images = implode( ',', $neworder );
            $product->save();
            
         }
         
         $this->message = 'OK';
         $this->result = true;
         return true;
            
      }
      
   }

?>