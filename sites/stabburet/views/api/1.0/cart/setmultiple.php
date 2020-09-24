<?php

   /**
    * Update cart from an array with multiple products
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class APICartSetMultiple extends JSONPage implements NoAuthRequired, IView {

      /**
       * Update cart from an array with multiple products
       * 
       * @api-name cart.setmultiple
       * @api-auth required
       * @api-post-optional gifts Array Multiple products
       * @api-post-optional mediaclip Array Multiple products
       * @api-post-optional goods Array Multiple products
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute() {
         
         //$postdata['gift'] = 
         $postdata = unserialize( $_POST );
         
         if( isset( $postdata['gifts'] ) || isset( $postdata['mediaclip'] ) || isset( $postdata['goods'] ) ) {
            
            $cartChanged = false;
            $cart = new Cart();
            $cartarray = $cart->asArray();
            
            if( isset( $postdata['gifts'] ) ) {
               
               $gifts = $postdata['gifts'];
               
               foreach( $gifts as $prodno => $products ) {

                  if( count( $products ) ) {
                     
                     foreach( $products as $referenceid => $quantity ) {

                        if( $cartarray['items']['gifts'][$prodno][$referenceid]['quantity'] != $quantity ) {
                           $cart->setItemQuantity( $prodno, $quantity, $referenceid );
                           $cartChanged = true;
                        }
                        
                     }
                     
                  }
                  
               }
               
            }
            
            if( isset( $postdata['mediaclip'] ) ) {
               
               $mediaclip = $postdata['mediaclip'];
               
               foreach( $medaiclip as $prodno => $products ) {
                  
                  if( count( $products ) ) {
                     
                     foreach( $products as $referenceid => $quantity ) {
                        
                        if( $cartarray['items']['mediaclip'][$prodno][$referenceid]['quantity'] != $quantity ) {
                           $cart->setItemQuantity( $prodno, $quantity, $referenceid );
                           $cartChanged = true;
                        }
                        
                     }
                     
                  }
                  
               }
               
            }
            
            if( isset( $postdata['goods'] ) ) {
               
               $goods = $postdata['goods'];
               
               foreach( $goods as $prodno => $productdata ) {
                  
                  if( $cartarray['items']['mediaclip'][$prodno]['quantity'] != $quantity ) {
                     $cart->setItemQuantity( $prodno, $quantity );
                     $cartChanged = true;
                  }
                  
               }
               
            }
            
            if( $cartChanged ) {
               
               $cart->save();
               $this->cart = $cart->asArray();
               $this->result = true;
               $this->message = 'Cart updated';
               return true;
               
            } else {
               
               $this->result = false;
               $this->messsage = 'Cart not updated. Nothing changed';
               return false;
            }
            
         } 
            
         $this->result = false;
         $this->message = 'Nothing to update';
         return false;
         
      }
      
   }


?>
