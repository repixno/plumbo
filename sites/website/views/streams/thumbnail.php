<?php
   
   import( 'cache.diskcache' );
   import( 'storage.stream' );
   import( 'website.image' );
   import( 'media.image.scaling' );
   
   class StreamThumbnail extends WebPage implements IValidatedView {
      
      protected $template = false;
      protected $alphascaling = false;
      
      const MAX_SIZEX = 400;
      const MAX_SIZEY = 400;
      
      public function Validate() {
         
         return array( 
            'execute' => array(
               'fields' => array(
                  'sharekey' => VALIDATE_STRING,
                  'type' => VALIDATE_STRING,
                  'size' => VALIDATE_STRING,
                  'imageid' => VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      /**
       * Thumbnails an existing image on disk or reads a cached copy from cache
       *
       * @param string $sharekey The sharing key of this object at the time of URL generation
       * @param string $type Supported types are square, width, height, box (size must be NxN), aspect and squaraspect
       * @param integer $size The target size in pixels
       * @param string $imageid The image ID to make thumbnail from
       */
      public function Execute( $sharekey = '', $type = 'square', $size = 100, $imageid = 0 ) {
         
         try {
            
            if( !$imageid ) throw new Exception( 'Not found', 404 );
            
            $imageobj = new Image( $imageid );
            if( $imageobj instanceof Image ) {
               
               // ensure the sharekey matches!
               if( $imageobj->sharekey =! $sharekey ) {
                  throw new Exception( 'Not found', 404 );
               }
               
               // load the preview stream from disk
               $image = imagecreatefromjpeg( sprintf( 'storage://eurofoto/%s/preview', $imageid ) );

               // find the original sizex/sizey
               $height = imagesy( $image );
               $width = imagesx( $image );
               
               // check if this is a box-style size
               if( strpos( $size, 'x' ) !== false ) {
                  
                  list( $sizex, $sizey ) = explode( 'x', $size );
                  
                  $sizex = min( $sizex, StreamThumbnail::MAX_SIZEX );
                  $sizey = min( $sizey, StreamThumbnail::MAX_SIZEY );
                  $size = sprintF( '%dx%d', $sizex, $sizey );
                  
               } else {
                  
                  $sizex = min( $size, StreamThumbnail::MAX_SIZEX );
                  $sizey = min( $size, StreamThumbnail::MAX_SIZEY );
                  $size = min( $size, StreamThumbnail::MAX_SIZEX );
                  
               }
               
               // if the requested size is larger than the thumbsize
               if ( ( $height < $sizey ) || ( $width < $sizex ) ) {
                  
                  // load the original image instead
                  $image = imagecreatefromjpeg( sprintf( 'storage://eurofoto/%s', $imageid ) );

                  // find the original sizex/sizey
                  $height = imagesy( $image );
                  $width = imagesx( $image );
               
               }
               
               // create a scaling engine
               $scaling = new ImageScaling( $height, $width, $size );
               
               // what thumbnail type to create?
               switch( $type ) {
                  
                  // in case of sqare
                  default:
                     
                  case 'square':

                     $scaling->scaleSquare();
                     
                     break;
                     
                  case 'aspect':
                     
                     $scaling->scaleAspect();
                     
                     break;
                     
                  case 'squareaspect':
                     
                     $scaling->scaleSquareAspect();
                     
                     break;
                     
                  case 'box':
                     
                     $scaling->scaleBox();
                     
                     break;
                     
                  case 'width':
                     
                     $scaling->scaleWidth();
                     
                     break;
                     
                  case 'height':
                     
                     $scaling->scaleHeight();
                     
                     break;

               }

               // start a clean buffer
               ob_start();
               
               imagejpeg( $scaling->scale( $image ), '', 90 );

               // fetch the filedata and clean up
               $filedata = ob_get_clean();
               
               // fetch the filesize
               $filesize = strlen( $filedata );
               
               // clean any existing buffers
               while( ob_get_level() > 0 ) {
                  ob_end_clean();
               }
               
               // draw some content headers
               header( 'Accept-Ranges: bytes' );
               header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + 86400 ) ) );
               header( 'Cache-Control: public' );
               header( 'Pragma: public' );
               header( 'Content-Type: image/jpeg' );
               header( sprintf( 'Content-Length: %s', $filesize ) );
               
               // output the filedata
               echo $filedata;
               
            }
            
         } catch( Exception $e ) {
            
            header( 'HTTP/1.0 404 Not Found' );
            $this->setTemplate( 'errors.notfound' );
            
         }
               
      }
      
   }
   
?>