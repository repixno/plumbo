<?PHP
   
   import( 'pages.admin' );
   
   class DocViewer extends AdminPage implements IView {
      
      protected $template = false;
      
      public function Execute( $apifunction = '' ) {
         
         $docpath = sprintf( '%s/docs/api', getRootPath() );
         if( !$apifunction ) $apifunction = 'index';
         
         $apihtml = sprintf( '%s/%s.html', $docpath, $apifunction );
         if( file_exists( $apihtml ) ) {
            
            echo file_get_contents( $apihtml );
            
         } else {
            
            relocate( '/' );
            
         }
         
      }
      
   }
   
?>