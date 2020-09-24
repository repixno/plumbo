<?PHP

   /**************************************
    * Script for handling Mal orders.
    * runst the converts script and moves
    * orders to correct location
    * 
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   import( 'production.conversions');


   class OrderImportScript extends Script {
     
     // public $smilFolder = '/home/produksjon/webspool/c8/Output/'; 
      public $webspoolFolder = '/home/produksjon/webspool/';
      private $imageserver = "http://therese.eurofoto.no/production/index.php";
      private $securecode = "p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ";
      private $imgserver = "10.64.1.134";
      Public function Main(){
         
         $ready_orders = Conversions::GetReadyArray();
              $stabburet = array( 7196, 7301, 7302, 7303, 7304, 7305, 7306, 7307, 7308,3044 , 7444, 7756,7745,7746 );
         $calenderarray = Settings::Get( 'production', 'calenderarray' );
         
         foreach ( $ready_orders  as $to_do_order ){
            
            $kampanje_kode = DB::query( "SELECT kampanje_kode, uid FROM historie_ordre WHERE ordrenr = ?", $to_do_order['order_id'] )->fetchSingle();
          //   $uidkunde = DB::query( "SELECT uid FROM historie_ordre WHERE ordrenr = ?", $to_do_order['order_id'] )->fetchSingle();
             //$uid = DB::query( "SELECT uid, kampanje_kode FROM historie_ordre WHERE ordrenr = ?", $to_do_order['order_id'] )->fetchSingle();
            Util::Debug($to_do_order);
            
            $org_source = sprintf( '%s%s/%s/%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
          
          
          // endra 2020 sidan perfectlyclear ikkje fungerer på png ordrer
          //  $convert = sprintf( '%sconvert/%s_%s_%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
            $convert = sprintf( '%senhance/%s_%s_%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
            
            if( $to_do_order['article_id']  == 7007){//sepsial folder for stamps
               $preview = sprintf( '%sstamp/%s/%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id']);
            }
            /*else if( $to_do_order['article_id']  == 7756){//sepsial folder for stamps
               $preview = sprintf( '%sSkanska/%s/%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id']);
            }*/
            else if( in_array(  $to_do_order['article_id'], $stabburet ) ){
               $preview = sprintf( '%spreview-stabburet/%s/%s_%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
            }
            
            else if( $to_do_order['article_id']  == 7474 || $to_do_order['article_id']  == 7508 || $to_do_order['article_id']  == 7458  || $to_do_order['article_id']  == 7455 || $to_do_order['article_id']  == 7454 || $to_do_order['article_id']  == 7461){//sepsial folder for stamps
               $preview = sprintf( '%sStudentskilt/%s/%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id']);
            }
            
            else if( $kampanje_kode ==  '1030157 '|| $to_do_order['article_id']  == 439){//sepsial folder for stamps
               $preview = sprintf( '%ssSmil_preview/%s/%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id']);
            }
            
            
             else if( $kampanje_kode ==  '1370892  '|| $to_do_order['article_id']  == 522){//sepsial folder for stamps
                $preview = sprintf( '%spreview/%s/%s_%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
            }
            
            
            
            
            
        
            else  if( $kampanje_kode ==  '1030136' && '1030138' && '1030146' && '1030148' && '1030149' &&'1030150' && '1030151' && '1030152' && '1030154' && '1030155' && '1030156' && '1030159' && '1030160' && '1033715' && '1033716' && '1033724'  &&  '1039217' &&  '1034717' &&  '1034721' &&  '1034722' && '1034920' && '1034922' && '1034957' && '1043366' && '1035956' && '1034929' && '1034743' && '1043368' && '1034911' && '1034952' &&'1041409' && '1034917' && '1037991' && '1037998' && '1090600' &&'1105325' && '1114803' && '1120915' && '1131937' && '1132808'){
              
              
               $preview = sprintf( '%sNetlife_preview/%s/%s_%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
           
            }
            
       
            else{
               $preview = sprintf( '%spreview/%s/%s_%s/', $this->webspoolFolder, $to_do_order['order_date'], $to_do_order['order_id'], $to_do_order['article_id'] );
            }
            
            $conversions = new Conversions( $to_do_order['id'] );
            if( file_exists( $convert . 'Proceed.txt' ) || file_exists( $convert . 'proceed.txt' ) ){
               
               
               if( in_array(  $to_do_order['article_id'], $stabburet ) ){
                  
                  
                  $conversions->began_at = date( 'Y-m-d H:i:s' );
                  $conversions->status = 'executing';
                  $conversions->save();
                  try{
                     Util::debug( $convert . 'autoedit' );
                     
                     if( !file_exists( $convert . 'autoedit' ) ){
                        mkdir( $convert . 'autoedit' );
                     }
                     
                     chdir($convert . 'autoedit' );
                     foreach ( glob( $convert . '*roceed.txt' ) as $filename ){
                        $this->StabburetLeverpostei( $filename, $org_source, $convert, $to_do_order['order_id'] );
                     }
                     
                     $conversions->status = 'done';
                     $conversions->ended_at = date( 'Y-m-d H:i:s' );
                     $conversions->save();
                     
                  }catch ( Exception $e ){
                     
                     util::Debug( $e );
                     $conversions->status = 'void';
                     $conversions->save();
                  }
                  
                  
                  
               }else{
               
                  $conversions->began_at = date( 'Y-m-d H:i:s' );
                  $conversions->status = 'executing';
                  $conversions->save();
                  try{      
                     //find files that needs to be converted
                     chdir($convert . 'autoedit' );
                     foreach ( glob( '*.sh' ) as $filename ){
                        exec( sprintf( "sh %s", $filename ) );
                     }
   
                     if( !file_exists( $org_source ) ){
                        mkdir( $org_source, 0777, true );
                     }
                     
                     foreach ( glob( $convert . '*' ) as $preview_file ){
                        if( !is_dir( $preview_file ) ){
                           rename( $preview_file,  $org_source . basename( $preview_file ) );  
                        }else{
                           foreach ( glob( $preview_file . '/*' ) as $autoedit_file ){
                              rename( $autoedit_file,  $org_source . 'autoedit/' . basename( $autoedit_file ) );
                           }
                           rmdir( $preview_file );
                        }
                     }
                     rmdir( $convert );
                     if( file_exists( sprintf( '%sCondition.txt', $org_source ) ) ||  $to_do_order['article_id'] == 7007 ){
                        if( !file_exists( $preview ) ){
                           mkdir( $preview , 0755, true );
                        }
                        
                        
                        $count = 0;
                        
                        foreach ( glob( $org_source . '*' ) as $preview_file ){

                           $basename = basename( $preview_file );                           
                           $basenamearr = explode( '-', $basename );
                           $antall = (int)$basenamearr[0];
                           
                           
                           if( (int)$basenamearr[4]  == 0  ){
                              $count++;
                           }
                           
                           if( !is_dir( $preview_file ) ){
                              if( in_array( $to_do_order['article_id'], $calenderarray  )  && $antall > 1 ){
                                 
                                 for ($i = 1; $i <= $antall; $i++){
                                    $newbasename = sprintf( "%d%03d-" ,$count, $i ) . $basename;
                                    if( !file_exists($preview . $newbasename ) ){
                                       symlink( $preview_file,  $preview . $newbasename );
                                    }
                                 }
                                    
                              }else if( ( in_array( $to_do_order['article_id'], $calenderarray  )  &&  $antall == 1 ) && pathinfo( $preview_file, PATHINFO_EXTENSION  ) == 'jpg' ){
                                 //util::Debug( $preview_file );
                                 $basename = sprintf( "%d000-" ,$count ) . $basename;
                                 symlink( $preview_file,  $preview . $basename );
                              }else{
                                 symlink( $preview_file,  $preview . $basename );
                              }
                           }
                        }
                     }
                     $conversions->status = 'done';     
                  }catch ( Exception $e ){       
                     $conversions->status = 'void';  
                  }
                  $conversions->ended_at = date( 'Y-m-d H:i:s' );
                  $conversions->save();
               
               }
               
            }
            else{
                  
                  if( strtotime( $conversions->added_at . ' +72 hour' ) <  strtotime( date('Y-m-d H:i:s')  ) ){
                     
                     Util::debug("***************VOID***********");
                     
                     $conversions->status = 'void';
                     $conversions->save();
                  }
               
                  
               }
         }
      }
      
      
      
      
      private function StabburetLeverpostei( $filename, $org_source, $convert, $orderid ){
         import( 'website.giftpagetemplate' );
         import( 'website.giftordertemplate' );
         //library( 'pdf.fpdf' );
         library( 'pdf.fpdi' );
         //Util::Debug( "STABBURET" );
         //Util::Debug( $filename );
         
         Util::Debug( $filename );
         $artnr =  explode( '_' ,  dirname( $filename ) );
         $artnr = $artnr[2];
         Util::Debug( $artnr );
         //die();

         if( $artnr == 7196 || $artnr == 7756 ){
            $res = 300;
         }else{
            $res = 150;
         }
         
            
         $file = basename( $filename . "autoedit/"  );
        
         $path = $filename . "autoedit/";
         
         
         $fileinfo = explode( '_', $file );
         $white = new ImagickPixel('white');
         
         
         $orderinfo = DB::query( "SELECT * FROM historie_mal WHERE ordrenr = ? AND artikkelnr = ? ORDER BY ordrenr DESC" , $orderid, $artnr )->fetchAll( DB::FETCH_ASSOC );
         $orderinfo  = $orderinfo[0];
          
         $templateOrder = new GiftOrderTemplate( $orderinfo['lopenummer'] );
         
         $malidtext = explode( '_', $templateOrder->printtype );
         
         Util::Debug($malidtext);
         
         $size = DB::query( 'SELECT lokksize FROM leverpostei_order WHERE id = ?', $malidtext[1] )->fetchSingle();
         
         
         //Util::Debug( $size );
         
         
         if( $size == 'large'  && $artnr == 7196  ){
            $templateOrder->malid = 2552;
         }
         else if( $artnr == 7756 ){
            $templateOrder->malid = 3160;
         }
         else if( $artnr == 7303 ){
            $templateOrder->malid = 2694;
         }
         
         $malpageid = DB::query( "SELECT malpageid FROM malpage WHERE malid = ? AND pagenr = ?", $templateOrder->malid, $templateOrder->page )->fetchSingle();

         Util::Debug( $malpageid );         
         
         $giftTemplate = GiftPageTemplate::fromTemplateIdAndPageId( $templateOrder->malid , 0);
         
         //Util::Debug( '/data/global/maler/orginal/' . $giftTemplate->fullsize_src );
         
         $templatefile = str_replace( '-|-', '', $templateOrder->text );
         
         
         if( !$templatefile ){
            
            $lopenummer = DB::query( "SELECT  lopenummer FROM historie_mal WHERE ordrenr = ? AND artikkelnr = 7196", $orderid )->fetchSingle();
            
            $templatefile = DB::query( "SELECT text FROM mal_order WHERE malorderid = ?" , $lopenummer )->fetchSingle();
            
            $templateOrder->text = $templatefile;
            $templateOrder->save();
            
            $templatefile = str_replace( '-|-', '', $templatefile );
            
            
         }
         
         copy( '/home/produksjon/stabburet/maler/' . $templateOrder->malid . '.png' ,$convert .  "autoedit/" . $giftTemplate->fullsize_src );
         
         /*if( !file_exists( $org_source .  "autoedit/" . $giftTemplate->fullsize_src ) ){
            
            Util::Debug( '/data/global/maler/orginal/' . $giftTemplate->fullsize_src );
            
            $connection = ssh2_connect('eva.eurofoto.no', 22);
            ssh2_auth_password($connection, 'toringe', 'bbc460');
            
            ssh2_scp_recv($connection, '/data/global/maler/orginal/' . $giftTemplate->fullsize_src , $convert .  "autoedit/" . $giftTemplate->fullsize_src );  
            
            ssh2_exec($connection, 'exit');
            
         }*/
         
         //$templatefile = sprintf( '%s_%s_%s.png', $malpageid,  $templateOrder->malid, $templateOrder->page );
         
         $templatefile = $giftTemplate->fullsize_src;
         
         //Util::Debug( $templatefile );
         
         $filtype = DB::query( "SELECT filtype  from bildeinfo where bid = ?" , $orderinfo['bid'] )->fetchSingle();
         $imagefile = sprintf( 'org_%s.%s' , $orderinfo['lopenummer'], $filtype );  
               
         
         if( !file_exists( $imagefile ) ){
            $imagepath = DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", $orderinfo['bid'] )->fetchSingle();
            //$url = $this->imageserver .  "?path=" . base64_encode($imagepath) . "&secure=" . $this->securecode;
            
            //file_put_contents( $imagefile, file_get_contents($url) );
            
            $connection = ssh2_connect($this->imgserver, 22);
            ssh2_auth_password($connection, 'www', 'Kefir4ever!');
            
            ssh2_scp_recv( $connection, '/data/bildearkiv/' . $imagepath , $imagefile);
            
         }
         
         if( $templateOrder->editor_x == 0 ){
               $templateOrder->editor_x = 439;
         }
         $template = new Imagick( $templatefile );
         $ratio =  $template->width / $templateOrder->editor_x;
         $dx = $templateOrder->dx * $ratio;
         $dy = $templateOrder->dy * $ratio;
         
         
         //Util::Debug("RAtio leverpostei");
         //Util::Debug($ratio);
         //Util::Debug( $template->width );
         
         
         $imagefile = new Imagick( $imagefile );
         $imagefile->scaleImage( $dx,  $dy );
         $imagefile->rotateImage(new ImagickPixel('none'), $templateOrder->rotate );
         
         if( !file_exists($org_source .  "autoedit")){
            mkdir( $org_source .  "autoedit" , 0755, 1 );
         }
         $imagefile->writeImage( $org_source .  "autoedit/image.jpg" );
        
         $x =  $templateOrder->x * $ratio - ( $imagefile->width / 2 );
         $y =  $templateOrder->y * $ratio - ( $imagefile->height / 2 );
         
         $image = new Imagick();
         $image->setResolution($res,$res);
         $image->newImage($template->width, $template->height, $white );
         
         //Util::Debug( $template->width );
         //Util::Debug( $template->height );
         
         $image->compositeImage( $imagefile, $imagefile->getImageCompose() , $x , $y );
         $image->compositeImage( $template, $template->getImageCompose() , 0 , 0 );
         
         //Util::Debug( $org_source .  "test.jpg" );
         
         $image->writeImage( $org_source .  "autoedit/image_and_template.jpg" );
         
         if(  $orderinfo['artikkelnr'] != 7756 ){
         
         $draw = new ImagickDraw();
         $draw->setFillColor('white');
         $draw->setFont('/home/produksjon/FairplexWideOT-Med.otf');
         $draw->setFontWeight (900);
         
         $text = explode( '-|-' , $templateOrder->text );
         
         $name = strtoupper( $text[0] );
         
            $name = str_replace( 'ø', 'Ø' , $name );
            $name = str_replace( 'å', 'Å' , $name );
            $name = str_replace( 'æ', 'Æ' , $name );
            $name = str_replace( '<U+1F600>', ':)' , $name );
            $name = str_replace( '<U+1F601>', ':)' , $name );
            $name = str_replace( '<U+1F602>', ':)' , $name );
            $name = str_replace( '<U+1F603>', ':)' , $name );
            $name = str_replace( '<U+1F604>', ':)' , $name );
            $name = str_replace( 'U+1F60D', ':)' , $name );
            $name = str_replace( 'U+2764', '<3' , $name );
            $name = str_replace( 'U+1F618', ':)' , $name );
            
            $name = str_replace( '☀️', '*' , $name );
            $name = str_replace( '👍', '^' , $name ); 
            $name = str_replace( '❤️', '<3' , $name );
            $name = str_replace( '’', "'" , $name );
         
            $name = utf8_decode( $name );
            
            ///Util::Debug( utf8_encode( $name )  );
   
            $year = $text[1];
            $fontsize = 110;
            $canvas = new Imagick();
            $textx = 878;
            $textimage = new Imagick();
            $textimage->newImage( $textx, $textx, new ImagickPixel( none ) );
            //$textimage->annotateImage($draw, 400 , 400, 180, $text);
            $letters = str_split($name);
            $rotate = 0;
            $i =0;
            
            for ($i = 0; $i <= count( $letters ); $i++) {
            
            //foreach( $letters as $letter ){
                $draw->setFontSize( $fontsize  );
                $metricsl = $canvas->queryFontMetrics( $draw, $letters[$i] );
                $metrics2 = $canvas->queryFontMetrics( $draw, $letters[$i + 1] );
                
                $textimage->rotateImage( new ImagickPixel('none'), $rotate);
                $rotate = ( $metricsl['textWidth'] / 4.5 )  + 6 ;
                
                $letterw  = ($metrics2['textWidth']/2) + ($metricsl['textWidth']/2);
                
                $rotate = rad2deg( tan( $letterw / 464 ) ) + 6;
                $textimage->cropImage( $textx, $textx, -1 , 0  );
                if( $letters[$i] == 'I555'){
                  $letter_left = ( $textx  / 2 )   -  30;
                }else{
                  $letter_left = ( $textx  / 2 )   -  ( $metricsl['textWidth'] / 2 );
                }
                $draw->setFillColor('#1f1f1f');
                $draw->setFillOpacity (4);
                $textimage->annotateImage($draw, $letter_left + 3  , 763 + 6, 0, $letters[$i]);
                $draw->setFillOpacity (10);
                $draw->setFillColor('white');
                $textimage->annotateImage($draw, $letter_left  , 763, 0, $letters[$i]);
                $totalrotate +=  $rotate;
            }
            
            $textimage->rotateImage(new ImagickPixel('none'), - ( $totalrotate / 2 ) + 0 );
            $textimage->cropImage( $textx, $textx , 0 , 0  );
            $textimage->scaleImage( $template->width,  $template->width );
            
            //yeartext
            $yeartext = "ORIGINALEN SIDEN " . $year;
            $draw->setFillColor('#e12f20');
            $letters = str_split($yeartext);
            $rotate = 0;
            $fontsize = 44;
            $totalrotate = 0;
            $yearimage = new Imagick();
            $yearimage->newImage( $textx, $textx, new ImagickPixel( none ) );
            foreach( $letters as $letter ){

                $draw->setFontSize( $fontsize  );
                $metricsl = $canvas->queryFontMetrics( $draw, $letter );
                $yearimage->rotateImage( new ImagickPixel('none'), $rotate);
                //$rotate = ( $metricsl['textWidth'] / 4.5 )  + 6 ;
                $rotate = rad2deg( tan( $metricsl['textWidth']  / 450 ) ) + 1;
                $yearimage->cropImage( $textx, $textx, -1 , -1  );
                if( $letter == 'I'){
                  $letter_left = ( $textx  / 2 )   -  10;
                }else{
                  $letter_left = ( $textx  / 2 )   -  ( $metricsl['textWidth'] / 2 );
                }
                $yearimage->annotateImage($draw, $letter_left  , 830, 0, $letter);
                $totalrotate +=  $rotate;
            }
             
            $yearimage->rotateImage(new ImagickPixel('none'), - ( $totalrotate / 2 )   + 2.5 );
            $yearimage->cropImage( $textx, $textx , 0 , 0  );
            $yearimage->scaleImage( $template->width,  $template->width );
            $yearimage->scaleImage( $template->width,  $template->width );
            
            
            $image->compositeImage( $textimage, $textimage->getImageCompose() , 0 , 0 );
            $image->compositeImage( $yearimage, $yearimage->getImageCompose() , 0 , 0 );
         }
            if(  $orderinfo['artikkelnr'] == 7196 ){
               $label = new ImagickDraw();
               $label->setFillColor('black');
               $label->setFontSize( 40 );
               $image->annotateImage($label, 5, 45 , 0, $orderinfo['ordrenr'] );
               
            }
            $image->setImageCompressionQuality( 100 );
            //$image->setResolution(150,150); 
            $image->writeImage( $org_source .  "autoedit/image_and_template_text.jpg" );
            
            $finalfile = sprintf( "%03d-%d-%d-%d-%03d.jpg" , $orderinfo['antall'], $orderinfo['ordrenr'], $orderinfo['lopenummer'], $orderinfo['artikkelnr'], $orderinfo['page']  );
            
            copy( $org_source .  "autoedit/image_and_template_text.jpg" , $org_source . $finalfile );
            
            
            $orderdate = DB::query( "SELECT tidspunkt FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
            
            $hotfoldergave = "/home/produksjon/stabburet_gave/" . date( 'Y-m-d' , strtotime($orderdate) ) . "/" . $orderinfo['artikkelnr'] ;
            $hotfolderskanska = "/home/produksjon/skanska/" . date( 'Y-m-d' , strtotime($orderdate) ) . "/" . $orderinfo['artikkelnr'] ;
            
            if( !file_exists( $hotfoldergave ) ){
               mkdir( $hotfoldergave, 0775, true );
            }
            
            
             if( !file_exists( $hotfolderskanska ) ){
               mkdir( $hotfolderskanska, 0775, true );
            }
            
               
            if(  $orderinfo['artikkelnr'] == 7303 ){
               Util::Debug( $templateOrder->malid  );
               $size = 75;
               $cutFilename = "/home/produksjon/stabburet/kuttekanta/matboks.pdf" ; 
               $pdf = new FPDI( 'P', 'mm', array( $size, $size ) );
               $pdf->AddPage();
               $pdf->Image( $org_source . $finalfile , 0, 0, -150 );
               
               $pdf->setSourceFile( $cutFilename );
               $tplIdx = $pdf->importPage(1);
               $pdf->useTemplate($tplIdx, 0, 0, 0);
               
               $outFile = $hotfoldergave . '/' . sprintf( "%d-%03d-%d-%d-%03d.pdf" , $orderinfo['ordrenr'], $templateOrder->malid , $orderinfo['lopenummer'], $orderinfo['artikkelnr'], $orderinfo['page']  );
               $pdf->Output( $outFile , 'F');
               
            }
            
            
            
                 if(  $orderinfo['artikkelnr'] == 7756 ){
               Util::Debug( $templateOrder->malid  );
                $size = 79.8;
                 $size2 = 95.7;
    
          $cutFilename = "/home/produksjon/skanska/kuttefil.pdf" ; 
               $pdf = new FPDI( 'P', 'mm', array( $size, $size2 ) );
               $pdf->AddPage();
               $pdf->Image( $org_source . $finalfile , 0, 0, -150 );
               
               $pdf->setSourceFile( $cutFilename );
               $tplIdx = $pdf->importPage(1);
               $pdf->useTemplate($tplIdx, 0, 0, 0);
               
               $outFile = $hotfolderskanska . '/' . sprintf( "%d-%03d-%d-%d-%03d.pdf" , $orderinfo['ordrenr'], $templateOrder->malid , $orderinfo['lopenummer'], $orderinfo['artikkelnr'], $orderinfo['page']  );
               $pdf->Output( $outFile , 'F');
               
            }
            
            
            
            
            
           
            
            
            else if(  $orderinfo['artikkelnr'] == 7196 ){
                //$hotfolder = "/home/produksjon/stabburet/" . date( 'Y-m-d' , strtotime($orderdate) );
               
               $hotfolder = "/home/produksjon/stabburet/" . date( 'Y-m-d' );
             //  $hotfolder2 = "/home/produksjon/stabburet_hotfolder";
              
                // $hotfolder3 = "/mnt/debian/hotfolder/Latex 315/Stabburet";
               
               Util::Debug( $templateOrder->malid  );
               
               if( $templateOrder->malid == 2553 ){
                  $size = 74.3;
                  $cutFilename = "/home/produksjon/stabburet/kuttekanta/tynnkant_stabburet_2016c.pdf" ; 
               }else{
                  $size = 87.4;
                  //stirt stabburet lokk
                  $cutFilename = "/home/produksjon/stabburet/kuttekanta/tynnkant_stabburet_2016_stor.pdf" ; 
               }
               //create pdf printfile
               
               $pdf = new FPDI( 'P', 'mm', array( $size, $size + 25 ) );
               $pdf->AddPage();
               $pdf->Image( $org_source . $finalfile , 0, 0, -300 );
          
               
               
               $badcodetext = base_convert( $orderinfo['ordrenr'], 10, 36  ) . " " . $orderinfo['ordrenr'];
               
                  $pdf->Code39(5, $size + 4,$badcodetext,0.8,11);
              // $pdf->Code39(15, $size + 4,$badcodetext,0.6,10);
              
               // $pdf->Code39(15, $size + 4,$badcodetext,0.6,10);
              
               // $pdf->Code39(15= kor langt fra venstre,
               //$size + 4,$badcodetext,
               //0.6=bredda på koden,10=høgda på koden);
               
               $pdf->setSourceFile( $cutFilename );
               $tplIdx = $pdf->importPage(1);
               $pdf->useTemplate($tplIdx, 0, 0, 0);

               if( !file_exists( $hotfolder ) ){
                  mkdir( $hotfolder );
               }
               $outFile = $hotfolder . '/' . sprintf( "%d-%03d-%d-%d-%03d.pdf" , $orderinfo['ordrenr'], $templateOrder->malid , $orderinfo['lopenummer'], $orderinfo['artikkelnr'], $orderinfo['page']  );
            //   $outFile2 = $hotfolder2 . '/' . sprintf( "%d-%03d-%d-%d-%03d.pdf" , $orderinfo['ordrenr'], $templateOrder->malid , $orderinfo['lopenummer'], $orderinfo['artikkelnr'], $orderinfo['page']  );
              // $outFile2 = $hotfolder3 . '/' . sprintf( "%d-%03d-%d-%d-%03d.pdf" , $orderinfo['ordrenr'], $templateOrder->malid , $orderinfo['lopenummer'], $orderinfo['artikkelnr'], $orderinfo['page']  );

               Util::Debug( $outFile );
               
               $pdf->Output( $outFile , 'F');
             //  $pdf->Output( $outFile2 , 'F');
            //   $pdf->Output( $outFile3 , 'F');
               
               //copy( $org_source . $finalfile,  $hotfolder . '/' . $finalfile );
               
               
            }else{
               copy( $org_source . $finalfile, $hotfoldergave . "/" . $finalfile );
            }


            foreach ( glob( $convert . '*' ) as $preview_file ){
                        if( !is_dir( $preview_file ) ){
                           if( file_exists( $preview_file ) ){
                              unlink( $preview_file );  
                           }
                           
                        }else{
                           foreach ( glob( $preview_file . '/*' ) as $autoedit_file ){
                              if( file_exists( $autoedit_file ) ){
                                 unlink( $autoedit_file );
                              }
                              
                           }
                           
                           if( file_exists($preview_file)){
                              rmdir( $preview_file );
                           }
                           
                        }
                     }
                     
                     if( file_exists($convert ) ){
                        rmdir( $convert ); 
                     }
            }
      
      

   }
   

   CLI::Execute();

?>