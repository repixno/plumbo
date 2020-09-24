<?
/**
 * ******************************************************
 * Script for streaming xml with information about
 * products to kelkoo
 *********************************************************/
import('website.product');

class Kelkoo extends WebPage implements IView {

	protected $template = '';
	

   public function Execute( $productgroup = "" ){

      $products = Product::listProductsByGroup( $productgroup );
            
      $xml = new SimpleXMLElement('<products></products>');
      /*
      foreach( $products as $productinfo ){

         $product = $xml->addChild('product');
         
         $product->Category = $productgroup;
         $product->Manufacturer = substr($productinfo['title'], 0, strpos($productinfo['title'],' ')); 
         $product->Productname = substr($productinfo['title'], strpos($productinfo['title'],' ')); ;
         $product->Productcode = $productinfo['option']['prodno'];
         $product->Price = $productinfo['option']['prices'][0]['price'];
         $product->Deliverycost = $this->getDeliveryPrice($productinfo['option']['weigth']);
         $product->Deliverytime = "3-5 D";
         $product->Availability = "In Stock";
         $product->ProductURL = "http://eurofoto.no" . $productinfo['url'];
         $product->ImageURL =  WebsiteHelper::staticBaseUrl() . "/images/products/thumbs/height/500/" . $productinfo['images'][0]['url'];
         $product->Description = $productinfo['ingress'];

      };
      */
      
      header ( 'content-type: text/xml' );
      echo $xml->asXML();  
   }
   
   
  protected function getDeliveryPrice($productweigth) {
         
         $regid = WebsiteHelper::region();

         if($productweigth > 1000) $productweigth = 1000;

         $shippingcost = DB::query( "
            SELECT 
               min(price)
            FROM 
               region_delivery 
            WHERE 
               regionid=? AND
               weight>=?" , $regid, $productweigth
         )->fetchSingle();
         
         return $shippingcost;
         
   }
}

?>
