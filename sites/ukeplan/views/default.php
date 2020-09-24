<?PHP
   
   class NotFound extends WebPage implements IView {
      
      public function Execute() {
         
         if( !count( Dispatcher::getExecPath() ) ) {

           $this->template = "frontpage.index";
           
         } else if( !$this->quickRoute( 'cms.default', 'cms', Dispatcher::getExecPath() ) ) {
            
            header( 'HTTP/1.0 404 Not Found' );
            
            $this->setTemplate( 'errors.notfound' );
            
            WebPage::$canonical = '';
            $this->canonical = '';
            
         }
         
      }
      
   }
   
?>