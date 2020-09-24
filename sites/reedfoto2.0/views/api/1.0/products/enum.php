<?php


   import( 'pages.json' );
   import( 'website.product' );
   
   class APIProductsEnum extends JSONPage implements IView {

      /**
       * Validator
       *
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'group' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      /**
       * enum products, filter by grouping
       * 
       * @api-name products.enum
       * @api-param-optional group group name
       * @api-post-optional group group name
       * @api-result products Array List of products
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
          
      public function Execute( $group = '' ) {
         
         $group = $group ? $group : $_POST['group'];

         $products = array();
         
         if ( strpos( $group, ',') > 0 ) {
            $groups = explode( ',', $group );
         } else {
            $groups[] = $group;
         }
         
         foreach ( $groups as $group ) {
         
            $products[] = Product::listProductsByGroup( $group );
         
         }
         
         $this->products = $products;
         $this->result = true;
         $this->message = "OK";
         
      }
      
   }


?>
