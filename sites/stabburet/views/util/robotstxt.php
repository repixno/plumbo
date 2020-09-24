<?PHP
   
   class RobotsTXT extends WebPage implements IView {
      
      protected $template = 'util.robotstxt';
      
      public function Execute() {
         
         header( 'content-type: text/plain' );
         
         $items = $children = array();
         foreach( Menu::enumItems() as $item ) {
            $items[$item->id] = $item->asArray();
            $children[$item->parentid][] = $item->id;
         }
         
         $this->menuitems = Menu::createMenuTree( $items, $children, 0 );
         
      }
      
   }
   
?>