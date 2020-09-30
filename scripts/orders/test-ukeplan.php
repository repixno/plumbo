<?PHP

   /******************************************
    * Script Ukeplan
    * 
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   import( 'website.image' );
   import( 'website.order.ukeplanorder' );
   import( 'website.giftpagetemplate' );

   class UkeplanScript extends Script {
      
      private $webspoolFolder = '/home/produksjon/webspool/';
      private $fontfolder = '/var/www/repix/other/fonts/ukeplan/';
      public $srgb = "/var/www/repix/other/colorprofiles/sRGB.icm";
      public $orderfolder = '';
      public $montharray = array( 8030, 8031, 8032, 8160, 8161 ,8162  );
      private $bottom_margin = 0;
      private $giftTemplate;
      private $portal = '';
      
  Public function Main(){
         $ukeplanorder = UserUkeplanOrder::toProduction();
         $orderid = $ukeplanorder->orderid;
         
       //     $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = 1952428")->fetchSingle();
         $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $ukeplanorder->orderid )->fetchSingle();
         //util::Debug( $ukeplanorder->orderid );
         if( !$id ){
            echo "Ingen ordre\n";
            exit;
         }
         // up = uke plan, ukeplan
         list($up_date, $up_time) = explode(' ', $ukeplanorder->date);
         util::Debug( $id );
              util::Debug( $up_date );
             //  util::Debug( $up_time );
           //     util::Debug( $ukeplanorder );
              
         Util::debug ("/usr/bin/rsync -a --ignore-missing-args 10.64.1.184:/data/bildearkiv/z078/print_download/$up_date/$orderid /home/produksjon/webspool/$up_date");
	 //$up_date = date( 'Y-m-d', strtotime('-1 day') );
         exec ("/usr/bin/rsync -a --ignore-missing-args 10.64.1.184:/data/bildearkiv/z078/print_download/$up_date/$orderid /home/produksjon/webspool/$up_date");
         //print_r ("/usr/local/bin/rsync -a --ignore-missing-args romulus.eurofoto.no::ordrer/z078/print_download/$up_date/$orderid /home/produksjon/webspool/$up_date");
         
         $kampanjekode = DB::query( "SELECT kampanje_kode FROM historie_ordre WHERE ordrenr = ?", $ukeplanorder->orderid )->fetchSingle();
         
         $order = new Order( $id );
         $this->portal = $order['kampanje_kode'];
         $this->orderfolder = $this->webspoolFolder . date( 'Y-m-d' , strtotime( $order->tidspunkt ) ) . '/' .  $ukeplanorder->orderid. '/' . $ukeplanorder->articleid . '/autoedit/' ;
         
         $this->xml = $ukeplanorder->project_xml;
         
         $this->project = new SimpleXMLElement( $this->xml );
         
         $template = $this->project->template;
         
         $this->grid = $this->project->grid;
         
         $this->giftTemplate = GiftPageTemplate::fromTemplateIdAndPageId( $template['malid'] , 0);
         
         //util::Debug( $this->grid->background );
         
         
         $this->ratio = $template['mal_width'] /  $template['editorcontainer_width'];
         
         $this->columncolor = (string)$template['gridcolor'];
         
         //$spaces = ( $numberColumns - 1 ) * 30;
         
         $this->place_images();
         
         
         $garderobearray = array( 8200, 8207 );
         $oppmotearray = array( 8201, 8208 , 8210 );
         $dagplanarray = array( 8051, 8050 );
         
         //$template['plantype'] =  'middagsplanlegger';
         
         if( in_array( $ukeplanorder['articleid'] , $oppmotearray )  ){
            $this->bottom_margin = 0;
            $this->createOppmoteGrid( 6 , 'oppmoteplan' );
            util::Debug("garderobeplan");
         }
         else if( in_array( $ukeplanorder['articleid'] , $dagplanarray  ) ){
            $this->bottom_margin = 0;
            $this->createDagplan();
            util::Debug("dagplan");
         }
         else if( in_array( $ukeplanorder['articleid'] , $garderobearray ) || $template['plantype'] ==  'garderobeplan' ){
            $this->bottom_margin = 0;
            $this->createGarderobeGrid();
            util::Debug("garderobeplan");
         }else if ( $template['plantype'] ==  'middagsplanlegger'  ){
            copy( $this->orderfolder . 'mal_with_image.jpg' , $this->orderfolder . 'mal_with_image_and_grid.jpg' );
            util::Debug("middag");
         }
         else if( $template['plantype'] ==  'personalromplan'   ){
            $this->bottom_margin = 0;
            $this->createOppmoteGrid();
            util::Debug("personal");
         }
         else{
            $this->bottom_margin = 25;
            if( count(  $this->project->name ) ){
               $this->place_names();
            }
            
            util::Debug( "grid ");
            if( in_array( $ukeplanorder->articleid, $this->montharray ) ){
               $this->createGrid( 31 );
            }else{
               $this->createGrid( 7 );
            }
         }
         
         
         $filename = $ukeplanorder->quantity . '-' . $ukeplanorder->orderid . '-' . $ukeplanorder->id . '-' . $ukeplanorder->articleid . '-0.jpg';
         
         //$image = new Imagick();
         //$image->readImage( $this->orderfolder . 'mal_with_image_and_grid.jpg' );
         //$image->setImageFormat('jpeg');
         //$image->setImageCompressionQuality(100);
         //$image->writeImage( str_replace( 'autoedit/' , '' , $this->orderfolder) . $filename );
         
         
         
         copy(  $this->orderfolder . 'mal_with_image_and_grid.jpg' , str_replace( 'autoedit/' , '' , $this->orderfolder) . $filename );
         //unlink( $this->orderfolder . 'mal_with_image_and_grid.tif' );
         //unlink( $this->orderfolder . 'mal_with_image.tif' );
         
         if( $kampanjekode == 'UP-001' ){
            
            copy( $this->orderfolder . 'mal_with_image_and_grid.jpg', '/home/produksjon/webspool/ukeplan/' . $filename  );
            
         }
         
         
         
         $ukeplanorder->processed = date( 'Y-m-d H:m:s');
         $ukeplanorder->save();
         
      }
      
      
      
      public function createDagplan(){
         $imageborder = 2;
         $template = $this->project->template;
         $this->createDayGrid();
         Util::Debug( $template );
      }
      
      private function createDayGrid( $rowcount  = 23 ){
         
         $color = '#e7e7e9';
         
         $imagetemplate = new Imagick( $this->orderfolder . 'mal_with_image.jpg' );
         
         $gridthicknes = 2;
         $imageborder = 2;
         $template = $this->project->template;
         $margin = (int)$template['imagefield_y'];
         foreach ( $this->project->image as $image ){
            
            $textwidth =  (int)$template['mal_width']  - (int)$image['imagefield_width'] - ( $margin * 2 );
            $textheight = (int)$image['imagefield_height'] / 3;
            
            $imagesrc = $this->writeTopText( $image['toptext'], 'center', $image['toptextfont'], $image['toptextcolor'], (int)$textwidth, $textheight, 'none', null );
            $imagick = new Imagick();
            $imagick->readImage( $imagesrc );
            $imagetemplate->compositeImage( $imagick, $imagick->getImageCompose() , $margin , $margin );
            $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image.jpg' );
         }
         
         $pos_x = $margin;
         //die();
         //934
         $gridHeight = (int)( $template['mal_height'] - $margin  - $margin - $textheight - $textheight );
         
         $gridFieldWidth =  400 ;
         $posYspace =   $gridHeight / $rowcount;

         $gridcolor = new ImagickPixel( $color );
         
         $vert = new Imagick();
         $vert->newImage( 130, $posYspace - 20, $gridcolor, "png" );      
         $grid_pos_y = (int)$template['imagefield_y'] + $textheight;
         $counter = 0;
            
         $hor_grid_y = $grid_pos_y;
         $y = 0;
         while( $y <= $rowcount ){
            $imagetemplate->compositeImage( $vert,  $vert->getImageCompose(), $pos_x , $hor_grid_y );
            $day =  $this->createMonthdays( $y, $posYspace );
            $day = new Imagick( $day );
            $imagetemplate->compositeImage( $day,  $day->getImageCompose(), $pos_x - 225 , $hor_grid_y );   
            $y++;
            $hor_grid_y += $posYspace;
         }
         
         $weekdays = $this->project->weekdays;
         
         $font = $weekdays['font'] . '.TTF';
         
         $draw = new ImagickDraw();
         $draw->setFont( $this->fontfolder . $font );
         $draw->setFontSize( 170 );
         $draw->setFillColor( "#cccccc" );
         
         $notatx = (int)$template['imagefield_x'];
         $notaty = (int)$template['imagefield_height'] + (int)$template['imagefield_y'] + 170;
         $notatheight = ( $template['mal_height'] - ( (int)$template['imagefield_height']  * 1.3 ) - ( $margin * 2 ) ) / 3 ;
         $imagetemplate->annotateImage($draw, $notatx, $notaty, 0, "Dagen i dag:");
         $imagetemplate->annotateImage($draw, $notatx, $notaty  + ( $notatheight  / 2 ), 0, "Dato:");
         
         $image = new Imagick();
         $image->newImage( (int)$template['imagefield_width'], $notatheight * 2, $color, "png" );
         
         $imagetemplate->compositeImage( $image, $image->getImageCompose() , $notatx , $notaty + $notatheight - 170 );
         $draw->setFillColor( "#000000" );
         
         $imagetemplate->annotateImage($draw, $notatx + 25, $notaty  + $notatheight, 0, "Husk!");
         
         $imagetemplate->setImageFormat('jpeg');
         $imagetemplate->setImageCompressionQuality(100);
         $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image_and_grid.jpg' );
      }
      
      
      public function place_images( ){

         $imageborder = 2;
         $template = $this->project->template;
         
         
         
         if( $this->portal == 'VP-001' ){
            $ext = "SV_";
         }
         else if( $this->portal == 'UP-DK'){
            $ext = 'DK_';
         }
         else{
            $ext = '';
         }
         
         //$giftTemplate = GiftPageTemplate::fromTemplateIdAndPageId( $this->project->template['malid'] , 0);
         $giftTemplate = GiftPageTemplate::fromTemplateIdAndPageId( $template['malid'] , 0);
         
         if( count( $this->project->image )  == 1 ){
            $imageFieldWidth =  ( int )( $template['imagefield_width'] / count( $this->project->image ) ) -  30 - ( $imageborder * 2 );
         }else{
            $imageFieldWidth =  ( int )( $template['imagefield_width'] / count( $this->project->image ) ) -  30;
         }
         try{
            if( !file_exists( $this->orderfolder . $ext . $giftTemplate->fullsize_src ) ){
               $connection = ssh2_connect('eva.eurofoto.no', 22);
               ssh2_auth_password($connection, 'toringe', 'bbc460');
               Util::Debug( $giftTemplate->fullsize_src );
		if( !file_exists( $this->orderfolder )){
			mkdir( $this->orderfolder , 0755, true );
		} 
               //ssh2_scp_recv($connection, '/data/global/maler/orginal/' . $ext . $giftTemplate->fullsize_src , $this->orderfolder . $giftTemplate->fullsize_src );
               //ssh2_exec($connection, 'exit');
			   
			   copy( '/data/global/maler/orginal/' . $ext . $giftTemplate->fullsize_src , $this->orderfolder . $giftTemplate->fullsize_src );
			   
            }
         }catch( Exception $e ){
            Util::Debug($e->getMessage());
         }

         $imagetemplate = new Imagick( $this->orderfolder . $giftTemplate->fullsize_src );
         
         
         //Util::Debug($template['background']);
         //exit;
         
         if( $template['background'] == 'black' ){
            $imagetemplate->negateImage(0);
         }

                  
         $pos_x =  (int)$template['imagefield_x'];
         $pos_y =  (int)$template['imagefield_y'];
         

         $counter = 0;
         foreach ( $this->project->image as $image ){
            if ( $template['background'] == 'black' ){
               $pixel = new ImagickPixel( 'black' );
               $backgroundcolor = 'black';
            }
            else if( $template['gridcolor'] == '#ffffff' || $giftTemplate->bgcolor == '000000' ){
               $pixel = new ImagickPixel( 'black' );
               $backgroundcolor = 'black';
            }else{
               $pixel = new ImagickPixel( 'white' );
               $backgroundcolor = 'white';
            }    
            if( $image['imageid'] == 'dinner'){
               ///$clipart = new Imagick();
               $imagick = new Imagick();
               $imagick->newImage( (int)$imageFieldWidth, (int)$image['imagefield_height'], $pixel );

               //$clipart->readImage( '/var/www/eurofoto/sites/website/webroot/ukeplan/clipart/middagsbestikk2.png' );

               //$clipart->scaleImage( (int)$image['width'], (int)$image['height'] -100 , true );
               //$clipart->setImageOpacity(0.3);
               
               $x = (int)$imageFieldWidth / 2 - (int)$image['width'] / 2;
               
               //$imagick->compositeImage( $clipart, $clipart->getImageCompose() ,  $x , 0 );
               
            }
	    else if(  strpos( $image['imageid'], 'clipart') ){
		
		$imgparts = explode( "/", $image['imageid'] );		
		util::debug( $image );
		$imagick = new Imagick();
               	$imagick->newImage( (int)$imageFieldWidth , (int)$image['imagefield_height'], new ImagickPixel($backgroundcolor));

		$clippath = sprintf(  "/var/www/repix/sites/website/webroot/ukeplan/clipart/familiefigurer/%s/%s", $imgparts[3], $imgparts[4] );

		$clip = new Imagick( $clippath );
		
		$clip->resizeImage( (int)$image['imagefield_width'], (int)$image['imagefield_height'], imagick::FILTER_LANCZOS, 1 , true);	
		
		$x = (int)$imageFieldWidth / 2 - (int)$clip->getImageWidth()  / 2;
		$imagick->compositeImage( $clip, $clip->getImageCompose() ,  $x , 0 );

		util::debug( $clippath);
		//exit;
	    }
            else if( $image['imageid'] == 'noimage' ||  $image['imageid'] == '' ){
               $imagick = new Imagick();
               $imagick->newImage( (int)$imageFieldWidth , (int)$image['imagefield_height'], new ImagickPixel($backgroundcolor));
               //$imagick->setOption('jpeg:size', $image['width'] . 'x'  . $image['height']);
               //$imagick->readImage( $imagesrc );
               //$imagick->resizeImage( (int)$image['width'], (int)$image['height'], imagick::FILTER_LANCZOS, 1 );            
               //$imagick->scaleImage( (int)$image['width'], (int)$image['height'] );  
            }
            else if( $image['imageid'] == 'textimage' ){
              
               if( empty( $image['toptext'] ) ){
                  $image['toptext'] = " ";
               }
               
               if( count( $this->project->name ) ){
                  $names = $this->project->name['height'];
               }
               
               $imagesrc = $this->writeTopText( $image['toptext'], 'center', $image['toptextfont'], $image['toptextcolor'], (int)$image['imagefield_width'], (int)$image['imagefield_height'] , $backgroundcolor, $names );
               $imagick = new Imagick();
               $imagick->readImage( $imagesrc );
               
            }else{
               
               $imagesrc = $this->orderfolder . $image['imageid'] . '.jpg';
               $imagick = new Imagick();
               $imagick->setOption('jpeg:size', $image['width'] . 'x'  . $image['height']);
               $imagick->readImage( $imagesrc );
               
               
               if( $image['rotate'] > 0  && $image['rotate']  < 360 ){
                  $imagick->rotateImage(new ImagickPixel(), (int)$image['rotate']  );
               }
               
               //$imagick->resizeImage( (int)$image['width'], (int)$image['height'], imagick::FILTER_LANCZOS, 1 );
               if( (int)$image['width'] > 0 && (int)$image['height'] > 0 ){ 
               
                  if ( $image['width'] < ( $imageFieldWidth - (int)$image['margin-left'] ) ){
                     $width = $imageFieldWidth - (int)$image['margin-left'];
                     $ratio =  $imagick->getImageWidth() /  $imagick->getImageHeight();
                     $imagick->scaleImage(  $width  , $width / $ratio , true ); 
                  }else{
                     $imagick->scaleImage( (int)$image['width'], (int)$image['height'] , true ); 
                  }
               }           
            
            }
            
            if( ( $imagick->getImageHeight() + (int)$image['margin-top'] ) < (int)$image['imagefield_height'] ){
               (int)$image['margin-top'] = $image['imagefield_height'] - $imagick->getImageHeight();
            }
            
            
            
            
            $imagick->cropImage( (int)$imageFieldWidth, (int)$image['imagefield_height'] , (int)$image['margin-left']  * -1, (int)$image['margin-top']  * -1 );            

            
            Util::Debug( $imagick->getImageHeight()  );
            
            if( $image['blackandwhite'] == 1 ){
               $imagick->modulateImage(100,0,100);
            }
            
            $color=new ImagickPixel();
            $color->setColor( $this->columncolor );
            if( $image['imageid'] != 'textimage' && $image['imageid'] != 'dinner' && !strpos( $image['imageid'], 'clipart') ){
               $imagick->borderImage( $color, $imageborder, $imageborder );
            }
            
            $imagetemplate->compositeImage( $imagick, $imagick->getImageCompose() , $pos_x , $pos_y );
            $imagick->writeImage( $this->orderfolder . basename( $image['imageid'] ) .'_image.jpg' );
            
            $pos_x += $imageFieldWidth + 30; 
            
         }
         $imagetemplate->setImageFormat('jpeg');
         $imagetemplate->setImageCompressionQuality(100);
         $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image.jpg' );

      }
      
      private function place_names(){
         $imageborder = 2;
         
         $template = $this->project->template;
         $imageFieldWidth =  ( int )( $template['imagefield_width'] / (int)$template['numberofcolumns'] ) -  30 ;
         
         if( count( $this->project->image )  == 1 ){
            $imageFieldWidth = $imageFieldWidth;
         }
         
         $imagetemplate = new Imagick( $this->orderfolder . 'mal_with_image.jpg' );
                  
         $pos_x =  (int)$template['imagefield_x'] + $imageborder ;
         $pos_y =  (int)$template['imagefield_y'] + $template['imagefield_height'] ;
         
         $counter = 0;
         
         try{
            foreach ( $this->project->name as $name ){

               $height = (int)$name['height'];
               $width = (int)$name['width'];
               $text = $name['text'];
               $font = $name['font'];
               $color = $name['color'];
               
               if( $text != 'Sett inn tekst' ){
                  $ratio = $template['mal_width']  / $template['editorcontainer_width'];
                  
                  $pointsize = $height -  ( 4 * $ratio ) ;
                  
                  $image = new Imagick();
                  $draw = new ImagickDraw();
   
                  if( $color == '#FFFFFF'){
                     $pixel = new ImagickPixel( 'black' );
                  }
                  else if( $template['background'] == 'black'  ){
                      $pixel = new ImagickPixel( 'black' );
                  }else{
                     $pixel = new ImagickPixel( 'white' );
                  }
                  
                  /* New image */
                  $image->newImage( $imageFieldWidth, $height, $pixel );
                  
                  //$image->setImageOpacity(0.5);
				  $image->evaluateImage(Imagick::EVALUATE_MULTIPLY, 0.5, Imagick::CHANNEL_ALPHA);

                  
                  $pixel = new ImagickPixel( $color );
                  $draw->setFillColor( $pixel );
                  /* Font properties */
                  $draw->setGravity( Imagick::GRAVITY_CENTER );
                  $draw->setFont( $this->fontfolder . $font );
                  $draw->setFontSize( $pointsize );
                  
                  
                   if( $font == 'greyscale.ttf' ){
                     $offset = 14;
                     $draw->setFontSize( $pointsize  + 18 );
                  }else{
                     $offset = 0;
                  }
                  
                  /* Create text */
                  $image->annotateImage($draw, 0, -1 + $offset , 0, $text);
                  //$image->writeImage( $this->orderfolder . "name_" . $text . '.png' );
                  
                  
                  $imagetemplate->compositeImage( $image, $image->getImageCompose() , $pos_x , $pos_y - $height - ( $imageborder * 2 ) - 1 );
                  $pos_x += $imageFieldWidth + 30; 
               }
               
            }
         }catch ( Exception $e){
            util::Debug( $e );
         }
          $imagetemplate->setImageFormat('jpeg');
          $imagetemplate->setImageCompressionQuality(100);
          $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image.jpg' );
         
         
      }
      
      private function createGarderobeGrid(  $rowcount  = 6 ){
         $gridthicknes = 2;
         $imageborder = 2;
         $template = $this->project->template;
         $image = $this->project->image;
         $pos_x = (int)$template['imagefield_x'];
         //die();
         
         $bottommargin = 350;
         $imagetemplate = new Imagick( $this->orderfolder . 'mal_with_image.jpg' );
         //934
         $gridHeight = (int)$template['imagefield_height'];
         
         $gridFieldWidth =  ( int )( $template['imagefield_width'] / (int)$template['numberofcolumns'] ) -  30 ;
         $posYspace =   30 ;

         $gridcolor = new ImagickPixel( $this->columncolor );
         
         $headergridbox = new Imagick();
         $headergridbox->newImage( $gridFieldWidth, (int)$image[0]['height'], "#FFFFFF", "png" );
         $headergridbox->borderImage ( $gridcolor , $gridthicknes , $gridthicknes );
         
         $headergridbox->writeImage( $this->orderfolder . 'mal_with_image_and_gridfes.jpg' );
         
         $grid_pos_y = (int)$template['imagefield_y'] + (int)$template['imagefield_height'] + 30;
         
         $counter = 0;
         
         $numberofcolumns = $template['numberofcolumns'];
         
         while( $counter <  $numberofcolumns ){
            if( $counter > 0 ){
               
               $day = $this->createWeekdaysBarnehage( $counter - 1, $gridFieldWidth );
               $day = new Imagick( $day );
               $imagetemplate->compositeImage( $day,  $day->getImageCompose(), $pos_x , $hor_grid_y );
               $imagetemplate->compositeImage( $headergridbox,  $headergridbox->getImageCompose(), $pos_x , $grid_pos_y  + $day->getImageHeight() );
            }
            $hor_grid_y = $grid_pos_y;
            $pos_x += $gridFieldWidth;
            $pos_x += 30;
            $counter++;

         }
         
         $grid_pos_y += (int)$template['imagefield_height'] + $posYspace + 2  + $day->getImageHeight();
         
         $counter = 0;
         $pos_x = (int)$template['imagefield_x'];
         
         
         $topgridboxheight = (int)$image[0]['height'] / 2 ;
         $topgridbox = new Imagick();
           
         while( $counter <  $numberofcolumns ){

            if( $counter == 0 ){
               $topgridbox->newImage( $gridFieldWidth, $topgridboxheight , $gridcolor, "png" );
               
            }else{
               $topgridbox->newImage( $gridFieldWidth, $topgridboxheight , "#FFFFFF", "png" );
            }
            $topgridbox->borderImage ( $gridcolor , $gridthicknes , $gridthicknes );
            $hor_grid_y = $grid_pos_y;
            $y = 0;
            while( $y <= 2 ){
               $y++;
               $imagetemplate->compositeImage( $topgridbox,  $topgridbox->getImageCompose(), $pos_x , $hor_grid_y );
               $hor_grid_y += $posYspace + $topgridboxheight;
            }
            
            $pos_x += $gridFieldWidth;
            //$imagetemplate->compositeImage( $vert,  $vert->getImageCompose(), $pos_x + ($imageborder * 2) - $gridthicknes, $grid_pos_y );
            $pos_x += 30;
            
            $counter++;

         }
         
         $y = 0;
         $maingrid = new Imagick();
         
         $seconmaingrid = ( $gridFieldWidth * 5 ) + ( $posYspace * 4 );
         $pos_x = (int)$template['imagefield_x'];
         
         $numberofrows = ( $this->grid['maingridrows'] ) ? $this->grid['maingridrows'] : 20;
         
         $maingridboxheight = ( ( $template['mal_height'] - $hor_grid_y - $bottommargin ) / $numberofrows ) - $posYspace ;
         
         util::Debug( $template['mal_height'] );
         util::Debug( $hor_grid_y );
         util::Debug( $maingridboxheight );
         
         
         
         while ( $y < $numberofrows ){
            
            $y++;
            
            if( $y % 2 ){
               $boxcolor = $gridcolor;
            }else{
               $boxcolor = "#FFFFFF";
            }
            $maingrid->newImage( $gridFieldWidth, $maingridboxheight  , $boxcolor, "png" );
            $maingrid->borderImage ( $gridcolor , $gridthicknes , $gridthicknes );
            $imagetemplate->compositeImage( $maingrid,  $maingrid->getImageCompose(), $pos_x , $hor_grid_y  );
            
            $maingrid->newImage( $seconmaingrid, $maingridboxheight , $boxcolor, "png" );
            $maingrid->borderImage ( $gridcolor , $gridthicknes , $gridthicknes );
            $imagetemplate->compositeImage( $maingrid,  $maingrid->getImageCompose(), $pos_x + $gridFieldWidth + $posYspace , $hor_grid_y  );
            
            $hor_grid_y += $posYspace + $maingridboxheight;
            
         }
         
         
         $imagetemplate->setImageFormat('jpeg');
         $imagetemplate->setImageCompressionQuality(100);
         $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image_and_grid.jpg' );
      }
      
      
      private function createOppmoteGrid( $rowcount  = 6 , $templatetype = '' ){
         
         $gridthicknes = 4;
         $imageborder = 2;
         $template = $this->project->template;
         $image = $this->project->image;
         $weekdays = $this->project->weekdays;
         $pos_x = (int)$template['imagefield_x'];
         $posYspace =   30 ;
         
         
         if( $template['plantype'] == 'oppmoteplan' || $templatetype == 'oppmoteplan' ){
            $topgridtextfield = "OPPMØTE UKE:";
            $bottomgridtextfield = array( "Husk!" );   
         }
         else if( $template['plantype'] == 'personalromplan' ) {
            $topgridtextfield = "PERSONALROM";
            $bottomgridtextfield = array( "Husk!" , "Dagen i dag!" ) ;
         }
         
         
         
         $whitepixel = $bordercolor = new ImagickPixel( '#FFFFFF' );
         $blackpixel = $bordercolor = new ImagickPixel( '#000000' );
         
         $height = (int)$template['imagefield_height'] / 3 ;
         $width = (int)$template['imagefield_width'] ;
         $bottommargin = 350;
         $imagetemplate = new Imagick( $this->orderfolder . 'mal_with_image.jpg' );
         //934
         $gridHeight = (int)$template['imagefield_height'];

         $gridFieldWidth =  (int)( $template['imagefield_width'] / (int)$template['numberofcolumns'] ) -  $posYspace + $gridthicknes  ;
         

         $gridcolor = new ImagickPixel( $this->columncolor );

         $counter = 0;
         
         $numberofcolumns = $template['numberofcolumns'];
         
         $color = $weekdays['color'];
         $font = $weekdays['font'] . '.TTF';
         
         $image = new Imagick();
         $draw = new ImagickDraw();
         $pixel = new ImagickPixel( 'white' );

         $image->newImage($width, $height, $pixel);
         $fontratio = 2; 
         $pixel = new ImagickPixel( $color );
         $draw->setFillColor($pixel);
            
         /* Font properties */
         $draw->setFont( $this->fontfolder . $font );
         $draw->setGravity( Imagick::GRAVITY_WEST );
         $draw->setFontSize( $height  );
         /* Create text */
         $image->annotateImage($draw, 0, 0 , 0, $topgridtextfield );
         /* Give image a format */
         $image->setImageFormat('png');
         $image->writeImage( $this->orderfolder . 'OPPMOTE.png' );
         $grid_pos_y = (int)$template['imagefield_y'] + (int)$template['imagefield_height'] + $posYspace;
         $imagetemplate->compositeImage( $image,  $image->getImageCompose(), $pos_x , $grid_pos_y );
         
         $grid_pos_y += $height;
         $hor_grid_y = $grid_pos_y;
         $xcount = 0;
         $numberofrows = $this->project->grid['maingridrows'];
         $maingrid_height = $template['mal_height'] - ( $template['imagefield_height']  / 1.8 ) - $grid_pos_y - 30 - $template['imagefield_x'] - ( $template['imagefield_height']  * 2 );
         $rowheight = (  $maingrid_height / $numberofrows ) - $posYspace - ( $gridthicknes * 2 );
         while( $counter <  $numberofcolumns ){
            if( $counter == 0 ){
               $gridFieldWidth_text =  ( ( $template['imagefield_width'] / 6 )  * 1.5 ) - 30;
               $firstgrid = $gridFieldWidth_text;
            }else{
               $gridFieldWidth_text = ( ( $template['imagefield_width'] - $firstgrid ) / 5 ) - 30 ; 
            }
            $day = $this->createWeekdaysBarnehage( $counter , $gridFieldWidth_text );
            $day = new Imagick( $day );
            $imagetemplate->compositeImage( $day,  $day->getImageCompose(), $pos_x , $hor_grid_y );
            
            $box_grid_y = $hor_grid_y + $day->getImageHeight() + 30 ;
            
            while( $xcount < $numberofrows ){
               if( $xcount % 2 ){
                  $backgroundcolor = "#FFFFFF";
                  $bordercolor = $blackpixel;
               }else{
                  $backgroundcolor = $gridcolor;
                  $bordercolor = $whitepixel;
               }
               $headergridbox = new Imagick();
               $headergridbox->newImage( $gridFieldWidth_text -4, $rowheight, $backgroundcolor, "png" );
               $headergridbox->borderImage ( $bordercolor , $gridthicknes , $gridthicknes );
               
               /*if( $counter > 0 ){
                  # define a line
                  $line = new ImagickDraw;
                  $line->setfillcolor($bordercolor);
                  $line->setstrokecolor($bordercolor);
                  $line->setstrokewidth( 4 );
                  $line->line(  $gridFieldWidth_text  / 2 , 4,  $gridFieldWidth_text / 2 , $rowheight + 4);
                  $headergridbox->drawimage( $line );
               }*/
               
               $headergridbox->writeImage( $this->orderfolder . 'mal_with_image_and_gridfes.jpg' );
               $imagetemplate->compositeImage( $headergridbox,  $headergridbox->getImageCompose(), $pos_x , $box_grid_y );
               $box_grid_y += $headergridbox->getImageHeight();
               $box_grid_y += 30;
               $xcount++;
            }
            
            $xcount = 0;
            $hor_grid_y = $grid_pos_y;
            $pos_x += $gridFieldWidth_text;
            $pos_x += 30;
            $counter++;
         }
         
         $grid_pos_y += $maingrid_height;
         /* Create bottomtext */
         if( is_array( $bottomgridtextfield ) ){
            $bottomgridcount = count( $bottomgridtextfield );
         }
         $box_grid_x = (int)$template['imagefield_x'];
         foreach ( $bottomgridtextfield  as $text ){
            //create bottom box
            $boxwidth = ( $template['imagefield_width']  - 4 ) / $bottomgridcount;
            $bottombox = new Imagick();
            $bottombox->newImage( $boxwidth - ( ( $bottomgridcount - 1) * 30 ), $template['imagefield_height'] * 2  , "#FFFFFF", "png" );
            $bottombox->borderImage ( $blackpixel , $gridthicknes , $gridthicknes );
            $imagetemplate->compositeImage( $bottombox,  $bottombox->getImageCompose(), $box_grid_x  , $box_grid_y );
         
            
            $bottomgridtext = new Imagick();
            $bottomgridtext->newImage($boxwidth - 100, $height + 30, $whitepixel);
            $bottomgridtext->annotateImage($draw, 0, 0 , 0, $text);
            /* Give image a format */
            $bottomgridtext->setImageFormat('png');
            $bottomgridtext->writeImage( $this->orderfolder . $text . '.png' );
            $imagetemplate->compositeImage( $bottomgridtext,  $bottomgridtext->getImageCompose(),$box_grid_x + 30 , $box_grid_y  + 30 );
            
            $box_grid_x += $boxwidth + 30;
         }
            /* SAVE IMAGE */
            $imagetemplate->setImageFormat('jpeg');
            $imagetemplate->setImageCompressionQuality(100);
            $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image_and_grid.jpg' );
         
         }
         
      
      private function createGrid( $rowcount  = 7 ){
         
         $gridthicknes = 2;
         $imageborder = 2;
         $template = $this->project->template;
         $pos_x = (int)$template['imagefield_x'];
         //die();
         
         if( $template['gridcolor'] == '#ffffff' ){
            $bottommargin = 429;
         }else{
            $bottommargin = 827;
         }

         $imagetemplate = new Imagick( $this->orderfolder . 'mal_with_image.jpg' );
         
         //$template['notatoption'] = 'true';
         
         Util::Debug($template);
         
         if( $template['notatoption'] == "true" ){
            
            $bottommargin += 1060;
            $notaty =  $template['mal_height'] - $bottommargin + 60;
            $notatwidth = $template['mal_width'] - ( $pos_x  * 2 ) - 30;
            
            if( $template['background'] == 'trans' ){
               $template['background']  = 'white';
            }
            
            $pixel = new ImagickPixel( $template['background'] ) ;
            
            $notatfield = new Imagick();
            $notatfield->newImage( (int)$notatwidth, 1000, $pixel );
            $notatfield->borderImage( $this->columncolor, 2, 2 );
            $imagetemplate->compositeImage( $notatfield,  $notatfield->getImageCompose(), $pos_x , $notaty );            
            
            $weekdays = $this->project->weekdays;
            
            $draw = new ImagickDraw();
            $pixel = new ImagickPixel( (string)$weekdays['color'] );
            $draw->setFillColor($pixel);
            //$draw->setFont('arial' );
            
            $draw->setFontSize( 100 );
            
            
            /* Create text */
            $imagetemplate->annotateImage($draw, $pos_x + 50 , $notaty + 170 , 0, "Husk!");
            
            
            
         }
         
         
         //934
         $gridHeight = (int)( $template['mal_height'] - $template['imagefield_height']  - (  $template['imagefield_x'] ) ) - $bottommargin;
         
         $gridFieldWidth =  ( int )( $template['imagefield_width'] / (int)$template['numberofcolumns'] ) -  30 ;
         $posYspace =   $gridHeight / $rowcount;

         $gridcolor = new ImagickPixel( $this->columncolor );
         
         $vert = new Imagick();
         $vert->newImage( $gridthicknes, $gridHeight, $gridcolor, "png" );      
         $grid_pos_y = (int)$template['imagefield_y'] + (int)$template['imagefield_height'] + 30;
         $hor = new Imagick();
         $hor->newImage( $gridFieldWidth + ( $imageborder * 2 ) , $gridthicknes, $gridcolor, "png" );
         
         $counter = 0;
         
         $numberofcolumns = $template['numberofcolumns'];
         
         while( $counter <  $numberofcolumns ){
            $imagetemplate->compositeImage( $vert,  $vert->getImageCompose(), $pos_x , $grid_pos_y );
            $hor_grid_y = $grid_pos_y;
            $y = 0;
            while( $y <= $rowcount ){
               
               if( $counter == 0 && $y < $rowcount ){
                  if( $rowcount == 7 ){
                     $day = $this->createWeekdays( $y, $posYspace );
                     $day = new Imagick( $day );
                     $imagetemplate->compositeImage( $day,  $hor->getImageCompose(), $pos_x - 175 , $hor_grid_y );
                  }
                  else if( $rowcount == 31 ){
                     $day =  $this->createMonthdays( $y, $posYspace );
                     $day = new Imagick( $day );
                     $imagetemplate->compositeImage( $day,  $hor->getImageCompose(), $pos_x - 225 , $hor_grid_y );
                  }
               }
               
               $y++;
               $imagetemplate->compositeImage( $hor,  $hor->getImageCompose(), $pos_x , $hor_grid_y );
               $hor_grid_y += $posYspace;
            }
            
            $pos_x += $gridFieldWidth;
            $imagetemplate->compositeImage( $vert,  $vert->getImageCompose(), $pos_x + ($imageborder * 2) - $gridthicknes, $grid_pos_y );
            $pos_x += 30;
            $counter++;
            if( $numberofcolumns == $counter && !empty(  $template['clipart'] ) ){
               
               $clipart = new Imagick( '/var/www/repix/sites/website/webroot/ukeplan/clipart/middagsbestikk2.png' );
               //$clipart->writeImage( $this->orderfolder . 'middag.tif' );
               //$clipart->resizeImage( (int)$gridFieldWidth, (int)$posYspace, imagick::FILTER_LANCZOS, true );
               $clipart->scaleImage( $gridFieldWidth, $posYspace, true );
               $hor_grid_y = $grid_pos_y;
               $yc = 0;
               while( $yc < $rowcount ){
                  //$clipart->writeImage( $this->orderfolder . 'middag' . $yc. '.tif' );
                  $imagetemplate->compositeImage( $clipart,  $clipart->getImageCompose(), $pos_x - ( $gridFieldWidth / 2 ) - ( $clipart->getImageWidth() / 2) , $hor_grid_y );
                  $hor_grid_y += $posYspace;
                  $yc++;
               }
            }
         }
         
         $imagetemplate->setImageFormat('jpeg');
         $imagetemplate->setImageCompressionQuality(100);
         $imagetemplate->writeImage( $this->orderfolder . 'mal_with_image_and_grid.jpg' );
      }
      
      private function createWeekdays( $index, $width ) {

            $weekdays = $this->project->weekdays;
            $height = 175;

            $template = $this->project->template;
            
            $color = $weekdays['color'];
            $font = $weekdays['font'] . '.TTF';

            $days = (array)$this->project->grid->gridvalue;
            
            /*$days = array( 
                  0 => 'Mandag' , 
                  1 => 'Tirsdag', 
                  2 => 'Onsdag', 
                  3 => 'Torsdag', 
                  4 => 'Fredag', 
                  5 => 'Lørdag', 
                  6 => 'Søndag' );*/
            
            $day = $days[$index];
            
            //util::Debug( $day );
            //util::Debug( $index );

            /* Create some objects */
            $image = new Imagick();
            $draw = new ImagickDraw();
            
            if( $template['gridcolor'] == '#ffffff' || $this->giftTemplate->bgcolor == '000000' ){
               $pixel = new ImagickPixel( none );
            }else{
               $pixel = new ImagickPixel( none );
            }
            $image->newImage($width, $height, $pixel);
            $image->setImageColorspace (imagick::COLORSPACE_RGB); 
            
            $fontratio = $width / $height;
            
            if( $fontratio < 2 ){
               $fontratio = 3.5;
            }
            else if ( $fontratio > 4.5 ){
               $fontratio = 2;
            }
            else{
               $fontratio = 2.7;
            }
            
            $pixel = new ImagickPixel( $color );
            $draw->setFillColor($pixel);
            
            /* Font properties */
            $draw->setFont( $this->fontfolder . $font );
            $draw->setGravity( Imagick::GRAVITY_CENTER );
            $draw->setFontSize( $height / $fontratio );
            
            
            /* Create text */
            $image->annotateImage($draw, 0, 0 , 0, $day);
            $image->rotateImage(new ImagickPixel(), -90);
            /* Give image a format */
            $image->setImageFormat('png');
            
            $image->writeImage( $this->orderfolder . 'weekdays.png' );
            
            return $this->orderfolder . 'weekdays.png';

      }
      
      
      private function createWeekdaysBarnehage( $index, $width ) {

            $weekdays = $this->project->weekdays;
            $height = 175;
            $template = $this->project->template;
            
            $color = $weekdays['color'];
            $font = $weekdays['font'] . '.TTF';
                        
            $height =  $template['imagefield_height']  / 1.8 ;
            $days = $this->project->grid->gridvalue ;

            
            $day = $days[$index];
            
            //util::Debug( $day );
            //util::Debug( $index );

            /* Create some objects */
            $image = new Imagick();
            $draw = new ImagickDraw();
           
            if( $template['gridcolor'] == '#ffffff' ){
               $pixel = new ImagickPixel( 'black' );
            }else{
               $pixel = new ImagickPixel( 'white' );
            }
            $image->newImage($width, $height, $pixel);
            $image->setImageColorspace (imagick::COLORSPACE_RGB); 
            
            $fontratio = $width / $height;
            
            util::Debug( "fontratio : " . $fontratio );
            
            if( $fontratio < 2 ){
               $fontratio = 3.5;
            }
            else if ( $fontratio > 4.5 ){
                $fontratio = 2;
            }
            else{
               $fontratio = 2.7;
            }
            
            $pixel = new ImagickPixel( 'black' );
            $draw->setFillColor($pixel);
            
            /* Font properties */
            $draw->setFont( $this->fontfolder . $font );
            $draw->setGravity( Imagick::GRAVITY_CENTER );
            
            
            if(  $this->bottom_margin  == 0 ){
               $fontratio = 2;
               $draw->setGravity( Imagick::GRAVITY_SOUTH );
            }
            if( $day == 'Navn:' || $day == 'ANSATT:' ){
               $draw->setGravity( Imagick::GRAVITY_SOUTHWEST );
            }

            $draw->setFontSize( $height / $fontratio );
            
            
            /* Create text */
            $image->annotateImage($draw, 0 , 20 , 0, $day);
            /* Give image a format */
            $image->setImageFormat('png');
            
            $image->writeImage( $this->orderfolder . 'weekdays.png' );
            
            return $this->orderfolder . 'weekdays.png';

      }

      private function createMonthdays( $index, $width ) {

            $weekdays = $this->project->weekdays;
            $height = $width;
            $width = 150;
            $template = $this->project->template;
            
            $color = $weekdays['color'];
            $font = $weekdays['font'] . '.TTF';

            $day = (string)$this->grid->gridvalue[$index] ;
            
            util::Debug( $day );

            /* Create some objects */
            $image = new Imagick();
            $draw = new ImagickDraw();
           
            if( $template['gridcolor'] == '#ffffff' ){
               $pixel = new ImagickPixel( 'black' );
            }else{
               $pixel = new ImagickPixel( 'white' );
            }
            $image->newImage($width, $height, $pixel);
            
            $fontratio = $width / $height;
            
            
            util::Debug( $fontratio );
            
            if( $fontratio < 2 ){
               $fontratio = 3.5;
            }
            else if ( $fontratio > 4.5 ){
               $fontratio = 2;
            }
            else{
               $fontratio = 2.7;
            }
            
            $pixel = new ImagickPixel( $color );
            $draw->setFillColor($pixel );
            
            /* Font properties */
            $draw->setFont( $this->fontfolder . $font );
            $draw->setGravity( Imagick::GRAVITY_EAST );
            $draw->setFontSize( $height  / $fontratio );
            
            
            /* Create text */
            $image->annotateImage($draw, 0, 0 , 0, $day);
           // $image->rotateImage(new ImagickPixel(), 0);
            /* Give image a format */
            $image->setImageFormat('png');
            
            $image->writeImage( $this->orderfolder . 'weekdays.png' );
            
            return $this->orderfolder . 'weekdays.png';

      }
      
      private function writeTopText( $text, $gravity, $font, $color, $width, $height, $backgroundcolor, $names ) {
      
         $border = 132;
         //$bottom_margin = $this->bottom_margin;
         $bottom_margin = $this->bottom_margin * $this->ratio;
         
         if( $names ){
            $bottom_margin += $names + 80; 
         }
         
         $offset = 120;
         
         $textcolor = new ImagickPixel( $color );
         $white = new ImagickPixel( none );
         
         $template = new Imagick();
         $template->newImage($width, $height, new ImagickPixel( 'none' ));
         $template->setImageColorspace (imagick::COLORSPACE_RGB); 
         $template->setImageFormat('png');

            if( $gravity == 'right' ){
               $textgravity = Imagick::GRAVITY_EAST;
            }
            else if( $gravity == 'center' ){
               $textgravity = Imagick::GRAVITY_CENTER;
            }else{
               $textgravity = Imagick::GRAVITY_WEST;
            }

            if( empty( (string)$text ) || strpos( $text, 'Sett inn tekst' ) ){
               $text = ' ';
            }
            $draw = new ImagickDraw();
      	    $draw->setFont( $this->fontfolder . $font );
      	    $draw->setFontSize( 512 );
      	    $draw->setGravity( $textgravity );
      	    $draw->setFillColor( $textcolor );
       
      	    $canvas = new Imagick();      		
      	    $metrics = $canvas->queryFontMetrics( $draw, $text );
			Util::debug( (string)$text );
			Util::debug( $metrics );
   
			$metrics['textWidth'] = $metrics['textWidth'] > 0 ? $metrics['textWidth']  : 1;
            $canvas->newImage( $metrics['textWidth'], $metrics['textHeight'], $white, "png");
            
      	    $canvas->annotateImage($draw,0,10,0,$text);
      	    $templategeo =  $template->getImageGeometry();
      		
            $textratio =  $metrics['textWidth'] / $metrics['textHeight'];
            $texplaceholderwidth = $width ;
            $textplaceholderheight = $height  - $bottom_margin;
            $templateratio =  $texplaceholderwidth  / $textplaceholderheight;
   
            if ( $textratio > $templateratio ){
               $canvas->scaleImage( $texplaceholderwidth , 0);
            }
            else{
               $canvas->scaleImage( 0,$textplaceholderheight);
            }

            if( $gravity == 'left' ){
               $sizeoffset = $border;
            }
            else if( $gravity == 'right' ){
               $sizeoffset = ( $templategeo['width'] - $canvas->getImageWidth() - ( $border * 2 ) );
            }else{
               $sizeoffset = ( $templategeo['width'] - $canvas->getImageWidth() ) / 2;
            }
   
            $topmargin = ( ( $templategeo['height'] - $canvas->getImageHeight() ) / 2 ) / 2 ;
            
      	    $canvas->setImageFormat('PNG');
   
            $template->compositeImage( $canvas, $canvas->getImageCompose() , $sizeoffset,  $topmargin );
            
            $template->writeImage( $this->orderfolder . 'toptext.png' );
            
            $canvas->clear();
            $canvas->destroy();
            $draw->clear();
            $draw->destroy();
            $template->clear();
            $template->destroy();
   		   
            return $this->orderfolder . 'toptext.png';

      }
      
   }
   

   CLI::Execute();

?>
