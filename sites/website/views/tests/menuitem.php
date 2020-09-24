<?PHP
   
   import( 'website.menu' );
   
   import( 'website.textentity' );
   
   import( 'website.article' );
   import( 'website.product' );

   class ArticleTests extends WebPage implements IView {
      
      protected $template = 'errors.notfound';
      
      public function Execute( $menuuuid = '', $dottemplate = '', $articleuuid = '' ) {
         
         // 
         $menuitem = MenuItem::fromFieldValue( array( 'identifier' => $menuuuid ), 'MenuItem' );
         if( !$menuitem ) return false;
         
         // store the menuitem
         $this->menuitem = $menuitem->asArray();
         
         $items = $children = array();
         foreach( Menu::enumItems() as $item ) {
            $items[$item->id] = $item->asArray();
            $children[$item->parentid][] = $item->id;
         }
         
         $this->menuitems = Menu::createMenuTree( $items, $children, $menuitem->id );
         
         if( $dottemplate ) {
            $this->setTemplate( $dottemplate );
         } elseif( $menuitem->template ) {
            $this->setTemplate( $menuitem->template );
         }
         
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
         
         return true;
         
      }
      
   }
   
?>