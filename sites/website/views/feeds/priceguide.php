<?php

Dispatcher::extendView( 'feeds.kelkoo' );

class Priceguide extends Kelkoo implements IView {

   public function Execute( $productgroup = "" ){

      //$products = Product::listProductsByGroup( $productgroup );
            
      $txt = "product_name;price;shipping_cost;stock;URL;image_url\n";
      /*
      foreach( $products as $productinfo ){
         
         $txt .=  $productinfo['title'] . ";" .
                  $productinfo['option']['prices'][0]['price'] . ";" .
                  $this->getDeliveryPrice($productinfo['option']['weigth']) . ";" .
                  "20;" .
                  "http://eurofoto.no" . $productinfo['url'] . ";" .
                  WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/height/500/" . $productinfo['images'][0]['url'] . ";\n";

      };*/
      
      
      header ( 'content-type: text/plain' );
      echo $txt;  
   }

}


?>