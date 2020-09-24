<?PHP

   import( 'pages.admin' );
   
   model( 'i18n.language' );

   class functionsAdmin extends AdminPage implements IView {

      protected $template = 'functions.index';

      public function Execute() {
      
      }

   }

?>