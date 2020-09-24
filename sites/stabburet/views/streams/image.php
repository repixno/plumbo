<?php
   
   import( 'storage.util' );
   import( 'storage.stream' );
   import( 'website.image' );
   
   class StreamImage extends WebPage implements IValidatedView {
      
      protected $template = false;
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  'year' => VALIDATE_STRING,
                  'month' => VALIDATE_STRING,
                  'day' => VALIDATE_STRING,
                  'hash' => VALIDATE_STRING,
                  'imageid' => VALIDATE_INTEGER,
               )
            )
         
         );
         
      }
      
      public function Execute( $year = '', $month = '', $day = '', $hash = '', $imageid = 0 ) {
         
         try {
            
            if( !$imageid ) throw new Exception( 'Not found', 404 );
            
            $image = new Image( $imageid );
            
            if( !$image->validateSignature( $hash ) ) {
               
               throw new SecurityException( 'Permission denied', 403 );
               
            }
            
            if( $image instanceof Image ) {
               
               header( 'Pragma: public' );
               #header( 'Expires: 0' );
               #header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
               #header( 'Cache-Control: public' );
               header( 'Expires', gmdate( ) );
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