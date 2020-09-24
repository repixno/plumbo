<?php
   
   class DyreparkenDefault extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute() {
         
         relocate('/');
         //$this->quickRoute( 'cms.default', 'cms', Dispatcher::getExecPath() );
         
      }
      
   } 
?>