<?PHP
   
   class AssembliesImageViewer extends WebPage implements IView {
      
      static $resourcepath = '%s/data/projects/assemblies/%s/icons/%s';
      protected $template = false;
      
      public function Execute( $productkey = '', $image = '' ) {
         
         $resourcepath = sprintf( AssembliesImageViewer::$resourcepath, getRootPath(), $productkey, $image );
         if( file_exists( $resourcepath ) && is_file( $resourcepath ) ) {
            
            // generate unique ID
            $hash = md5( $image );
            
            // grab all request headers
            $headers = getallheaders();
            
            // if browser sent id, we check if they match
            if( isset( $headers['If-None-Match'] ) && strpos( $hash, $headers['If-None-Match'] ) !== false ) {   
               
               // Output a 304 Not Modified header
               header( 'HTTP/1.1 304 Not Modified' );
               
               // simply exit
               exit;
               
            } else {
               
               // setup caching headers
               header( "Accept-Ranges: bytes");
               header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
               header( 'Cache-Control: public' );
               header( 'Pragma: public' );
               
               // find the extension
               $elements = explode( '.', $image );
               $extension = strtolower( end( $elements ) );
               
               // based on extesion, draw the content-type
               switch( $extension ) {
                  
                  case 'jpg':
                  case 'jpeg':
                     header( 'Content-Type: image/jpeg' );
                     break;
                  case 'gif':
                     header( 'Content-Type: image/gif' );
                     break;
                  case 'png':
                     header( 'Content-Type: image/png' );
                     break;
                  default:
                     header( 'Content-Type: application/octet-stream' );
                     break;
                  
               }
               
               // read filedata from disk
               $filesize = filesize( $resourcepath );
               
               // draw the content-length header
               header( 'Content-Length: '.$filesize );
               
               // output the filedata
               readfile( $resourcepath );
               
            }
            
         } else {
            
            header( 'HTTP/1.0 404 Not Found' );
            
            $this->setTemplate( 'errors.notfound' );
            
         }
         
      }
      
   }
   
?>