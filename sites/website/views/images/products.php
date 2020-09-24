<?php
   
   import( 'cache.diskcache' );

   class ProductImageViewer extends WebPage implements IView {
      
      protected $template = false;
      
      protected $resourcepath = '%s/data/images/products/%s';
      protected $alphascaling = false;
      
      /**e
       * Thumbnails an existing image on disk or reads a cached copy from cache
       *
       * @param string $type Supported types are square, width and height
       * @param integer $size The target size in pixels
       * @param string $image The image file to thumbnail
       * @param boolean $force Whether or not to force a rethumbnail
       */
      public function Thumbs( $type = 'square', $size = 32, $image = '', $force = false ) {
         
         // create a diskcache engine instance
         $cache = new DiskCacheEngine();
         
         // define the local resource path
         $resourcepath = sprintf( $this->resourcepath, getRootPath(), $image );
         
         // does the resource path and file exist on disk?
         if( $type == 'blankbox' || ( file_exists( $resourcepath ) && is_file( $resourcepath ) ) ) {
                        
            $negate = $_GET['negate'];
            
            // generate a unique thumbname for this combination
            $thumbname = sprintf( '%s%s_%s_%s', $type, $size, $image, $negate );

            // generate unique ID
            $hash = md5( $thumbname );
            
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
               #header( "ETag: \"$hash\"");
               header( "Accept-Ranges: bytes");
               #header( "Content-Disposition: inline; filename=\"$thumbname\";" );
               header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
               header( 'Cache-Control: public' );
               header( 'Pragma: public' );
               
               // find the file extension
               $elements = explode( '.', $image );
               $extension = strtolower( end( $elements ) );
               
               // select the correct content-type
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
                     return $this->Execute( $image );
                     break;
                  
               }
               
               // attempt to read cached data from disk (unless forced regenerate)
               if( !$force && $filedata = $cache->read( $thumbname ) ) {
                  
                  // extract the filesize from the object
                  $filesize = strlen( $filedata );
                  
                  // output a content-length header
                  header( 'Content-Length: '.$filesize );
                  
                  // output the filedata
                  echo $filedata;
                  
               } else {
                  
                  $image = false;
                  
                  if( $type == 'blankbox' ) {
                     
                     if( !file_exists( $resourcepath ) ) {
                        
                        $resourcepath = sprintf( '%s/data/images/resources/transparent.png', getRootPath() );
                        
                     }
                     
                     $type = 'box';
                     
                  }
                  
                  // load the image from disk based on extension
                  if( !$image ) switch( $extension ) {
                  
                     case 'jpg':
                     case 'jpeg':
                        $image = imagecreatefromjpeg( $resourcepath );
                        break;
                     case 'gif':
                        $image = imagecreatefromgif( $resourcepath );
                        break;
                     case 'png':
                        $image = imagecreatefrompng( $resourcepath );
                        break;
                     
                  }
                  
                  // find the original sizex/sizey
                  $height = imagesy( $image );
                  $width = imagesx( $image );
                  
                  // what thumbnail type to create?
                  switch( $type ) {
                     
                     // in case of sqare
                     default:
                     case 'square':
                        
                        // limit the maximum scale to size of image 
                        if( $width < $size ) {
                           $size = $width;
                        }
                        
                        if( $height < $size ) {
                           $size = $height;
                        }
                        
                        // define new coordinates
                        $newtop = 0;
                        $newleft = 0;
                        $newwidth = $size;
                        $newheight = $size;
                        
                        if( $width > $height ) {
                           
                           $srctop = 0;
                           $srcleft = ($width / 2)-($height / 2);
                           $srcwidth = $height;
                           $srcheight = $height;
                           
                        } else {
                           
                           $srctop = ($height / 2)-($width / 2);
                           $srcleft = 0;
                           $srcwidth = $width;
                           $srcheight = $width;
                           
                        }
                        
                        $imgwidth = $newwidth;
                        $imgheight = $newheight;
                        
                        break;
                        
                     case 'aspect':
                        
                        // define new coordinates
                        $srctop = 0;
                        $srcleft = 0;
                        $srcwidth = $width;
                        $srcheight = $height;
                        
                        if( $width > $height ) {
                           
                           $newwidth = $size;
                           $newheight = ( $height / $width ) * $size;
                           
                        } else {
                           
                           $newheight = $size;
                           $newwidth = ( $width / $height ) * $size;
                           
                        }
                        
                        $newtop = 0;
                        $newleft = 0;
                        $imgwidth = $newwidth;
                        $imgheight = $newheight;
                        
                        break;
                        
                     case 'squareaspect':
                        
                        // define new coordinates
                        $imgwidth = $size;
                        $imgheight = $size;
                        
                        $srctop = 0;
                        $srcleft = 0;
                        $srcwidth = $width;
                        $srcheight = $height;
                        
                        if( $width > $height ) {
                           
                           $newwidth = $size;
                           $newheight = ( $height / $width ) * $size;
                           
                        } else {
                           
                           $newheight = $size;
                           $newwidth = ( $width / $height ) * $size;
                           
                        }
                        
                        $newtop = ($imgheight / 2) - ($newheight / 2);
                        $newleft = ($imgwidth / 2) - ($newwidth / 2);
                        
                        break;
                        
                     case 'box':
                        
                        list( $boxwidth, $boxheight ) = explode( 'x', $size );
                        
                        $srctop = 0;
                        $srcleft = 0;
                        $srcwidth = $width;
                        $srcheight = $height;
                        
                        if( $width > $height ) {
                           
                           $newwidth = $boxwidth;
                           $newheight = ( $height / $width ) * $boxwidth;
                           
                           if( $newheight > $boxheight ) {
                              
                              $newheight = $boxheight;
                              $newwidth = ( $width / $height ) * $boxheight;
                              
                           }
                           
                        } else {
                           
                           $newheight = $boxheight;
                           $newwidth = ( $width / $height ) * $boxheight;
                           
                           if( $newwidth > $boxwidth ) {
                              
                              $newwidth = $boxwidth;
                              $newheight = ( $height / $width ) * $boxwidth;
                              
                           }
                           
                        }
                        
                        $newtop = 0;
                        $newleft = 0;
                        $imgwidth = $newwidth;
                        $imgheight = $newheight;
                        
                        break;
                        
                     case 'width':
                        
                        // limit the maximum scale to size of image 
                        if( $width < $size ) {
                           $size = $width;
                        }
                        
                        // define new coordinates
                        $newtop = 0;
                        $newleft = 0;
                        $newwidth = $size;
                        $newheight = round( $size * ($height/$width) );
                        
                        $srctop = 0;
                        $srcleft = 0;
                        $srcwidth = $width;
                        $srcheight = $height;
                        
                        $imgwidth = $newwidth;
                        $imgheight = $newheight;
                        
                        break;
                        
                     case 'height':
                        
                        // limit the maximum scale to size of image 
                        if( $height < $size ) {
                           $size = $height;
                        }
                        
                        // define new coordinates
                        $newtop = 0;
                        $newleft = 0;
                        $newwidth = round( $size * ($width/$height) );
                        $newheight = $size;
                        
                        $srctop = 0;
                        $srcleft = 0;
                        $srcwidth = $width;
                        $srcheight = $height;
                        
                        $imgwidth = $newwidth;
                        $imgheight = $newheight;
                        
                        break;
                  }
                  
                  // make sure output 
                  $imgwidth = max( 1, $imgwidth );
                  $imgheight = max( 1, $imgheight );
                  
                  // create the output image
                  $target = imagecreatetruecolor( $imgwidth, $imgheight );
                  
                  $white = imagecolorallocate( $target, 0xFF, 0xFF, 0xFF );
                  imagefilledrectangle( $target, 0, 0, $imgwidth, $imgheight, $white );
                  
                  // set the alphablending bit
                  imagealphablending( $target, $this->alphascaling );
                  
                  // resample the image to the target
                  imagecopyresampled( $target, 
                                      $image, 
                                      $newleft, 
                                      $newtop, 
                                      $srcleft, 
                                      $srctop, 
                                      $newwidth, 
                                      $newheight, 
                                      $srcwidth, 
                                      $srcheight );
                  
                  // make sure to save alpha information
                  imagesavealpha( $target, true );
                  
                  
                  if( $negate ){
                     imagefilter($target, IMG_FILTER_NEGATE);
                  }
                  
                  
                  // start a clean buffer
                  ob_start();
                  
                  // write out in the source format
                  switch( $extension ) {
                  
                     case 'jpg':
                     case 'jpeg':
                        imagejpeg( $target, NULL, 90 );
                        break;
                     case 'gif':
                        imagegif( $target );
                        break;
                     case 'png':
                        imagepng( $target );
                        break;
                     
                  }
                  
                  // fetch the filedata and clean up
                  $filedata = ob_get_clean();
                  
                  // fetch the filesize
                  $filesize = strlen( $filedata );
                  
                  // draw a content-length header
                  header( 'Content-Length: '.$filesize );
                  
                  // output the filedata
                  echo $filedata;
                  
                  // write the filedata to the cache
                  $cache->write( $thumbname, $filedata );
                  
               }
               
            }
            
         } else {
            
            header( 'HTTP/1.0 404 Not Found' );
            
            $this->setTemplate( 'errors.notfound' );
            
         }
         
      }
      
      /**
       * Streams an image file from disk, ensuring proper cache headers
       *
       * @param string $image The image file to stream
       */           
      public function Execute( $image = '' , $language = '') {
        
         
         if( empty( $language ) ){
             $language = Session::get( 'language');
         }
        
         
         $resourcepath = sprintf( $this->resourcepath . "/%s" , getRootPath(), $language , $image );
         
         if( !file_exists( $resourcepath ) ){
            // prepare the resource paths
            $resourcepath = sprintf( $this->resourcepath, getRootPath(), $image );
         }
         
         
         
         // make sure it actually exists
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
               #header( "ETag: \"$hash\"");
               header( "Accept-Ranges: bytes");
               #header( "Content-Disposition: inline; filename=\"$image\";" );
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
                  case 'xml':
                     header( 'Content-Type: text/xml; charset=utf-8' );
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
