<?PHP
   
   class FrontpageIndex extends WebPage implements IView {
      
      protected $template = 'frontpage.index';
      
      public function Execute() {
         
         if( Dispatcher::getPortal() == 'RP-001' ){
            relocate('/');
            exit;
         }
         
         setcookie("Chooseef", "done", time() + 3600 );
         
      }
               
   }
   
?>
