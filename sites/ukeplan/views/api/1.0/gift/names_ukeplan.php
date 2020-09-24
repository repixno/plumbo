<?php
   /**
    * creates names for ukeplan
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no> / Tor Inge Løvland <tor.inge@eurofoto.no>
    * 
    */

   // Needed to find path to fonts
   chdir( '/var/www/repix/data/fonts' );

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
                  'gravity' => VALIDATE_STRING,
                  'color' => VALIDATE_STRING,
                  'background' => VALIDATE_STRING,
                  'quality' => VALIDATE_INTEGER,
                  'png' => VALIDATE_INTEGER,
                  'width' => VALIDATE_INTEGER,
                  'height' => VALIDATE_INTEGER
               )
            )
         );
         
      }
      
      
      /**
       * Execute
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function Execute() {
         
         // Move this to settings?
         $tmplocation = '/tmp';
         
         // Set the temp filename
         $tmpname = tempnam( "$tmplocation","txt" );
         $tmpname = basename( $tmpname );
         
         // Properties set by user
         $text    = isset( $_GET['text'] ) ? $_GET['text'] : null;
         $gravity = isset( $_GET['gravity'] ) ? $_GET['gravity'] : 'center';
         $font    = isset( $_GET['font'] ) ? $_GET['font'] : null;
         $quality = isset( $_GET['quality'] ) ? 1 : 0;
         $color   = isset( $_GET['color'] ) ? $_GET['color'] : '000000';
         $background   = isset( $_GET['background'] ) ? $_GET['background'] : 'FFFFFF';
         $png     = isset( $_GET['png'] ) ? $_GET['png'] : 0;
         $width   = isset( $_GET['width'] ) ? $_GET['width'] : 110;
         $height  = isset( $_GET['height'] ) ? $_GET['height'] : 110;
         
         
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
         $font = $font;
         
         // Right, time to escape text and fix linebreaks
         $text = $this->replaceTextAndFixLineBreaks( $text );
         $this->writeText( $text, $pointsize, $color, $background, $gravity, $font, $tmpfile, $filename, $png, $width, $height );
         
         die();
         
      }
      
      
      /**
       * Write the text to disc and output the image to user
       *
       * @param string $text
       * @param integer $pointsize
       * @param string $color
       * @param string $gravity
       * @param string $font
       * @param string $tmpfile
       * @param string $filename
       * @param integer $png
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function writeText( $text, $pointsize, $color, $background, $gravity, $font, $tmpfile, $filename, $png, $width, $height ) {
         
            //$font = 'Century_Gothic.TTF';
            $x = 0;
            
            if( $font == 'greyscale.ttf' || $font  == 'quicksand.otf' ){
               $x = 2;
            }
            
            $pointsize = $height - 4;
            
            /* Create some objects */
            $image = new Imagick();
            $draw = new ImagickDraw();
           
            $pixel = new ImagickPixel( "#" . $background );
            
            /* New image */
            $image->newImage( $width, $height, $pixel );
            $image->setImageOpacity(0.5);
            
            $pixel = new ImagickPixel( "#" . $color );
            /* Black text */
            
            
            $draw->setFillColor($pixel);
            /* Font properties */
            $draw->setGravity( Imagick::GRAVITY_CENTER );
            $draw->setFont( $this->fontfoder . $font );
            $draw->setFontSize( $pointsize );
            
            /* Create text */
            $image->annotateImage($draw, 0 , -1 +  $x , 0, $text);
            
            /* Give image a format */
            $image->setImageFormat('png');
            
            /* Output the image with headers */
            header('Content-type: image/png');
            echo $image;

      }
      
      
      /**
       * Replace some important letters and add linebreak
       *
       * @param string $text
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function replaceTextAndFixLineBreaks( $text ) {
         
         $text = stripslashes( html_entity_decode( str_replace( "XXOGXX","&", $text ) ) );
         $text = str_replace( "XXNYLINJEXX", "\n", $text );
         return $text;
         
      }
      
   }



?>