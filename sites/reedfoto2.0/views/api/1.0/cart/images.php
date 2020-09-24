<?php


   /**
    * Get unique images in cart for printing.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.cart' );
   
   class APICartImages extends JSONPage implements IView, NoAuthRequired {
      
      /**
       * Images in cart up for print
       * 
       * @api-name cart.images
       * @api-auth not required
       * @api-result images Array Images in user's cart
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function execute() {
         
         $cart = new Cart();
         
         // Get prints and enlargements from cart
         $items = $cart->enumItems();
         
         $this->result = false;
         $this->message = 'Cart is empty';
         if( count( $items ) == 0 ) return false;
         
         $prints = $items['prints'];
         $enlargements = $items['enlargements'];
         $allimages = array();
         $imageids = array();
         
         // Loop through prints
         if( count( $prints ) > 0 ) {
            
            foreach( $prints as $prodno => $item ) {
               $images = $item['images'];
               if( count( $images ) > 0 ) {
                  
                  foreach( $images as $imageid => $quantity ) {
                     
                     if( !in_array( $imageid, $imageids ) && $quantity > 0 ) {
                        $image = new Image( $imageid );
                        $allimages []= $image->asArray();
                        $imageids []= $imageid;
                     }
                     
                  }
                  
               }
               
            }
            
         }
         
         
         // Loop through enlargements
         if( count( $enlargements ) > 0 ) {
            
            foreach( $enlargements as $prodno => $item ) {
               $images = $item['images'];
               if( count( $images ) > 0 ) {
                  
                  foreach( $images as $imageid => $quantity ) {
                     
                     if( !in_array( $imageid, $imageids ) && $quantity > 0 ) {
                        $image = new Image( $imageid );
                        $allimages []= $image->asArray();
                        $imageids []= $imageid;
                     }
                     
                  }
                  
               }
               
            }
            
         }
         
         
         // Do we have any images
         if( count( $allimages ) > 0 ) {
            
            $this->result = true;
            $this->message = 'OK';
            $this->images = $allimages;
            return true;
            
         }
         
         
         // No images found
         $this->result = false;
         $this->message = 'No images in cart';
         return false;
         
      }
      
   }


?>