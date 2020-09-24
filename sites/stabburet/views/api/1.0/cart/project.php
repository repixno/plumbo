<?php

   /**
    * API to add different types of products to EF cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no> / Tor Inge Løvland
    */

   import( 'pages.json' );
   import( 'website.cart' );
   
   class APICartAddProject extends JSONPage implements NoAuthRequired, IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'quantity' => VALIDATE_INTEGER,
                  'imageid' => VALIDATE_INTEGER,
                  'x1' => VALIDATE_INTEGER,
                  'y1' => VALIDATE_INTEGER,
                  'width' => VALIDATE_INTEGER,
                  'height' => VALIDATE_INTEGER,
               )
            ),
         );
         
      }

      public function Execute( $prodno = '', $quantity = 0, $attributes = array() ) {

         if( isset( $_POST['prodno'] ) ) $prodno = $_POST['prodno'];
         if( isset( $_POST['quantity'] ) ) $quantity = $_POST['quantity'];
         if( isset( $_POST['attributes'] ) ) $attributes = $_POST['attributes'];
         if( isset( $_POST['imageid'] ) ) $imageid = $_POST['imageid'];
         if( isset( $_POST['x1'] ) ) $x1 = $_POST['x1'];
         if( isset( $_POST['y1'] ) ) $y1 = $_POST['y1'];
         if( isset( $_POST['width'] ) ) $width = $_POST['width'];
         if( isset( $_POST['height'] ) ) $height = $_POST['height'];
         
         
         file_put_contents( "/home/toringe/project/cartadd.txt", "\n******DEBUG******\n" . date( 'Y-m-d H:i:s') . "\n" . serialize( $_POST ) . "\n*****************\n" );
         
         $this->result = false;
         $this->message = 'Not a valid quantity';
         if( $quantity < 1 ) return false;
         if( $quantity > 9999 ) return false;
         
         $this->message = "Product doesn't exist or is not correctly setup";
         $this->result = false;
         
         $product = ProductOption::fromProdNo( $prodno );
         if( !$product->isLoaded() || !$product instanceof ProductOption ) return false;
         
         
         $attributes = array( 
            'images' => array( 
                  $imageid => $quantity,
                  'crop'   => array(
                        'x1' => $x1,
                        'y1' => $y1,
                        'height' => $height,
                        'width' => $width
                     )
                  )
               );
         
         // Everything's fine?
         if( !empty( $prodno ) && $quantity > 0 ) {
            
            $cart = new Cart();
            
            // Try to put in the cart
            $cart->addItem( $prodno, $quantity, $attributes );
            $cart->save();
            
            // store some local entries from the cart
            $this->totalprice = $cart->getTotalPrice();
            $this->totalitems = $cart->getTotalItems();
            $this->totalweight = $cart->getTotalWeight();
            
            // return successful!
            $this->result = true;
            $this->message = 'OK';
            
            return true;
            
               
         } else {
               
            $this->result = false;
            $this->message = "Missing or faulty params given.";
            return false;
               
         }
         
      }
      
   }

?>