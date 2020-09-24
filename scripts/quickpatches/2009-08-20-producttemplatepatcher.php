<?PHP
   
   exit();
   $force = true;
   
   include "../../bootstrap.php";
   
   config('website.config');
   import( 'website.product' );
   import( 'website.article' );
   
   // has this object type a default template set?
   $templates = Settings::get( 'cms', 'templates', array() );

   // resave all articles  
   $articles = new Article();
   foreach( $articles->collection( array( 'id' ), array() )->fetchAllAs('Article') as $article ) {
      if( ( $force || !$article->template ) && isset( $templates['textentity_defaults']['article'] ) ) {
         $article->template = $templates['textentity_defaults']['article'];
         $article->save();
         echo sprintf( "Patched article %s\n", $article->title );
      }
   }
   
   // resave all products
   $products = new Product();
   foreach( $products->collection( array( 'id' ), array() )->fetchAllAs('Product') as $product ) {
      if( ( $force || !$product->template ) && isset( $templates['textentity_defaults']['product'] ) ) {
         $product->template = $templates['textentity_defaults']['product'];
         $product->save();
         echo sprintf( "Patched product %s\n", $product->title );
      }
   }
   
?>
