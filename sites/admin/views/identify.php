<?PHP
   
   class IdentifyAdminHost extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
         
         echo `hostname`;
         
      }
      
   }
   
?>