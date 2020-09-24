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
                  'mal' => VALIDATE_STRING,
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
         $mal = isset( $_GET['mal'] ) ? $_GET['mal'] : null;
         try{
            $project = new UserMerkelappOrder( $projectid );
            $project->line1 = $line1;
            $project->line2 = $line2;
            $project->line3 = $line3;
            $project->clipart = '';
            $project->gravity = '';
            $project->font = '';
            $project->image = '';
            $project->treshold = '';
            $project->crop = '';
            $project->backgroundfile = $mal;
            $project->color = '';
            $project->colormode = '';
            $project->save();
         }catch (Exception $e ){
            
         }
         
         // The actual font name
         $font = 'verdanab.ttf';
         
         $text = $line1;
         
         $text = $line1;
         if( !empty( $line2) ){
            $text .= "\n" . $line2;
         }
         if( !empty( $line3 ) ){
            $text .=  "\n" . $line3;
         }
         
         $this->writeText( $projectid, $text, $mal );
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
      private function writeText( $projectid, $text= '', $mal = '') {
         
            $background = new Imagick( '/var/www/repix/sites/dinmerkelapp/webroot/gfx/merkelappsett_template/' . $mal . '.png' );
            
         
            $textplassering = array(
	// med clipart
   
   
   /*
 
                                0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
                            1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
                            2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ), 
                            3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
                            4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
                            5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
                            
                            */
                    
'0'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#ffffff'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#ffffff'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#ffffff'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#ffffff')
),

'1'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'2'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),


'3'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'4'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'5'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),


'6'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#000000')
),
// uten clipart
'7'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#000000')
),
'8'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#000000')
),

'9'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#fffef2'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#15777a'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#6d6e71' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#414042'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#ffffff'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#106366')
),

'10'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#e40303'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#ff8c00'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#ffed00' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#008026'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#004dff'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#750787')
),

'11'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#78c26e'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#66cad8'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#ef575b' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#da0203'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#fee63e'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#ffffff')
),

'12'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#000000')
),

'13'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#ffffff'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#ffffff'),
2=> array( 'x' => 420, 'y' => -5, 'textcolor' => '#ffffff' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#ffffff'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#ffffff'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#ffffff')
),

'14'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
) ,

'15'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'16'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'17'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'18'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'19'=> array(
0=> array( 'x' => 40, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 250, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 40, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 250, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 460, 'y' => 90,'textcolor' => '#000000')
),

'20'=> array(
0=> array( 'x' => 0, 'y' => -5, 'textcolor' => '#000000'),
1=> array( 'x' => 210, 'y' => -5, 'textcolor' => '#000000'),
2=> array( 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ),
3=> array( 'x' => 0, 'y' => 90,'textcolor' => '#000000'),
4=> array( 'x' => 210, 'y' => 90,'textcolor' => '#000000'),
5=> array( 'x' => 420, 'y' => 90,'textcolor' => '#000000')
)
             

);

                
            $malkey = str_replace( 'UB', '', $mal );
                
            foreach( $textplassering[$malkey] as $textmal ){
  
            
            $black = new ImagickPixel( $textmal['textcolor'] );
            $white = new ImagickPixel( none );
            list( $x1, $y1, $width, $height, $colormode2 ) = explode( '-',  $crop );
            $template = new Imagick();
            

            $this->width = 203;
            $this->height = 100;
            $ppi = 300;
            $border = 11;
            $offset = 60;
            $bleed = 10;
            $template->setResolution(300,300);
            $template->newImage( $this->width , $this->height , new ImagickPixel('transparent') );
            
           //$backgroundsrc = '/var/www/repix/sites/static/webroot/gfx/merkelapp/backgroundfiles/' . $backgroundfile;
           
            
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
               $textgravity = Imagick::GRAVITY_CENTER;
               $draw = new ImagickDraw();
               $draw->setFont( $this->fontfoder . "verdanab.ttf" );
              
               $draw->setFontSize( 196 );
               $draw->setGravity( $textgravity );
               $draw->setFillColor( $black );
          //     $draw->setStrokeColor('#ffffff ');
           //    $draw->setStrokeWidth(4);
               $canvas = new Imagick();
               $metrics = $canvas->queryFontMetrics( $draw, $text );
               $canvas->newImage( $metrics['textWidth'], $metrics['textHeight'], $white, "png");
               $canvas->annotateImage($draw,0,0,0,$text);
               $templategeo =  $template->getImageGeometry();
               
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
               
               
               //$template->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
               /*Util::Debug( $template->identifyImage() );
               die();*/
               
               $canvas->clear();
      	      $canvas->destroy();
               $draw->clear();
      	      $draw->destroy();
               //$template->scaleImage( 204, 87 );
               //$previewtemplate = new Imagick( '/var/www/repix/sites/static/cms/images/stempel.jpg' );
               //$previewtemplated = new Imagick( $previewtemplate );
               //$template->compositeImage( $previewtemplated, $previewtemplated->getImageCompose() ,  0 , 0 );
               
               $background->compositeImage( $template, $template->getImageCompose(),  $textmal['x'],  $textmal['y'] );
            }

               if( !file_exists( $orderfolder ) ){
                  mkdir( $orderfolder , 0755 );
               }
               $background->writeImage( $orderfolder . '/' . $projectid . '.png' );
               
               //$previewtemplate->setImageFormat('PNG');
      	       header("Content-Type: image/png");
      	       echo $background;


      }
      
      
   }



?>