<?php
   
   import( 'storage.util' );
   import( 'storage.stream' );
   import( 'website.image' );
   
   class StreamDownload extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $imageid = 0, $filename = '' ) {
         
         try {
            
            if( !$imageid ) throw new Exception( 'Not found', 404 );
            
            $image = new Image( $imageid );
            
            if( $image instanceof Image ) {
               
               header( 'Pragma: public' );
               header( 'Expires: 0' );
               header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
               header( 'Cache-Control: public' );
               header( 'Content-Description: File Transfer' );
               header( 'Content-Transfer-Encoding: binary' );
               header( sprintf( 'Content-Disposition: attachment; filename="%s";', $image->filename ) );
               
               while( ob_get_level() > 0 ) {
                  ob_end_clean();
               }
               
               StorageUtil::readImage( $imageid );
               
            }
            
         } Catch (Exception $e) {
            
            header( 'HTTP/1.0 404 Not Found' );
            $this->setTemplate( 'errors.notfound' );
            
         }
         
      }
      
   }
   
?>