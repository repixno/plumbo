<?PHP
   
   import( 'reedfoto.page' );
   import( 'reedfoto.correction' );

   class PrintView extends UserPage implements IView {
      
      protected $template = 'print';
      
      public function Execute( $correctionid = 0 ) {
         
         $correction = new RFCorrection( $correctionid );
         if( !Login::isAdmin() && $correction->userid != Login::userid() ) {
            throw new SecurityException( 'Access denied', 403 );
         }
         
         $this->correction = $correction->asArray();
         
         $pages = array();
         foreach( RFPage::enum( $correctionid ) as $page ) {
            $pages[] = $page->asArray();
         }
         
         $this->pages = $pages;
         
      }
      
   }
   
?>