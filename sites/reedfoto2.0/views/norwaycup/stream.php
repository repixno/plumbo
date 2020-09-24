<?php
   
   model( 'reedfoto.fotballkort' );
   
   class Fotballkort extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute( $imageid = 0 ) {
         
         $imagick = new Imagick();
         $imagick->newImage( 400 , 586 , new ImagickPixel('white'));
         
         $year = date('Y');
         
         $imagefolder = "/var/www/repix/data/reedfoto/norwaycup/images/$year/";
         $cardforlder = "/var/www/repix/data/reedfoto/norwaycup/cards/$year/";
         
         if( !file_exists($cardforlder) ){
            mkdir( $cardforlder, 0755 );
         }
         
         $filename = $cardforlder . $imageid. '_card.jpg';
         
         if( file_exists( $filename ) ){
            readfile( $filename );
         }
         else{
            //$imageid = "ca1192fc";
                               
            $image = new Imagick( $imagefolder . $imageid . '.jpg' );
            $image->scaleImage( 356, 368 , false );
            $imagick->compositeImage( $image, $image->getImageCompose() ,  23 , 76 );
            
            $mal = new Imagick( '/var/www/repix/data/norwaycup/fotballkort/mal.png' ); 
            $imagick->compositeImage( $mal, $mal->getImageCompose() ,  0 , 0 );
            
            
            $pointsize = $height -  ( 4 * $ratio ) ;
                      
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
            
            $imagick->writeImage( $filename );
         }
         header( "Content-Type: image/jpeg" ); 
         readfile( $filename );
        
      }
      
      public function Thumb( $imageid = 0, $renew = null ) {
        
         $imagick = new Imagick();
         $imagick->newImage( 400 , 586 , new ImagickPixel('white'));
         
         $year = date('Y');
         
         $imagefolder = "/var/www/repix/data/reedfoto/norwaycup/images/$year/";
         $cardforlder = "/var/www/repix/data/reedfoto/norwaycup/cards/$year/";
         
         if( !file_exists($cardforlder) ){
            mkdir( $cardforlder, 0755 );
         }
         
         $filename = $cardforlder . $imageid. '_card_thumb.jpg';
         
         if( file_exists( $filename ) && !$renew ){
            readfile( $filename );
         }
         else{
            //$imageid = "ca1192fc";
                               
            $image = new Imagick( $imagefolder . $imageid . '.jpg' );
            $image->scaleImage( 356, 368 , false );
            $imagick->compositeImage( $image, $image->getImageCompose() ,  23 , 76 );
            
            $mal = new Imagick( '/var/www/repix/data/norwaycup/fotballkort/mal.png' ); 
            $imagick->compositeImage( $mal, $mal->getImageCompose() ,  0 , 0 );
            
            
            $pointsize = $height -  ( 4 * $ratio ) ;
                      
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
            $imagick->annotateImage($draw, 128, 229 , 0, $fotballkort['mobile']);
            $imagick->annotateImage($draw, 128, 250 , 0, $fotballkort['email']);
            $imagick->thumbnailImage( 400, 400, true );
            
            $thumb = new Imagick();
            $thumb->newImage(400, 400 , new ImagickPixel('white'));
            
            $thumb->compositeImage( $imagick,$imagick->getImageCompose(), 64, 0  );
            
            
            $thumb->writeImage( $filename );
            
            
         }
         header( "Content-Type: image/jpeg" ); 
         readfile( $filename );
        
      }
      
   }
   
?>