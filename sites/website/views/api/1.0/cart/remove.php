<?php

   /**
    * 
    * Remove an item from cart
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'pages.json' );
   import( 'website.cart' );
   

   class APICartRemove extends JSONPage implements NoAuthRequired, IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'prodno' => VALIDATE_STRING,
                  'referenceid' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      
      /**
       * Remove a item from cart
       * 
       * @api-name cart.remove
       * @api-auth required
       * @api-post-optional prodno String Product number
       * @api-param-optional prodno String Product number
       * @api-post-optional referenceid String Item reference ID
       * @api-param-optional referenceid String Item reference ID
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Execute( $prodno = '', $referenceid = '' ) {
         
         if( isset( $_GET['prodno'] ) ) $prodno = $_GET['prodno'];
         if( isset( $_POST['prodno'] ) ) $prodno = $_POST['prodno'];
         if( isset( $_GET['referenceid'] ) ) $referenceid = $_GET['referenceid'];
         if( isset( $_POST['referenceid'] ) ) $referenceid = $_POST['referenceid'];
         
         $this->result = false;
         $this->message = "Not a valid prodno";
         if( empty( $prodno ) || !isset( $prodno ) ) return false;
         
         $this->result = true;
         $this->message = "OK";
         
         $cart = new Cart();
         $cart->removeItem( $prodno, $referenceid );
         $cart->save();
         
         return true;
            
      }
      
   }


?>