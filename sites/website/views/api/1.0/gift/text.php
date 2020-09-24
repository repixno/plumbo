<?php

   
   /**
    * Creates and saves a text to image on disc and
    * writes output to user.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   // Needed to find path to fonts
   chdir( '/var/www/repix/data/fonts' );

   import( 'pages.json' );

   class CreateImageText extends JSONPage implements NoAuthRequired, IValidatedView {
      
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
                  'quality' => VALIDATE_INTEGER,
                  'png' => VALIDATE_INTEGER,
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
         $png     = isset( $_GET['png'] ) ? $_GET['png'] : 0;
         
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
         
         // Right, time to escape text and fix linebreaks
         $text = $this->replaceTextAndFixLineBreaks( $text );
         $this->writeText( $text, $pointsize, $color, $gravity, $font, $tmpfile, $filename, $png );
         
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
      private function writeText( $text, $pointsize, $color, $gravity, $font, $tmpfile, $filename, $png ) {
         
         // Execute the command to create the text
         $command = "convert -background transparent -pointsize $pointsize -fill \"#$color\" -font $font -gravity $gravity label:\"".$text."\" -trim +repage $tmpfile";
         exec( $command );
         
         // Convert gif to png
         $command = "convert $tmpfile $filename";
         exec( $command );
         
         // Read out the file
         if( file_exists( $tmpfile ) && $png == 1 ) {
         	Header( "Content-type: image/png" );
         	readfile( $tmpfile );
         }
         
         if( file_exists( $filename ) ){
         	Header( "Content-type: image/gif" );
         	readfile( $filename );
         }
         
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