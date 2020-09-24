<?php

   
   /**
    * 
    * @author Tor Inge
    * 
    */

   import( 'pages.json' );

   class CreateTopText extends JSONPage implements NoAuthRequired, IValidatedView {
      
      private $fontfolder = '/var/www/repix/other/fonts/ukeplan/';
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'get' => array(
                  'projectid' => VALIDATE_STRING,
                  'text'   => VALIDATE_STRING,
                  'width' => VALIDATE_STRING,
                  'height' => VALIDATE_STRING,
                  'color' => VALIDATE_STRING,
                  'background' => VALIDATE_STRING,
                  'font' => VALIDATE_STRING,
                  'gravity' => VALIDATE_STRING,
				  'bottom_margin' => VALIDATE_STRING
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
		 
         // Properties set by user
         $projectid    = isset( $_GET['projectid'] ) ? $_GET['projectid'] : '1';
         $text    = isset( $_GET['text'] ) ? $_GET['text'] : '';
         $color   = isset( $_GET['color'] ) ? $_GET['color'] : 'black';
         $background   = isset( $_GET['background'] ) ? $_GET['background'] : 'white';
         $font    = isset( $_GET['font'] ) ? $_GET['font'] : 'crulz';
         $gravity = isset( $_GET['gravity'] ) ? $_GET['gravity'] : 'center';
         $width = isset( $_GET['width'] ) ? $_GET['width'] : '400';
         $height = isset( $_GET['height'] ) ? $_GET['height'] : '108';
         $bottom_margin = isset( $_GET['bottom_margin'] ) ? $_GET['bottom_margin'] : '25';
               		
			   
         //$this->writeText( $projectid, $text, $gravity, $font, $width, $height );
         $this->writeText( 1, $text, $gravity, $font,$color, $background , $width, $height, $bottom_margin );
         die();
         
      }
      
      
      /**
       * Write the merkelapp to disc and output the image to user
       *
       * 
       * @author Tor Inge Lovland <tor.inge@eurofoto.no>
       */
      private function writeText( $projectid, $text, $gravity, $font, $color, $background, $width, $height, $bottom_margin ) {
         
         $border = 22;
         $offset = 120;
         
         $textcolor = new ImagickPixel( $color );
         $white = new ImagickPixel( none );

         $template = new Imagick();
         $template->newImage($width, $height, new ImagickPixel(none));
         $template->setImageFormat('png');
   
         
            if( $gravity == 'right' ){
               $textgravity = Imagick::GRAVITY_EAST;
            }
            else if( $gravity == 'center' ){
               $textgravity = Imagick::GRAVITY_CENTER;
            }else{
               $textgravity = Imagick::GRAVITY_WEST;
            }
            
            if( empty( $text ) ){
               $text = ' ';
            }
            $draw = new ImagickDraw();
      		$draw->setFont( $this->fontfolder . $font );
      		$draw->setFontSize( 196 );
      		$draw->setGravity( $textgravity );
      		$draw->setFillColor( $textcolor );
       
      		$canvas = new Imagick();      		
      		$metrics = $canvas->queryFontMetrics( $draw, $text );
   
      		$canvas->newImage( $metrics['textWidth'] + 50 , $metrics['textHeight'] + 60, $white, "png");
      		$canvas->annotateImage($draw,30, 30,0,$text);
      		$templategeo =  $template->getImageGeometry();
      		
            $textratio =  $metrics['textWidth'] / $metrics['textHeight'];
            $texplaceholderwidth = $width ;
            $textplaceholderheight = $height - $bottom_margin;
            $templateratio =  $texplaceholderwidth  / $textplaceholderheight;
   
            if ( $textratio > $templateratio ){
               $canvas->scaleImage( $texplaceholderwidth , 0);
            }
            else{
               $canvas->scaleImage( 0,$textplaceholderheight);
            }

            if( $gravity == 'left' ){
               $sizeoffset = 5;
            }
            else if( $gravity == 'right' ){
               $sizeoffset = ( $templategeo['width'] - $canvas->getImageWidth() - ( $border * 2 ) );
            }else{
               $sizeoffset = ( $templategeo['width'] - $canvas->getImageWidth() ) / 2;
            }
   
            $topmargin = 0;
   
      	    $canvas->setImageFormat('PNG');
   
            $template->compositeImage( $canvas, $canvas->getImageCompose() , $sizeoffset,  $topmargin );
            

            header("Content-Type: image/png");
      		echo $template;
            
            $canvas->clear();
			$canvas->destroy();
			$draw->clear();
			$draw->destroy();
			$template->clear();
			$template->destroy();

      }
      
      
   }



?>