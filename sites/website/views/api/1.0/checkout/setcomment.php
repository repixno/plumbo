<?php

   import( 'pages.json' );
   import( 'website.cart' );

   class APISetComment extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(  
            "execute" => array(
               "post" => array(
                  "comment" => VALIDATE_STRING,
               ),
               "fields" => array(
                  VALIDATE_STRING,
               )
            )
         );
         
      }

        
      public function Execute( ) {
        
        $comment = $_POST['comment']; 
        $cart = new Cart();
        $cart->setComment( $comment );
        $cart->save();

         
        $this->result = true;
        $this->message = "OK";
        $this->data = $comment;
         
         
      }
      
      
   }


?>