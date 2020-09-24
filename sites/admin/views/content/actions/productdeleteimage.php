<?php

   import( 'pages.json' );
   import( 'website.product' );
   
   class ProductDeleteImage extends JSONPage implements IView {
      
      public function Execute( $productid = 0, $imagename = '' ) {
         
         $product = new Product( $productid );
         if( !$product->isLoaded() ) return false;
         
         try {
            
            unlink( $path = sprintf( '%s/data/images/products/%s', getRootPath(), $imagename ) );

            $images = explode(',', $product->images );
            unset( $images[array_search( $imagename, $images )] );
            
            $product->images = implode( ',', $images );
            $product->save();
            
            $this->message = 'OK';
            $this->result = true;
            
         } catch ( Exception $e ) {
            
            $this->message = 'FAIL!';
            $this->result = false;
            
         }
            
      }
      
   }

?>