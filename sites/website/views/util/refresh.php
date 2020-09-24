<?PHP
   
   class RefreshUtil extends WebPage implements IView {
      
      protected $template = 'dialogs.refresh';
      
      public function Execute( $refreshtime = 10 ) {
         
         $this->refreshtime = $refreshtime;
         
      }
      
   }
   
?>