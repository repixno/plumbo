<?PHP
   
   import( 'website.menu' );
   
   import( 'website.textentity' );
   
   import( 'website.article' );
   import( 'website.product' );

   class CMS extends WebPage implements IView {
      
      protected $template = 'cms.content';
      
      public function Execute() {
         
         if($_GET['utm_source']){
            Session::set('utm_source',$_GET['utm_source']);
         }
         
         $menuitem = Menu::findItemFromURL( Dispatcher::$execPath );
         if( !$menuitem ) return false;
         
         // store the menuitem
         $menuitemarray = $menuitem->asArray();
         $this->menuitem = $menuitemarray;
         if( !empty( $menuitemarray['article']['customjs'] ) )    $this->customjs = $menuitemarray['article']['customjs'];
         if( !empty( $menuitemarray['article']['customcss'] ) )   $this->customcss = $menuitemarray['article']['customcss'];
         if( !empty( $menuitemarray['article']['comment'] ) )     $this->comment = $menuitemarray['article']['comment'];
         
         
         $items = $children = array();
         foreach( Menu::enumItems() as $item ) {
            $items[$item->id] = $item->asArray();
            $children[$item->parentid][] = $item->id;
         }
         
         $this->menuitems = Menu::createMenuTree( $items, $children, $menuitem->id );
         
         if( $menuitem->template ) {
            $this->setTemplate( $menuitem->template );
         }
         
         // is this an article or direct link?
         if( $menuitem->partialMatch ) {
            
            $entity = Dispatcher::getExecPath();
            $entity = end( $entity );
            
            $this->menudepth = count( $entity ) - 1;
            
            $entity = TextEntity::findEntityInMenuSet( $entity, $menuitem->getContentIds(), i18n::languageCode() );
            if( $entity instanceof TextEntity && $entity->isLoaded() ) {
               
               switch( $entity->type ) {
                  
                  case 'article': 
                  case 'product': 
                     $item = new $entity->type( $entity->id );
                     if( $item->template ) {
                        $this->setTemplate( $item->template );
                     }
                     break;
                  
               }
               
               if( empty( $item ) ) return false;
               
               $this->item = $item->asArray();
               
            } else {
               
               return false;
               
            }
            
         } else {
            
            // store the current menu depth
            $this->menudepth = count( Dispatcher::getExecPath() );
               
            // get the sections
            $sections = $menuitem->getContentObjects( true );
            
            // fill the default items
            $this->items = $sections[''];
            
            // create a list of sections
            $allsections = array();
            if( count( $sections ) > 0 ) {
               foreach( $sections as $title => $items ) {
                  $allsections[$title ? $title : 'default'] = $items;
               } 
            }
            
            // fill the sections-item
            $this->sections = $allsections;
            
         }
         
         return true;
         
      }
      
   }
   
?>
