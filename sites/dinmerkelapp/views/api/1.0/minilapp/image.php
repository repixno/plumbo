<?php

   
   /**
    * 
    * @author Tor Inge
    * 
    */

   // Needed to find path to fonts
   chdir( '/var/www/repix/data/fonts' );

   import( 'pages.json' );

   class CreateImageText extends JSONPage implements NoAuthRequired, IValidatedView {
      
      private $fontfoder = '/var/www/repix/data/fonts/';
      
      private $width = 360;
      private $height = 154;
      
      
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
                  'line1' => VALIDATE_STRING,
                  'line2' => VALIDATE_STRING,
                  'line3' => VALIDATE_STRING,
                  'font' => VALIDATE_STRING,
                  'gravity' => VALIDATE_STRING
               )
            )
         );
         
      }

      /**
       * Execute
       * 
       * @author Tor Inge Løvland
       */
      public function Execute() {
         
         // Move this to settings?
         $tmplocation = '/tmp';
         
         // Set the temp filename
         $tmpname = tempnam( "$tmplocation","txt" );
         $tmpname = basename( $tmpname );
         
         // Properties set by user
         $line1    = isset( $_GET['line1'] ) ? $_GET['line1'] : null;
         $line2   = isset( $_GET['line2'] ) ? $_GET['line2'] : null;
         $line3    = isset( $_GET['line3'] ) ? $_GET['line3'] : null;
         $gravity = isset( $_GET['gravity'] ) ? $_GET['gravity'] : 'center';
         $font    = isset( $_GET['font'] ) ? $_GET['font'] : null;
         
         
         // Setup filenames
         $filename = $tmplocation."/".$tmpname."".$id.".gif";
         $tmpfile = $tmplocation."/".$tmpname."".$id.".png";
         
         //if( !isset( $font ) ) die(); 
         //if( !isset( $text ) ) die();
         
         // The actual font name
         $font = $font.'.ttf';
         
         $text = $line1;
         
         $text = $line1;
         if( !empty( $line2) ){
            $text .= "\n" . $line2;
         }
         if( !empty( $line3 ) ){
            $text .=  "\n" . $line3;
         }
         
         
         $this->writeText( $text, $gravity, $font);
         
         die();
         
      }
      
      
      
   }



?>