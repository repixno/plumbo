<?php

import( 'website.menu' );
import( 'pages.json' );
model( 'site.product' );

class APIFetchProducts extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array()
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Fetch list of all protals in db.
      $products = new DBProduct();
      $ret = array();
      foreach ( $products->collection( array( 'id' ) )->fetchAll() as $product ) {

         $ret[] = $product;

      }

      //$this->products = $ret;
      $this->products = array(
         array( 123, 'Test product 1' ),
         array( 124, 'Test product 2' )
         );
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
