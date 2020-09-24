<?php

   
   /**
    * 
    * @author Tor Inge
    * 
    */

   // Needed to find path to fonts
   chdir( '/var/www/repix/data/fonts' );

   import( 'pages.json' );
   import( 'website.order.merkelapporder' );
    import( 'math.simple' );

   class CreateImageText extends JSONPage implements NoAuthRequired, IValidatedView {
      
      private $fontfoder = '/var/www/repix/data/fonts/';
      private $tmpfolder = '/home/www/tmpbilder/';
      private $orderfolder = '/data/global/merkelapp/';
      private $width = 718;
      private $height = 307;
      
      
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
                  'projectid' => VALIDATE_STRING,
                  'line1' => VALIDATE_STRING,
                  'line2' => VALIDATE_STRING,
                  'line3' => VALIDATE_STRING,
                  'font' => VALIDATE_STRING,
                  'gravity' => VALIDATE_STRING,
                  'clipart' => VALIDATE_STRING,
                  'image' => VALIDATE_STRING,
                  'treshold' => VALIDATE_STRING,
                  'crop'     => VALIDATE_STRING,
                  'color'    => VALIDATE_STRING,
                  'backgroundfile' => VALIDATE_STRING,
                  'colormode' => VALIDATE_STRING,
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
         $projectid    = isset( $_GET['projectid'] ) ? $_GET['projectid'] : '';
         $line1    = isset( $_GET['line1'] ) ? $_GET['line1'] : '';
         $line2   = isset( $_GET['line2'] ) ? $_GET['line2'] : '';
         $line3    = isset( $_GET['line3'] ) ? $_GET['line3'] : '';
         $gravity = isset( $_GET['gravity'] ) ? $_GET['gravity'] : 'center';
         $font    = isset( $_GET['font'] ) ? $_GET['font'] : null;
         $clipart = isset( $_GET['clipart'] ) ? $_GET['clipart'] : null;
         $image = isset( $_GET['image'] ) ? $_GET['image'] : null;
         $treshold = isset( $_GET['treshold'] ) ? $_GET['treshold'] : null;
         $crop = isset( $_GET['crop'] ) ? $_GET['crop'] : null;
         $backgroundfile = isset( $_GET['backgroundfile'] ) ? $_GET['backgroundfile'] : null;
         $color = $_GET['color'] != 'undefined' ? urldecode( $_GET['color'] ) : "000000";
         $colormode = isset( $_GET['colormode'] ) ? $_GET['colormode'] : null;
         
         //stupid hack
         $color = str_replace( '#', '', $color );
         $color = "#" . $color;
         
        

         $clipart = str_replace( '-', '/', $clipart );
         $backgroundfile = str_replace( '-', '/', $backgroundfile );

         try{
            $project = new UserMerkelappOrder( $projectid );
            $project->line1 = $line1;
            $project->line2 = $line2;
            $project->line3 = $line3;
            $project->clipart = $clipart;
            $project->gravity = $gravity;
            $project->font = $font;
            $project->image = $image;
            $project->treshold = $treshold;
            $project->crop = $crop;
            $project->backgroundfile = $backgroundfile;
            $project->color = $color;
            $project->colormode = $colormode;
            $project->save();
            
            
         }catch (Exception $e ){
            
         }
         
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
         
         $this->writeText( $projectid, $text, $gravity, $font, $clipart, $image, $treshold, $crop, $color , $backgroundfile, $colormode );
         die();
         
      }
      
      
      /**
       * Write the merkelapp to disc and output the image to user
       *
       * @param string $line1
       * @param string $line2
       * @param string $line3
       * @param string $gravity
       * @param string $font
       * @param string $clipart
       * @param string $image
       * @param string $treshold
       * @param string $crop
       * 
       * @author Tor Inge Lovland <tor.inge@eurofoto.no>
       */
      private function writeText( $projectid, $text= '', $gravity='', $font='', $clipart='', $image='', $treshold=2, $crop=0 , $color = "", $backgroundfile = '', $colormode = '') {
         
            $black = new ImagickPixel( $color );
            $white = new ImagickPixel( none );
            list( $x1, $y1, $width, $height, $colormode2 ) = explode( '-',  $crop );
            $template = new Imagick();
            
            if( $colormode == 'color' ){
               $this->width = 354;
               $this->height = 165;
               $ppi = 300;
               $border = 11;
               $offset = 60;
               $bleed = 10;
               $previewheight = 87;
               $template->setResolution(300,300);
               $template->newImage( $this->width + ( $bleed * 2 ) , $this->height + ( $bleed * 2 ) , new ImagickPixel('white') );
               $previewtemplate = '/var/www/repix/sites/static/webroot/gfx/merkelapp/template_merkelapp2.png';
            }
            else if( $colormode == 'stempel' ){
               $this->width = 874;
               $this->height = 331;
               $ppi = 600;
               $leftmargin = 20;
               $border = 14;
               $offset = 100;
               $bleed = 0;
               $previewheight = 75;
               $template->setResolution(600,600);
               $template->newImage($this->width, $this->height, new ImagickPixel('white'));
               $previewtemplate = '/var/www/repix/sites/static/webroot/cms/images/stempel2.png';
               
            }
            else{
               $border = 22;
               $offset = 120;
               $ppi = 600;
               $bleed = 0;
               $previewheight = 87;
               $template->setResolution(600,600);
               $template->newImage($this->width, $this->height, new ImagickPixel('white'));
               $previewtemplate = '/var/www/repix/sites/static/webroot/gfx/merkelapp/template_merkelapp2.png';
            }
            
            if( !empty( $backgroundfile ) ){
               $backgroundsrc = '/var/www/repix/sites/static/webroot/gfx/merkelapp/backgroundfiles/' . $backgroundfile;
               if( file_exists( $backgroundsrc ) ){
                  $background = new Imagick( $backgroundsrc );
                  $background->scaleImage( $template->getImageWidth() ,  $template->getImageWidth());
                  $template->compositeImage( $background, $background->getImageCompose(),  0,  0 );
               }  
            }
            
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
               $draw->setFont( $this->fontfoder . $font );
               $draw->setFontSize( 196 );
               $draw->setGravity( $textgravity );
               $draw->setFillColor( $black );
               $canvas = new Imagick();
               $metrics = $canvas->queryFontMetrics( $draw, $text );
               $canvas->newImage( $metrics['textWidth'], $metrics['textHeight'], $white, "png");
               $canvas->annotateImage($draw,0,0,0,$text);
               $templategeo =  $template->getImageGeometry();

               
               if( !empty( $image )  || !empty( $clipart ) ){
                     if( !empty( $image ) ){
                         $src = $this->tmpfolder . $image;
                         if( $width > 0 ){ 
                            $cropimage = new Imagick( $src );
                            $ratio = ( $cropimage->getImageWidth() / 254 );

                            $x1 = $ratio * $x1;
                            $y1 = $ratio * $y1;
                            $width = $ratio * $width;
                            $height = $ratio * $height;
                            
                            $cropimage->cropImage ( $width, $height, $x1, $y1);
                            $src = $src . 'cropped.jpg';
                            $cropimage->writeImage( $src );
                     }
                     
                     if( $colormode != 'color' ){
                        exec( sprintf( 'convert %s -threshold %s %s', $src, $treshold . '%' , $src . 'trs.png' ) );
                        $src = $src . 'trs.png';
                     }
                     }else{
                        if( $colormode != 'color' ){
                           $src = '/var/www/repix/sites/static/webroot/gfx/merkelapp/clipart/' . $clipart;
                        }else{
                         $src = '/var/www/repix/sites/static/webroot/gfx/merkelapp/colorclipart/' . $clipart;
                        }
                     }

                     $ownimage = new Imagick( $src );
                     $ownimage->scaleImage( $templategeo['height'] - $offset - ( $bleed * 2 ), $templategeo['height'] - $offset - ( $bleed * 2 ) );
                     /*if( $image ){
                         $max = $ownimage->getQuantumRange();
                         $max = $max[quantumRangeLong];

                         $ownimage->randomThresholdImage( $max/$treshold, $max/$treshold );
                     }*/

                     $textoffset = $templategeo['height'] - $offset - ( $bleed * 2 ) + $leftmargin;

                     $template->compositeImage( $ownimage, $ownimage->getImageCompose() ,  $border + $bleed  + $leftmargin ,  ( $offset / 2 )  + $bleed );
                }
               
               $textratio =  $metrics['textWidth'] / $metrics['textHeight'];
               $texplaceholderwidth = $templategeo['width'] - $textoffset - ( $border  * 2 ) - ( $offset / 3 )  - ( $bleed * 2  ) ;
               $textplaceholderheight = ( $templategeo['height'] ) - ( $border * 2 );
               $templateratio =  $texplaceholderwidth  / $textplaceholderheight;

               if ( $textratio > $templateratio ){
                  $canvas->scaleImage( $texplaceholderwidth - $bleed , 0);
               }
               else{
                  $canvas->scaleImage( 0,$textplaceholderheight - $bleed );
               }
               
               if( $textoffset ){
                  $templategeo['width'] = $templategeo['width'] - $textoffset;  
               }
               
               if( $gravity == 'left' ){
                  $sizeoffset = $border;
               }
               else if( $gravity == 'right' ){
                  $sizeoffset = ( $templategeo['width'] - $canvas->getImageWidth() - ( $border *  3 ) - ( $bleed  * 2 ) );
               }else{
                  $sizeoffset = ( ( $templategeo['width'] - $canvas->getImageWidth() ) / 2  ) - $border - ( $bleed  * 2 ) ;
               }
               $topmargin = ( ( $templategeo['height'] - ( $bleed * 2 ) - $canvas->getImageHeight() ) / 2 );
               $canvas->setImageFormat('PNG');
               $template->compositeImage( $canvas, $canvas->getImageCompose() ,  $border + $textoffset + $sizeoffset + $bleed ,  $topmargin + $bleed  );
               $orderfolder =  $this->orderfolder . date( 'Y-m-d');
               
               if( !file_exists( $orderfolder ) ){
                  mkdir( $orderfolder , 0755 );
               }
               $template->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
               /*Util::Debug( $template->identifyImage() );
               die();*/
               
               $template->writeImage( $orderfolder . '/' . $projectid . '.png' );
               $canvas->clear();
      	       $canvas->destroy();
               $draw->clear();
      	       $draw->destroy();
               $template->cropImage( $this->width , $this->height, $bleed, $bleed  );
               //$template->scaleImage( 204, 87 );
               $template->scaleImage( 204, $previewheight );
               //$previewtemplate = new Imagick( '/var/www/repix/sites/static/cms/images/stempel.jpg' );
               $previewtemplated = new Imagick( $previewtemplate );
               $template->compositeImage( $previewtemplated, $previewtemplated->getImageCompose() ,  0 , 0 );
               
               //$previewtemplate->setImageFormat('PNG');
      	       header("Content-Type: image/png");
      	       echo $template;


      }
      
      
   }



?>
