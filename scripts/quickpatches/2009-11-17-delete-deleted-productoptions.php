<?PHP
   
   include "../../bootstrap.php";
   config('website.config');
   
   import( 'website.product' );
   
   // first, find all deleted products
   $products = new Product();
   foreach( $products->collection( array( 'id' ), array( 'deleted' => array( 'IS', 'NOT NULL' ) ) )->fetchAllAs('Product') as $product ) {
      
      // find all product-options in them and...
      $collection = new ProductOption();
      foreach( $collection->collection( array( 'id' ), array( 'productid' => $product->id ) )->fetchAllAs( 'ProductOption' ) as $option ) {
         
         // ...as long as they're not deleted...
         if( !$option->deleted ) {
            
            echo "$option->id in product $product->id needed deletion\n";
            
            // ...delete them too
            $option->delete();
            
         }
         
      }
      
      // drop this article/product/whatever from the menu link table if present
      DB::query( 'DELETE FROM site_menu_contents WHERE textentityid = ?', $product->id );
      
   }
   
?>