<?PHP
   
   import( 'pages.admin' );
   
   class DefaultMenuList extends AdminPage implements IView {
      
      protected $template = 'content.defaultmenu';
      
      public function Execute( $accessoryid = null ) {
         
         $this->products = array();
         
         $collection = new Product();
         
         if( count( $collection ) ) foreach( $collection->collection( array( 'id' ) )->fetchAllAs( 'Product' ) as $product ) {
         	
            if( !$product->id ) continue;
            
            if( ( $product->type == 'product' ) ) {
            
               $newproduct = array(
                  'id'              => $product->id,
                  'title'           => $product->title,
                  'type'            => $product->type,
                  'defaultmenuid'   => $product->defaultmenuid,
                  'defaultmenuurl'  => $this->getMenuUrl( $product->defaultmenuid ),
                  'url'             => $product->url,
               );
               
               if ($product->defaultmenuid) {
                  $newproduct['hasdefault'] = true;
               }
               
               $products[] = $newproduct;
            
            }

         }
         
         $this->products = $products;
         
      }

      public function getMenuUrl( $defaultmenuid ) {

         $menu = new MenuItem( $defaultmenuid ); 

         return $menu->getTranslatedUrl(i18n::languageCode());
      }
      
   }
   
?>
