<?PHP
   
   import( 'pages.admin' );
   
   class PHPInfo extends AdminPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
         
         phpinfo();
         
      }
      
   }
   
?>