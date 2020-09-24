<?php
   
   model( 'reedfoto.fotballkort' );
   
   class Fotballkort extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute( $imageid = 0 ) {
        
         $imagick = new Imagick();
         $imagick->newImage( 400 , 586 , new ImagickPixel('white'));
         
         $imagefolder = "/var/www/repix/data/reedfoto/norwaycup/images/2015/";
         
         //$imageid = "ca1192fc";
                            
         $image = new Imagick( $imagefolder . $imageid . '.jpg' );
         $image->scaleImage( 356, 368 , false );
         $imagick->compositeImage( $image, $image->getImageCompose() ,  23 , 76 );
         
         $mal = new Imagick( '/var/www/repix/data/norwaycup/fotballkort/mal.png' ); 
         $imagick->compositeImage( $mal, $mal->getImageCompose() ,  0 , 0 );
         
         
         $pointsize = $height -  ( 4 * $ratio );
                   
         $draw = new ImagickDraw();
         
         $fotballkort = DB::query( "SELECT * FROM project_fotballkort WHERE imageid = ?" , $imageid )->fetchAll( DB::FETCH_ASSOC );
         
         $fotballkort = $fotballkort[0];
         
         $draw->setFillColor( new ImagickPixel( '#32b34c' )  );
         $draw->setFontSize( '28' );
         $draw->setFont( 'Helvetica-Bold' );
         $draw->setGravity ( Imagick::GRAVITY_CENTER );
         $imagick->annotateImage($draw, 0, 178 , 0, $fotballkort['name']);
         
         $draw->setFillColor( new ImagickPixel( '#000000' )  );
         $draw->setFontSize( '14' );
         $draw->setFont( 'Helvetica' );
         $draw->setGravity ( Imagick::GRAVITY_WEST );
         
         
         /* Create text */
         $imagick->annotateImage($draw, 128, 208 , 0,  $fotballkort['team']);
         $imagick->annotateImage($draw, 128, 229 , 0, $fotballkort['country']);
         $imagick->annotateImage($draw, 128, 250 , 0, $fotballkort['email']);
         
         $cardforlder = "/var/www/repix/data/reedfoto/norwaycup/cards/2015/";
         
         
         $name = str_replace( ' ', '_' , $fotballkort['name'] );
         
         $file = $cardforlder . $name . '_card.jpg';
         
         $imagick->writeImage( $file );
         
         
         if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
        
      }
      
      public function Instagram( $imageid = 0 ) {
        
         $imagick = new Imagick();
         $imagick->newImage( 400 , 586 , new ImagickPixel('white'));
         
         $imagefolder = "/var/www/repix/data/reedfoto/norwaycup/images/2015/";
         
         //$imageid = "ca1192fc";
                            
         $image = new Imagick( $imagefolder . $imageid . '.jpg' );
         $image->scaleImage( 356, 368 , false );
         $imagick->compositeImage( $image, $image->getImageCompose() ,  23 , 76 );
         
         $mal = new Imagick( '/var/www/repix/data/norwaycup/fotballkort/mal.png' ); 
         $imagick->compositeImage( $mal, $mal->getImageCompose() ,  0 , 0 );
         
         
         $pointsize = $height -  ( 4 * $ratio );
                   
         $draw = new ImagickDraw();
         
         $fotballkort = DB::query( "SELECT * FROM project_fotballkort WHERE imageid = ?" , $imageid )->fetchAll( DB::FETCH_ASSOC );
         
         $fotballkort = $fotballkort[0];
         
         $draw->setFillColor( new ImagickPixel( '#32b34c' )  );
         $draw->setFontSize( '28' );
         $draw->setFont( 'Helvetica-Bold' );
         $draw->setGravity ( Imagick::GRAVITY_CENTER );
         $imagick->annotateImage($draw, 0, 178 , 0, $fotballkort['name']);
         
         $draw->setFillColor( new ImagickPixel( '#000000' )  );
         $draw->setFontSize( '14' );
         $draw->setFont( 'Helvetica' );
         $draw->setGravity ( Imagick::GRAVITY_WEST );
         
         
         /* Create text */
         $imagick->annotateImage($draw, 128, 208 , 0,  $fotballkort['team']);
         $imagick->annotateImage($draw, 128, 229 , 0, $fotballkort['country']);
         $imagick->annotateImage($draw, 128, 250 , 0, $fotballkort['email']);
         
         $insta = new Imagick();
         $insta->newImage( 586 , 586 , new ImagickPixel('green'));
         
         $insta->compositeImage( $imagick, $imagick->getImageCompose() ,  93 , 0 );
         
         
         $cardforlder = "/var/www/repix/data/reedfoto/norwaycup/cards/2015/";
         
         
         $name = str_replace( ' ', '_' , $fotballkort['name'] );
         
         $file = $cardforlder . $name . '_card.jpg';
         
         $insta->writeImage( $file );
         
         
         if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
        
      }
      
   }
   
?>