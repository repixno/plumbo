<?PHP
   
   class FrontpageIndex extends WebPage implements IView {
      
      protected $template = 'frontpage.index';
      
      public function Execute() {
         
         if( Dispatcher::getPortal() == 'FE-001' ){
            relocate('/skapa');
            exit;
         }
         
         setcookie("Chooseef", "done", time() + 3600 );
         
      }
      
   }
   
?>
