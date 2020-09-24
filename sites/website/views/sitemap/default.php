<?PHP
   
   import( 'website.menu' );
   
   import( 'website.textentity' );
   
   import( 'website.article' );
   import( 'website.product' );

   class SitemapDefault extends WebPage implements IView {
      
      protected $template = 'sitemap.index';
      
      public function Execute() {
         
         foreach( Menu::enumItems() as $item ) {
            $items[$item->id] = $item->asArray();
            $children[$item->parentid][] = $item->id;
         }
         
         $this->menuitems = Menu::createMenuTree( $items, $children, $menuitem->id, 0, 0, true );
         
         if( $menuitem->template ) {
            $this->setTemplate( $menuitem->template );
         }
         
         return true;
         
      }
      
      public function XML() {
         
         header( "content-type: text/xml" );
         $this->setTemplate( 'sitemap.xml' );
         return $this->Execute();
         
      }
      
   }
   
?>