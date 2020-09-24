<?php

   /**
    * Toggle the printing/producing of a physical giftcard
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'pages.json' );
   import( 'website.cart' );

   class TogglePrintGiftcard extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'status' => VALIDATE_STRING,
               )
            )
         );
            
         
      }
      
      public function Execute() {
         
         $prodno = isset( $_POST['prodno'] ) ? $_POST['prodno'] : null;
         $status = isset( $_POST['status'] ) ? $_POST['status'] : null;
         if( $status == 'true' ) {
            $status = true;
         } else {
            $status = false;
         }
         
         $this->result = false;
         $this->message = 'Missing parameter';
         if( empty( $prodno ) ) return false;
         
         $productoption = ProductOption::fromProdNo( $prodno );
         $tags = explode( ' ', $productoption->tags );
         
         $this->result = false;
         $this->message = 'Not a giftcard product';
         if( !in_array( 'giftcard', $tags ) ) return false;
         
         try {
            
            // Load cart and toggle value
            $cart = new Cart();
            $cart->togglePrintGiftcard( $prodno, $status );
            $cart->save();
            
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed to toggle print';
            return false;
            
         }
         
         // Everything went fine
         $this->result = true;
         $this->message = 'OK';
         return true;
         
      }
      
   }


?>