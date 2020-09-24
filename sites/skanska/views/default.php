<?PHP
   
   class NotFound extends WebPage implements IView {
      
      protected $template = 'frontpage.index';
      
      public function Execute() {
         
         if( Dispatcher::$execPath ) {
            
            if( !$this->quickRoute( 'website|cms.default', 'cms', Dispatcher::getExecPath() ) ) {
               
               header( 'HTTP/1.0 404 Not Found' );
               
               $this->setTemplate( 'errors.notfound' );
               
               WebPage::$canonical = '';
               $this->canonical = '';
               
            }
            
         }
         
      }
      
   }
   
?>