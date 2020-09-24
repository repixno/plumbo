<?PHP
   
   import( 'pages.admin' );

   class SetSite extends AdminPage implements IView {
      
      protected $template = false;
      
      public function Execute( $adminsiteid ) {
         
         Session::set( 'adminsiteid', $adminsiteid );
         Session::set( 'siteid', $adminsiteid );
         relocate( $_SERVER['HTTP_REFERER'] );
         die();
         
      }
      
   }
   
?>