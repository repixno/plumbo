<?PHP
   
   import( 'reedfoto.page' );
   import( 'reedfoto.correction' );

   class PageFileStreamer extends UserPage implements IView, AllowHTTPCache {
      
      protected $template = false;
      
      public function Execute( $pagecommentid = 0 ) {
         
         $storageroot = Settings::Get( 'reedfoto', 'storageroot' );
         $filesfolder = Settings::get( 'reedfoto', 'filesfolder', 'files' );
         $filespath = sprintf( '%s/%s/%s', getRootPath(), $storageroot, $filesfolder );
         
         try {
            
            $pagecomment = new RFPageComment( $pagecommentid );
            if( !$pagecomment->isLoaded() ) {
               throw new Exception( 'Not Found', 404 );
            }
            
            $page = new RFPage( $pagecomment->pageid );
            if( !$page->isLoaded() ) {
               throw new Exception( 'Not Found', 404 );
            }
            
            // ensure the user has access to this document
            /*if( !Login::isAdmin() && $pagecomment->userid != Login::userid() ) {
               throw new Exception( 'Access denied', 403 );
            }*/
            
            $fileguid = $pagecomment->filehash;
            
            $fullpath = sprintf( '%s/%s/%s/%s.ext', $filespath, substr( $fileguid, 0, 2 ), substr( $fileguid, 2, 2 ), $fileguid );

            if( file_exists( $fullpath ) ) {
               
               ob_end_clean();
               header( 'Content-Type: '. $pagecomment->filetype );
               header( 'Content-Length: '.filesize( $fullpath ) );
               header( 'Content-Transfer-Encoding: binary' );
               header( 'Cache-Control: public' );
               header( 'Age: 86400' );
               header( 'Expires: '.gmdate( 'D, d M Y H:i:s', time() + 86400 ) . ' GMT' );
               header( 'Last-Modified: '.gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
               header( 'Pragma:', true );
               # header( 'ETag: "'.md5( sprintf( '%s-%s-%d', $pageguid, $size, filemtime( $fullpath ) ) ).'"' );
               
               flush(); ob_flush();
               $file = fopen( $fullpath, 'r' );
               while( !feof( $file ) ) {
                  echo fread( $file, 4096 );
                  flush(); ob_flush();
               }
               fclose( $file );
               
            }
            
         } catch( Exception $e ) {
            
            if( $e->getCode() == 404 || $e->getCode() == 403 ) {
               header( 'HTTP/1.0 '.$e->getCode().' '.$e->getMessage() );
            }
            echo '<H1>'.$e->getMessage().'</H1>';
            die();
            
         }
         
      }
      
   }
   
?>