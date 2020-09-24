<?
   import( 'storage.util');
   import( 'website.image');   
   config( 'website.conversion' );
 
   /**
    * Handles image effect processing for the Eurofoto Platform
    * 
    * @todo Rewrite this to use the Imagick.so routines instead
    */
   class ImageEffects {
      
      const RETURN_NEWIMAGECOPY = 0;
      const RETURN_TEMPFILENAME = 1;
      
      public function defaultAlbumName() {

         return __( 'Effects' );
      
      }

      static function getEffectlist() {

         return array( array( 'id' => 1, 
                              'title' => 'Sepia', 
                              'translated' => __( 'Sepia' ) 
                       ),
                       array( 'id' => 2, 
                              'title' => 'Monochrome', 
                              'translated' => __( 'Monochrome' ) 
                       ),
                       array( 'id' => 3, 
                              'title' => 'Grayscale', 
                              'translated' => __( 'Grayscale' ) 
                       ),
                       array( 'id' => 4, 
                              'title' => 'Charcoal', 
                              'translated' => __( 'Charcoal') 
                       ),
                       array( 'id' => 5, 
                              'title' => 'Outline', 
                              'translated' => __( 'Outline') ) 
                       ); 
               
      }

      public function processImage( $imageid, $effectid, $albumid, $return = ImageEffects::RETURN_NEWIMAGECOPY, $preview = false ) {
         
         $imagefile = sprintf( '/tmp/tempeffect_in_%s_%s.jpg', $imageid, $effectid );
         $newimagefile = sprintf( '/tmp/tempeffect_out_%s_%s.jpg', $imageid, $effectid );

         $image = new Image( $imageid );
         
         /*
         $fh = fopen( 'storage://eurofoto/'.$imageid.($preview ? '/preview' : ''), 'rb' );

         $im = new Imagick();
         $im->readImageFile( $fh );
         
         switch ( $effectid ) {
            case 1:
               $im->sepiaToneImage( 80 );
               break;
            case 2:
               #exec( sprintf( '%s %s -monochrome %s', $convert, $imagefile, $newimagefile ) );
               break;
            case 3:
               #exec( sprintf( '%s %s -colorspace Gray %s', $convert, $imagefile, $newimagefile ) );
               $im->setColorspace( imagick::COLORSPACE_GRAY );
               break;
            case 4:
               #exec( sprintf( '%s %s -charcoal 5 %s', $convert, $imagefile, $newimagefile ) );
               $im->charcoalImage( 5, 5 );
               break;
            case 5:
               //-segment 1x1 +dither -colors 2 -edge 1 -negate -normalize
               #exec( sprintf( '%s %s -edge 1 -negate -normalize -colorspace Gray -blur 0x.5 -contrast-stretch 0x50%% %s', $convert, $imagefile, $newimagefile ) );
               
               break;
            default:
               copy( $imagefile, $newimagefile );
               break;
         }
         
         
         $newimagecontent = $im->getImageBlob();

         $im->clear();
         $im->destroy();

         fclose( $fh );
         */
         
         if( $preview ) {
            $imagecontent = file_get_contents( 'storage://eurofoto/'.$imageid.'/preview' );
         } else {
            $imagecontent = StorageUtil::readOriginal( 'storage://eurofoto/' . $imageid, true );
         }
         
         file_put_contents( $imagefile, $imagecontent );

         $convert = Settings::Get( 'conversion', 'convert', '/usr/bin/convert' );

         switch ( $effectid ) {
            case 1:
               exec( sprintf( '%s %s -sepia-tone 80%% %s', $convert, $imagefile, $newimagefile ) );
               break;
            case 2:
               exec( sprintf( '%s %s -monochrome %s', $convert, $imagefile, $newimagefile ) );
               break;
            case 3:
               exec( sprintf( '%s %s -colorspace Gray %s', $convert, $imagefile, $newimagefile ) );
               break;
            case 4:
               exec( sprintf( '%s %s -charcoal 5 %s', $convert, $imagefile, $newimagefile ) );
               break;
            case 5:
               //-segment 1x1 +dither -colors 2 -edge 1 -negate -normalize
               exec( sprintf( '%s %s -edge 1 -negate -normalize -colorspace Gray -blur 0x.5 -contrast-stretch 0x50%% %s', $convert, $imagefile, $newimagefile ) );
               break;
            default:
               copy( $imagefile, $newimagefile );
               break;
         }
         
         if( $return == ImageEffects::RETURN_NEWIMAGECOPY ) {
            
            $newimagecontent = file_get_contents( $newimagefile );
            
            try {
            
               unlink( $imagefile );
               unlink( $newimagefile ); 
               
            } catch ( Exception $e ) {
               
            }
            
            return StorageUtil::uploadImageString( Login::userid(), $albumid, $newimagecontent, 'image/jpeg', $image->tittel, $image->tekst );
            
         } else {
            
            try {
            
               unlink( $imagefile );
               
            } catch ( Exception $e ) {
               
            }
            
            return $newimagefile;
            
         }

      }
      
   }
   
?>
