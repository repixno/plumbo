<?PHP
   
   class ServerNamePage extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
        
	echo `hostname`;

      }
      
   }
   
?>
