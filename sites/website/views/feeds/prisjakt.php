<?php

Dispatcher::extendView( 'feeds.kelkoo' );

class Prisjakt extends Kelkoo implements IView {

   public function Execute( $productgroup = "" ){

      //$products = Product::listProductsByGroup( $productgroup );
            
      $txt = "Produktnamn;Art.nr.;Kategori;Pris inkl.moms;Lagerstatus;Produkt-URL;Tillverkare;Frakt;Bild-URL\n";
      
      /*foreach( $products as $productinfo ){
         
         $txt .=  $productinfo['title'] . ";" .
                  $productinfo['option']['prodno'] . ";" .
                  $productgroup . ";" .
                  $productinfo['option']['prices'][0]['price'] . ";" .
                  "20;" .
                  "http://eurofoto.no" . $productinfo['url'] . ";" .
                  substr($productinfo['title'], 0, strpos($productinfo['title'],' ')) . ";" .
                  WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/height/500/" . $productinfo['images'][0]['url'] . ";\n";

      };*/
      
      
      header ( 'content-type: text/plain' );
      echo $txt;  
   }

}


?>