<?PHP

   import( 'pages.admin' );
   
   model( 'i18n.language' );

   class AdminOldadmin extends AdminPage implements IView {

      protected $template = 'old_admin.index';

      public function Execute() {
      
      }
	 
	 
     public function orders() {
        $this->setTemplate( 'old_admin.orders' );
      }

   }

?>