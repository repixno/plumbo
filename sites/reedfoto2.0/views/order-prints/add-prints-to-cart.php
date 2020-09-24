<?php

   /**
    * Add choosen images for purhchase to cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'session.usersessionarray' );
   import( 'website.cart' );
   import( 'website.product' );

   class OrderPrintsAddPrintsToCart extends UserPage implements IView {
      
      public function Execute() {
         
         $printOrder = UserSessionArray::getItems( "printorder" );
         $printOrder = reset( $printOrder );
         $tmpOrder = array();
         
         //util::debug( $printOrder );
         
         if( count( $printOrder["imageobjects"] ) > 0 ) {
            
            foreach( $printOrder["imageobjects"] as $imageId => $imageData ) {
               
               $imageQuantity = $imageData["quantity"];
               
               if( count( $imageQuantity ) > 0 ) {

                  
                  foreach( $imageQuantity as $prodno => $qty ) {
                     
                     $tmpOrder[$prodno][$imageId] += $qty;
                     $totQty[$prodno] += $qty;
                     
                  }
                  
               }
               
            }
            
         }
         
         //util::debug( $tmpOrder );
         
         
         if( count( $tmpOrder ) > 0 ) {
            
            foreach( $tmpOrder as $prodno => $imageData ) {
               
               $totQty = 0;
               
               foreach( $imageData as $imageId => $quantity ) {
                  
                  
                  $totQty += $quantity;
                  
               }
               
               if( $totQty > 0 ) {
                  //util::debug( $imageId );
                  Cart::addItem( $prodno, $totQty, array( array( "images" => $imageData ) ) );
               }
               
            }
            
         }
         
         UserSessionArray::clearItems( "printorder" );
         UserSessionArray::clearItems( "choosenimages" );
         
         relocate( '/cart' );
         
      }
      
      
   }



?>