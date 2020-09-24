<?PHP

   /******************************************
    *TEST SCRIPT FOR ADDING CUTMARKS
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );

   class MerkelappImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/stabburet/test/';
      
      Public function Main(){
            
            $imgpos = explode( ',' , "244.31931166347994, 269.585086042065, 657.3613766730401, 490.82982791587" );
            
            $dx = $imgpos[2] * 2;
            $dy = $imgpos[3] * 2;
            $x = ( $imgpos[0] * 2 ) - ( $dx / 2 );
            $y = ( $imgpos[1] * 2 ) - ( $dy / 2 );
            $text = ' STABBURET ';
            $white = new ImagickPixel('white');
            $fontsize = 90;
            
            
            $image = new Imagick();
            $image->newImage(878, 878, $white );
            
            $userimage = new Imagick( $this->webspoolFolder . 'images/IMG_1107.JPG' );
            $userimage->scaleImage( $dx,  $dy );
            
            $template = new Imagick( $this->webspoolFolder . 'liten-utentekst.png' );
            
            $image->compositeImage( $userimage, $userimage->getImageCompose() , $x , $y );
            $image->compositeImage( $template, $template->getImageCompose() , 0 , 0 );
            
            
            
            $draw = new ImagickDraw();
            $draw->setFillColor('white');
            $draw->setFontWeight (800);
            //$draw->setFont('Bookman-DemiItalic');
            
            
            $canvas = new Imagick();
            $metrics = $canvas->queryFontMetrics( $draw, $text );
            
            
            //system( "convert -pointsize 220 label:' LEVERPOSTEI ' xc:none  -rotate 180   -distort Arc '120 180'  '/home/produksjon/stabburet/test/arc_flip.png'" );
            
            $textimage = new Imagick();
            $textimage->newImage( 878, 878, new ImagickPixel( none ) );
            //$textimage->annotateImage($draw, 400 , 400, 180, $text);
            
            $letters = str_split($text);
            foreach( $letters as $letter ){
                $metricsl = $canvas->queryFontMetrics( $draw, $letter );
                $fontsize += 0.5;
                $draw->setFontSize( $fontsize  );
                $textimage->rotateImage(new ImagickPixel('none'), $rotate);
                $rotate = ( $metricsl['textWidth'] / 4.5 )  + 4 ;
                $rotate = min( 13, $rotate );
                $textimage->cropImage( 878, 878 , 0 , 0  );
                $textimage->annotateImage($draw, 405 , 763, 0, $letter);
                $totalrotate +=  $rotate;
            }
            
            $textimage->rotateImage(new ImagickPixel('none'), -( $totalrotate / 2 ) );
            $textimage->cropImage( 878, 878 , 0 , 0  );
            
            $image->compositeImage( $textimage, $textimage->getImageCompose() , 0 , 0 );
            
            
            
            $image->writeImage ( $this->webspoolFolder . "test.jpg" );
            
            
            
            
      }
   
   
   }
   

   CLI::Execute();

?>