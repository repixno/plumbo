<?PHP
   
   include "../../bootstrap.php";
   
   config('website.config');
   import( 'website.product' );

   // resave all products
   $productoptions = new ProductOption();
   foreach( $productoptions->collection()->fetchAllAs('ProductOption') as $productoption ) {
      $productoption->save();
   }
   
?>
