<?PHP

   include "../../bootstrap.php";
   config( 'website.config' );
   
   import( 'website.giftpagetemplate' );
   
   foreach( DB::query( 'SELECT artikkelnr FROM mal' )->fetchAll() as $row ) {
      list( $malid ) = $row;
      $templateids[$malid] = true;
   }
   
   i18n::setLanguage('nb_NO');
   
   foreach( $templateids as $malid => $d ) {
      foreach( DB::query( 'SELECT id FROM site_product_option WHERE refid = ?', $malid )->fetchAllAs('ProductOption') as $productoption ) {
         if( $productoption instanceof ProductOption ) {
            $product = new Product( $productoption->productid );
            if( $product->isLoaded() ) {
               echo "{$productoption->productid}/{$productoption->id}/$malid: ";
               echo $product->title;
               echo ' - old: ';
               echo $productoption->tags;
               
               $tags = explode( ' ', trim( $productoption->tags ) );
               $tags = array_flip( $tags );
               $tags['gift'] = true;
               $tags = array_keys( $tags );
               $tags = implode( ' ', $tags );
               
               echo ' - new: ';
               echo $tags;
               
               $productoption->tags = $tags;
               $productoption->save();
               
               echo "\n";
            }
         }
      }
   }
   
?>