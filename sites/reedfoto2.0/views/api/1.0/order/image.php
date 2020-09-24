<?php

   /**
    * 
    * Put image in cart
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'pages.json' );
   import( 'website.cart' );
   
   class APIOrderImage extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'imageid' => VALIDATE_INTEGER,
                  'quantity' => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      
      public function Execute() {
         
         $prodno = isset( $_POST['prodno'] ) ? $_POST['prodno'] : null;
         $imageid = isset( $_POST['imageid'] ) ? (int) $_POST['imageid'] : null;
         $quantity = isset( $_POST['quantity'] ) ? (int) $_POST['quantity'] : 1;
         
         $this->result = false;
         $this->message = 'Missing prodno';
         if( !isset( $prodno ) ) return false;
         
         $this->result = false;
         $this->message = 'Missing imageid';
         if( !isset( $imageid ) ) return false;
         
         $this->result = false;
         $this->message = 'Missing quantity';
         if( !isset( $quantity ) ) return false;
         
         try {
            
            $image = new Image( $imageid );
            
         } catch ( Exception $e ) {
            
            $this->result = true;
            $this->message = 'No image access';
            return false;
            
         }
         
         $cart = new Cart();
         $this->result = false;
         $this->message = 'Could not initialize cart';
         if( !$cart instanceof Cart ) return false;
         
         $attributes = array( 'images' => array( $imageid => $quantity ) );
         $cart->addItem( $prodno, $quantity, $attributes );
         $cart->save();
         
         $this->result = true;
         $this->message = 'OK';
         return true;
         
      }
      
   }

   


?>