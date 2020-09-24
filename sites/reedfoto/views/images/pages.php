<?PHP
   
   import( 'reedfoto.page' );
   import( 'reedfoto.correction' );

   class PageImageStreamer extends UserPage implements IView, AllowHTTPCache {
      
      protected $template = false;
      
      public function Execute( $pageid = 0, $size = 'small.jpg' ) {
         
         $storageroot = Settings::Get( 'reedfoto', 'storageroot' );
         $archivefolder = Settings::get( 'reedfoto', 'archivefolder', 'archive' );
         $storagefolder = sprintf( '%s/%s/%s', getRootPath(), $storageroot, $archivefolder );
         
         switch( $size ) {
            
            case 'small.jpg':
            case 'large.jpg':
            case 'medium.jpg':
            case 'print.jpg':
               break;
            default:
               $size = 'small.jpg';
               break;
         }
         
         try {
            
            $page = new RFPage( $pageid );
            if( !$page->isLoaded() ) {
               throw new SecurityException( 'Not Found', 404 );
            }
            
            $correction = new RFCorrection( $page->correctionid );
            if( !$correction->isLoaded() ) {
               throw new SecurityException( 'Not Found', 404 );
            }
            
            // ensure the user has access to this document
            if( !Login::isAdmin() && $correction->userid != Login::userid() ) {
               throw new SecurityException( 'Access denied', 403 );
            }
            
            $pageguid = $page->imageguid;
            
            $fullpath = sprintf( '%s/%s/%s/%s.%s', $storagefolder, substr( $pageguid, 0, 2 ), substr( $pageguid, 2, 2 ), $pageguid, $size );
            
            if( $size == 'print.jpg' && !file_exists( $fullpath ) ) {
               
               $imagick = new Imagick( sprintf( '%s/%s/%s/%s.%s', $storagefolder, substr( $pageguid, 0, 2 ), substr( $pageguid, 2, 2 ), $pageguid, 'large.jpg' ) );
               $imagick->rotateImage( 'white', 270 );
               $imagick->writeImage( $fullpath );
               
            }
            
            if( file_exists( $fullpath ) ) {
               
               ob_end_clean();
               header( 'Content-Type: image/jpeg' );
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