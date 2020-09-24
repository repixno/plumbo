<?php
   /**
    * creates weekdays for ukeplan
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no> / Tor Inge Løvland <tor.inge@eurofoto.no>
    * 
    */
   
   //chdir( '/var/www/repix/data/fonts' );

   import( 'pages.json' );

   class CreateImageText extends JSONPage implements NoAuthRequired, IValidatedView {
      
      private $fontfoder = '/var/www/repix/other/fonts/ukeplan/';
      
      
      /**
       * Validate incoming data
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'get' => array(
                  'text' => VALIDATE_STRING,
                  'font' => VALIDATE_STRING,
                  'orientation' => VALIDATE_STRING,
                  'color' => VALIDATE_STRING,
                  'background' => VALIDATE_STRING,
                  'quality' => VALIDATE_INTEGER,
                  'png' => VALIDATE_INTEGER,
                  'width' => VALIDATE_INTEGER,
                  'height'=> VALIDATE_INTEGER,
                  'rotate' => VALIDATE_STRING,
                  'align' => VALIDATE_STRING
               )
            )
         );
         
      }
      
      
      public function Execute() {
         
         // Move this to settings?
         $tmplocation = '/tmp';
         
         // Set the temp filename
         $tmpname = tempnam( "$tmplocation","txt" );
         $tmpname = basename( $tmpname );
         
         // Properties set by user
         $text    = isset( $_GET['text'] ) ? $_GET['text'] : null;
         $orientation = isset( $_GET['orientation'] ) ? $_GET['orientation'] : 'portrait';
         $font    = isset( $_GET['font'] ) ? $_GET['font'] : null;
         $quality = isset( $_GET['quality'] ) ? 1 : 0;
         $color   = isset( $_GET['color'] ) ? $_GET['color'] : '000000';
         $background   = isset( $_GET['background'] ) ? $_GET['background'] : 'FFFFFF';
         $png     = isset( $_GET['png'] ) ? $_GET['png'] : 0;
         $width   = isset( $_GET['width'] ) ? $_GET['width'] : 110;
         $height   = isset( $_GET['height'] ) ? $_GET['height'] : 36;
         $rotate   = isset( $_GET['rotate'] ) ? $_GET['rotate'] : '-90';
         $align   = isset( $_GET['align'] ) ? $_GET['align'] : 'center';

         // If user indicated better quality the change to pintsize 72
         $pointsize = 36;
         if( $quality ) $pointsize = 72;
         
         // Don't know what this is for, seems to never change
         $id = 0;
         
         // Special settings for font shelly_script_adante
         if( $font == 'shelly_script_adante' ) {
            $pointsize = 72;
            if( $quality ) {
               $pointsize = 144;
            }
         }
         
         // Setup filenames
         $filename = $tmplocation."/".$tmpname."".$id.".gif";
         $tmpfile = $tmplocation."/".$tmpname."".$id.".png";
         
         if( !isset( $font ) ) die(); 
         if( !isset( $text ) ) die();
         
         // The actual font name
         $font = $font.'.ttf';
         
         $this->writeText( $text, $pointsize, $color, $background, $orientation, $font, $tmpfile, $filename, $png, $width, $height, $rotate, $align );
         
         die();
         
      }

      private function writeText( $text, $pointsize, $color, $background,  $orientation, $font, $tmpfile, $filename, $png, $width, $height, $rotate, $align ) {
         
            $font = 'Century_Gothic.TTF';

            /* Create some objects */
            $image = new Imagick();
            $draw = new ImagickDraw();
           
            $pixel = new ImagickPixel( "#" . $background );
            /* New image */
            $image->newImage($width ,$height, $pixel);
            
            $pixel = new ImagickPixel( "#" . $color );
            /* Black text */
            $draw->setFillColor($pixel);
            
            
            $fontratio = $width / $height;
            
            if( $fontratio < 1.2 ){
               $fontratio = 4.5;
            }
            else if( $fontratio < 2 ){
               $fontratio = 3.5;
            }
            else if ( $fontratio > 4.5 ){
               $fontratio = 2;
            }
            else{
               $fontratio = 2.7;
            }
                  
            if( $orientation == 'landscape'  ){
               $fontratio = 7;
               if( $align == 'left' ){
                  $gravity = Imagick::GRAVITY_WEST;
               }else{
                  $gravity = Imagick::GRAVITY_SOUTH;
               }
            }else{
               $gravity = Imagick::GRAVITY_SOUTH;
            }
            /* Font properties */
            
            
            $draw->setFont( $this->fontfoder . $font ); 
            $draw->setFontSize( $width );

            if( $rotate == -90 ){
               /* Create text */
               $draw->setGravity( Imagick::GRAVITY_SOUTH );
               $image->annotateImage($draw, 0 , 0, 0, $text);
               $image->rotateImage(new ImagickPixel(), '-90');
            }
            else if( $rotate == 0 ){
               $draw->setGravity( $gravity );
               $image->rotateImage(new ImagickPixel(), '-90');
               $image->annotateImage($draw, 0 , 0, 0, $text);
            }
            
            /* Give image a format */
            $image->setImageFormat('png');
            
            /* Output the image with headers */
            header('Content-type: image/png');
            echo $image;

      }
      
      
   }



?>