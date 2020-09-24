<?PHP
/*******************************
 * This script moves all orders
 * to correct location.
 * 
 *****************************/

config( 'production.settings');
import( 'website.order' );
import( 'production.c8' );
import( 'website.order.image' );
import( 'website.order.orderrow' );

class Container {
   
   public $orderid = 0;
   public $photolab = '';
   public $size = '';
   public $order = '';
   public $destination = '';
   public $source = '';
   public $separator_file = array( 'xyz_promo1.jpg', 'xyz_promo2.jpg', 'xyz_promo3.jpg', 'xyz_promo4.jpg' ,'xyz_promo5.jpg' );
   public $webspoolFolder = '/home/produksjon/webspool/';
    public $stabburetFolder = '/home/produksjon/stabburet/';
     public $studentFolder = '/home/produksjon/webspool/Studentskilt/';
   
   public function C8Print( $orderid, $container ){
      $this->orderid = $orderid;
      $this->tidspunkt = DB::query("SELECT tidspunkt FROM historie_ordre WHERE ordrenr =?", $orderid )->fetchSingle();
      $this->kode = DB::query("SELECT kampanje_kode FROM historie_ordre WHERE ordrenr =?", $orderid )->fetchSingle();
      $this->userid = DB::query("SELECT uid FROM historie_ordre WHERE ordrenr =?", $orderid )->fetchSingle();
      foreach ($container as $printsize=>$articles ){
         $std_article = '';
         foreach ( $articles as $article ){
              $std_article .= $article .',';
         }
         if( $printsize == 'default_print'){
            $print_container = $this->StdPrint( $articles );
            $papertype = $this->papertype();
            //util::Debug( $papertype );
            $this->destination = sprintf( '%sc8/%s/%s/%d_%s/' ,$this->webspoolFolder, $this->photolab, $papertype ,$this->orderid, $this->size );
            
        
        /*
            if( $this->kode == 'Netlife' ){
               $previewfolder = $this->webspoolFolder . 'Netlife_preview';
               
               $this->destination = sprintf( '%s/%s/%s_%s/' , $previewfolder , date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article );
            }
            
            */
            
            util::Debug($this->destination);
            if( !file_exists( $this->destination )){
               mkdir( $this->destination, 0755 , true );
            }
            
            $articlelist = $this->PrintArticlelist( $std_article );
            
            foreach ( $articlelist as $imageinfo ){

               $this->source = sprintf('%s%s/%d/%d/',  $this->webspoolFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $imageinfo['artikkelnr'] );
               if( $imageinfo['dx'] > 0 && file_exists( $this->source . $imageinfo['filename'] ) ){
                  //util::Debug( $this->source . $imageinfo['filename'] );
                  $imageMagic = new Imagick( $this->source . $imageinfo['filename'] );
                  $imageMagic->cropImage( $imageinfo['dx'], $imageinfo['dy'], $imageinfo['x'] , $imageinfo['y'] );
                  $imageMagic->writeImage( $this->destination . $imageinfo['filename'] );
                       
               }else{
                  try{
                     if( file_exists($this->source . $imageinfo['filename']) && !file_exists( $this->destination . $imageinfo['filename'] ) ){
                        symlink( $this->source . $imageinfo['filename'] , $this->destination . $imageinfo['filename'] );
                     }
                     
                  }catch( Exception $e){
                     util::Debug( $e->getMessage() );
                  }
               }
            }
            $this->c8( $articlelist );
         }else{
            $articlelist = $this->PrintArticlelist( $std_article );
            foreach ( $articlelist as $imageinfo ){
               
               if( $this->kode == 'RF-001' ){
                  $previewfolder = $this->webspoolFolder . 'preview';
               }
               else if( $this->userid == '1030157' ){
                  $previewfolder = $this->webspoolFolder . 'Smil_preview';
               }
               
                else if( $this->kode == 'STU-SV' ){
                  $previewfolder = $this->studentFolder . 'Inspool';
               }
               
               
               
               else if( $this->kode == 'ST-001' ){
                  $previewfolder = $this->stabburetFolder . 'Inspool';
               }
               
               
               
                else if( $this->userid == '1030136' && '1030138' && '1030146' && '1030148' && '1030149' &&'1030150' && '1030151' && '1030152' && '1030154' && '1030155' && '1030156' && '1030159' && '1030160' && '1033715' && '1033716' && '1033724'  &&  '1039217' &&  '1034717' &&  '1034721' &&  '1034722' &&'1034920' && '1034922' && '1034957' && '1043366' && '1035956' && '1034929' && '1034743' && '1043368'&& '1034911' && '1034952' &&'1041409' && '1034917' && '1037991' && '1037998' && '1090600' &&'1105325' && '1114803' && '1120915' && '1131937' && '1132808' ){
                  $previewfolder = $this->webspoolFolder . 'Netlife_preview';
               }
               
               
               else if( $this->userid == 983136 ){
                  $previewfolder = $this->webspoolFolder . 'fovea_preview';
               }else{
                  $previewfolder = $this->webspoolFolder . 'preview_enhance';
               }
               
               $this->destination = sprintf( '%s/%s/%s_%s/' , $previewfolder , date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $imageinfo['artikkelnr'] );

               if( !file_exists( $this->destination )){
                  mkdir( $this->destination, 0755 , true );
               }
               
               $this->source = sprintf('%s%s/%d/%d/',  $this->webspoolFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $imageinfo['artikkelnr'] );
               if( $imageinfo['dx'] > 0 && file_exists( $this->source .$imageinfo['filename'] )){ 
                  $imageMagic = new Imagick( $this->source .$imageinfo['filename'] );
                  $imageMagic->cropImage( $imageinfo['dx'], $imageinfo['dy'], $imageinfo['x'] , $imageinfo['y'] );
                  $imageMagic->writeImage( $this->destination . $imageinfo['filename'] );
               }else{
                  //util::Debug( $this->source . $imageinfo['filename'] . " " . $this->destination . $imageinfo['filename'] );
                  try{
                     if( file_exists($this->source . $imageinfo['filename']) && !file_exists( $this->destination . $imageinfo['filename'] ) ){
                        symlink( $this->source . $imageinfo['filename'] , $this->destination . $imageinfo['filename'] );
                     }
                  }catch ( Exception $e){
                     util::Debug( $e->getMessage() );
                  }
               }
               $this->photolab = 'MyDevice';
               $this->c8( $articlelist );
            }
         }	
      }
   }
   
   public function C8Mal( $orderid, $container, $tidspunkt ){
      $malcontainer = Settings::Get( 'production' , 'malprintsize' );
      $this->userid = DB::query("SELECT uid FROM historie_ordre WHERE ordrenr =?", $orderid )->fetchSingle();
      $this->kode = DB::query("SELECT kampanje_kode FROM historie_ordre WHERE ordrenr =?", $orderid )->fetchSingle();
      $this->orderid = $orderid;
      $this->tidspunkt = $tidspunkt;
      
      
      Util::Debug($container);
      
      foreach ($container as $article ){
            
         if( $this->userid == 983136 ){
            $this->destination = sprintf( '/home/produksjon/fovea_preview/%s/%s_%s/', $tidspunkt, $orderid , $article );
            foreach ( $malcontainer as $size=>$articlelist ){
               $articles[$item['artikkelnr']] = 1;
               if( in_array( $article , $articlelist ) ){
                  $this->photolab = 'MyDevice';
                  
                  $imagelist = DB::query( "SELECT * FROM historie_bilde WHERE ordrenr = ? AND artikkelnr = ?" ,$orderid,  $article )->fetchAll( DB::FETCH_ASSOC );
                  
                  $this->C8( $imagelist );
               }
            }
         }
         else if( $this->kode == 'Netlife' && DB::query( "SELECT * FROM historie_bilde WHERE ordrenr = ? AND artikkelnr = ?" ,$orderid,  $article )->fetchAll( DB::FETCH_ASSOC )){
            
            
            $tidspunkt = date( 'Y-m-d', strtotime($tidspunkt) );
            
            $this->destination = sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s/', $tidspunkt, $orderid , $article );
            $this->destination = sprintf( '/home/produksjon/webspool/Netlife_preview/C8_Netlife/%s_%s/', $tidspunkt, $orderid , $article );
            foreach ( $malcontainer as $size=>$articlelist ){
               $articles[$item['artikkelnr']] = 1;
               if( in_array( $article , $articlelist ) ){
                  $this->photolab = 'MyDevice';
                  
                  $imagelist = DB::query( "SELECT * FROM historie_bilde WHERE ordrenr = ? AND artikkelnr = ?" ,$orderid,  $article )->fetchAll( DB::FETCH_ASSOC );
                  
                  $this->C8( $imagelist );
               }
            }
         }
         else if($this->kode == 'Netlife' ){
            $tidspunkt = date( 'Y-m-d', strtotime($tidspunkt) );
            $this->destination = sprintf( '/home/produksjon/webspool/Netlife_preview/%s/%s_%s/', $tidspunkt, $orderid , $article );
            
            
            foreach ( $malcontainer as $size=>$articlelist ){
               $articles[$item['artikkelnr']] = 1;
               if( in_array( $article , $articlelist ) ){
                  $this->photolab = 'MyDevice';
                  $this->c8( $this->MalArticlelist( $article ) );
               }
            }
            
         }
         
         
         
         else if($this->kode == 'ST-001' ){
            $this->destination = sprintf( '%sInspool/%s_%d_%d/' , $this->stabburetFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article);
         
            if( !file_exists( $this->destination )){
               mkdir( $this->destination, 0755 , true );
            }
            $this->source = sprintf('%s%s/%d/%d/*', $this->webspoolFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article );
            exec( sprintf( 'cp -r %s %s', $this->source, $this->destination ) );
            
            foreach ( $malcontainer as $size=>$articlelist ){
               $articles[$item['artikkelnr']] = 1;
               if( in_array( $article , $articlelist ) ){
                  $this->photolab = 'MyDevice';
                  $this->c8( $this->MalArticlelist( $article ) );
               }
            }
         }
         
         
         else if($this->kode == 'STU-SV' ){
            $this->destination = sprintf( '%sInspool/%s_%d_%d/' , $this->studentFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article);
         
            if( !file_exists( $this->destination )){
               mkdir( $this->destination, 0755 , true );
            }
            $this->source = sprintf('%s%s/%d/%d/*', $this->webspoolFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article );
            exec( sprintf( 'cp -r %s %s', $this->source, $this->destination ) );
            
            foreach ( $malcontainer as $size=>$articlelist ){
               $articles[$item['artikkelnr']] = 1;
               if( in_array( $article , $articlelist ) ){
                  $this->photolab = 'MyDevice';
                  $this->c8( $this->MalArticlelist( $article ) );
               }
            }
         }
         
         
         
         else{
            $this->destination = sprintf( '%senhance/%s_%d_%d/' , $this->webspoolFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article);
         
            if( !file_exists( $this->destination )){
               mkdir( $this->destination, 0755 , true );
            }
            $this->source = sprintf('%s%s/%d/%d/*', $this->webspoolFolder, date('Y-m-d', strtotime(  $this->tidspunkt )), $this->orderid, $article );
            exec( sprintf( 'cp -r %s %s', $this->source, $this->destination ) );
            
            foreach ( $malcontainer as $size=>$articlelist ){
               $articles[$item['artikkelnr']] = 1;
               if( in_array( $article , $articlelist ) ){
                  $this->photolab = 'MyDevice';
                  $this->c8( $this->MalArticlelist( $article ) );
               }
            }
         }
         
         //util::Debug( $this->destination );
         
         $fe = fopen( $this->destination . "Proceed.txt","w");
         fwrite($fe,"");
         fclose($fe);
         
         
      }
   }

   private function StdPrint( $articles  ){
      
      //10x13 419
      //10x15 1
      //10x10 14
      //15x20 2
      //15x15 7345
      //10x18 6
      
      //10cm 10x13, 10x15, 10x10, 
      
      
      $sizearray = array(
         419 => "10cm",
         14 => "10cm",
         2 => "15cm",
          522 => "10cm",
         7345 => "15cm",
         6 => "10cm"
      );
      
      
      $comb = array();
      
      foreach( $articles as $articleid ){
         if( $articleid != 1 ){
            $comb[$sizearray[$articleid]] = $sizearray[$articleid];
         }
         
      }
      
      /*
      $combinations_array = array( 
         '10cm' => array( 419, 420, 14, 15, 433, 434 ), //13 cm= arikkelnr 419, 13cm + 15cm = 419 + 1 = 420 osv.
         '15cm' => array( 1, 2, 3, 7345, 7346, 7347, 7348 ),
         'both' => array( 422, 421 )
      );
      foreach ( $combinations_array as $size=>$combinations ){
         if( in_array( array_sum( $articles ),  $combinations ) ){
            $print_container = $size;
         };
      }
      */
      
      
      //Util::Debug( $comb );
      //Util::Debug( "count" . count($comb) );
      
      if( count($comb) == 0 ){
         $print_container = '15cm';
      }
      else if( count($comb) == 1 ){
         $print_container =  key($comb);
      }
      
      
      
       else{
         $print_container = 'both';
      }
      //Util::Debug( $print_container );
      
      switch( $print_container ){
               case '10cm':
                  if($this->orderid % 2 == 0){//partallsordrer
                     $this->photolab = '390_2'; 
                  }else{//oddetallsordrer
                     //$this->photolab = '390_3';
                     $this->photolab = '390_2'; 
                  }
                  break;
               case '15cm':
                  $this->photolab = '390_1';
                  break;
              
                case 'both':
                  $this->photolab = '570_1';
                  break;     
        }
        
        $this->size = $print_container;
   }
   
   private function PrintArticlelist( $article ){
      
      $images = array();
      $article = rtrim( $article, ',' );
      
      foreach ( DB::query( sprintf( "SELECT * FROM historie_bilde where ordrenr = ? AND artikkelnr in(%s) ORDER BY artikkelnr, exif_date", $article ), $this->orderid  )->fetchAll( DB::FETCH_ASSOC ) as $item ) {  
            $images[] = $item;
      } 
      return $images;
      
   }
   
   private function MalArticlelist( $article ){
      
      
      $calenderarray = Settings::Get( 'production', 'calenderarray' );

      $images = array();
      $article = rtrim( $article, ',' );
      
      foreach ( DB::query( sprintf( "SELECT * FROM historie_mal where ordrenr = ? AND artikkelnr in(%s) ORDER BY filnamn", $article ), $this->orderid  )->fetchAll( DB::FETCH_ASSOC ) as $item ) {  
            $images[] = $item;
      }
      $imagesarray = array();
      
      $count = 0;;
      foreach( $images as $ret ){
         if( $ret['page'] == 0 ){
            $count++;
         }
         $antall = $ret['antall'];
         $artikkelnr = $ret['artikkelnr'];
         $filename = $ret['filnamn'];
         
         if( strpos( $ret['text'], 'Lablink' ) ){
            $imagesarray[] = $ret;
         }
         else if(  in_array(  $artikkelnr, $calenderarray )  && $antall > 1 ){
            for ($i = 1; $i <= $antall; $i++){
               //foreach( $images as $image ){
                  $ret['filnamn'] = sprintf( "%d%03d-" ,$count, $i ) . $filename;
                  $ret['antall'] = 1;
                  $ret['sort']  = sprintf( "%d%02d%02d" ,$count, $i , $ret['page'] );
                  $imagesarray[] = $ret;
               //}         
            }
         }
         else if(  in_array(  $artikkelnr, $calenderarray )  && $antall == 1 ){
            $ret['filnamn'] = sprintf( "%d000-" ,$count ) . $filename;
            $ret['sort']  = sprintf( "%d%02d%02d", $count, 0 , $ret['page'] );
            $imagesarray[] = $ret;
         }
         else{
            $imagesarray[] = $ret;
         }
      }
      
      
      usort($imagesarray, function($a, $b) {
         return $b['sort'] - $a['sort'];
      });
      
      ///$this->subval_sort( $imagesarray, 'filnamn' );
      
      //Util::Debug($imagesarray);
      //die();
      
      return $imagesarray;
      
      //$orderImages = OrderImage::fetchPrArt( $this->orderid, $article );
     
      
   }
   
   public function C8( $list ){
      //die();
      
      $labInfo = Settings::GetSection( 'production' );
      $device = $labInfo['fotolab'][$this->photolab];
      
      
      $uid = DB::query( "SELECT uid FROM historie_ordre where ordrenr =? ",  $this->orderid  )->fetchSingle();
      
      
      $condition	= $this->destination . "Condition.txt";
      $papertype = $this->papertype( $orderid );
                
      /*if( $this->kode == 'RF-001' ){
         $backprint1 = "www.reedfoto.no ";
         $backprint2 = $image['tittel'];
         $colorspace = "sRGB";
      }
      else if( $image['uid'] == 969748 ){
         $backprint1 = "www.utestemme.no ";
         $backprint2 = $image['tittel'];
         $colorspace = "sRGB";
      }
      else{
         $backprint1 = "www.eurofoto.no ";
         $backprint2 = date( 'Y-m-d H:i:s', strtotime(  $image['exif_date'] ) ) . " " .$image['tittel'];
         $colorspace = "sRGB";
      }*/
    	
		
      $imagelist_count  = 0;
      
      foreach ($list as $image){
         
         if( $this->kode == 'RF-001' ){
            $backprint1 = "www.reedfoto.no ";
            $backprint2 = $image['tittel'];
            $colorspace = "PD";
         }
         else if( strpos( $image['manualpath'], "utestemme" ) > 0  ){
            $backprint1 = "www.utestemme.no ";
            $backprint2 = $image['tittel'];
            $colorspace = "sRGB";
         }
         else if( strpos( $image['manualpath'], "fovea" ) > 0  ){
            $backprint1 = "www.fovea.no ";
            $backprint2 = $image['tittel'];
            $colorspace = "sRGB";
            $this->photolab = "MyDevice";
         }
         else{
            $backprint1 = "www.repix.no ";
            $backprint2 = date( 'Y-m-d H:i:s', strtotime(  $image['exif_date'] ) ) . " " .$image['tittel'];
            $colorspace = "sRGB";
         }
         
         /*if( $this->kode == 'RF-001' ){
            $backprint2 = $image['tittel'];
         }else{
            $backprint2 = date( 'Y-m-d H:i:s', strtotime(  $image['exif_date'] ) ) . " " .$image['tittel'];
         }*/
         
        if( $image['artikkelnr'] == 693 || $image['artikkelnr'] == 7254 || $image['artikkelnr'] == 1318 || $image['artikkelnr'] == 7347 || $image['artikkelnr'] == 7551 || $image['artikkelnr'] == 7298 ){
            $resize = 'NONE'; 
        }
        else if( $image['fitin'] ){
            $resize = 'FITIN';
        }else{
            $resize = 'FILLIN';   
        }
		   
   	 if( $image['filename'] ){
            $filename = $image['filename'];
         }else{
            $filename = $image['filnamn'];
         }
	 
         $sizename = null;
         //select printsizes
         if( $papertype == 'mp' ){
            if( OrderRow::Classic( $this->orderid ) ){
               $sizename = $labInfo['sizename_border']['matt'][$image['artikkelnr']];
            }
            if( !$sizename ){
               $sizename = $labInfo['sizename']['matt'][$image['artikkelnr']];
            }
         }
         else{
            if( OrderRow::Classic( $this->orderid ) ){
               $sizename = $labInfo['sizename_border'][$this->size][$image['artikkelnr']];
            }
            if( !$sizename ){
               $sizename = $labInfo['sizename'][$this->size][$image['artikkelnr']];
            }
         }
         if( !$sizename ){
            $sizename = $labInfo['sizename'][$image['artikkelnr']];
         }
        
         
         if( $image['artikkelnr'] == 439 &&  ( $uid == 1030157  || $uid == 969748  || $uid == 1370892 ) ){
            $sizename = '2035';
         }
         
         
          if( $image['artikkelnr'] == 2015 &&  ( $uid == 1030157  ) ){
            $sizename = '2035';
         }
         
         
          if( $image['artikkelnr'] == 7237 ){
            $sizename = '10X15';
         }
          
         
         
         
         if( $image['artikkelnr'] == 2 &&  (  $uid == 969748 || $uid == 1370892 ) ){
            $sizename = '20X13';
            $resize = 'FILLIN';
            
         }
         
         
         
         if( $image['artikkelnr'] == 5 &&  ( $uid == 969748 || $uid == 1370892 ) ){
            $sizename = '30X45';
         }
         
          if( $image['artikkelnr'] == 522 &&  ( $uid == 969748 || $uid == 1370892 ) ){
            $sizename = '102C';
         }
         
         

        
         if( $this->size  == '15cm'){
            $xyz_size = '15R10';
         }
         else{
            $xyz_size = '102BC';
         }
        
    	 $imagelist_count++;
    	 $image_list .= $imagelist_count."=". $filename ."\n";
    		
         if( $image['artikkelnr'] == 693 || $image['artikkelnr'] == 7254 || $image['artikkelnr'] == 2609 || $image['artikkelnr'] == 7551 ){
            $antall = $image['antall'] * 2;
    	 }else{
            $antall = $image['antall'];
    	 }
    		
         $artnr = $image['artikkelnr'];
         $unique = $image_info[0];	
         $image_print_count = $image_info[1];
         $image_print_info .="[". $filename ."]
         SizeName=" . $sizename . "
         PrintCnt=" . $antall . "
         BackPrint=FREE
         BackPrintLine1=" . $backprint1 . $filename . "
         BackPrintLine2=" . $backprint2 . "
         Resize=" . $resize ."
         DSC_Chk=FALSE
         Color_Space=".$colorspace."\n\n";
   		}

   	 //util::Debug(  "sizecheck " . $this->size );
   	 //Util::debug( $this->photolab );
         
   	 $malcontainer = Settings::Get( 'production' , 'seperatorfile' );
   	 if( $this->photolab != 'MyDevice' ){
   	    $this->seperatorFile();
   	    $imagelist_count++;
    	    $image_list .= $imagelist_count."=xyz.jpg\n";
   	    $image_print_info .= "[xyz.jpg]
	SizeName=" . $xyz_size . "
	PrintCnt=1
	BackPrint=FREE
	BackPrintLine1=" . $this->orderid . "
	BackPrintLine2=" . $backprint1 . "
	Resize=FITIN
	DSC_Chk=FALSE
	Color_Space=".$colorspace."\n\n";
   		}
      
    	//start devicename
    	
	    $fp = fopen($condition,"w");
            fwrite($fp,
               //print_r(
    		"[OutDevice]\nDeviceName=".$device . 
    		"\n\n[ImageList]\n" .	
    		sprintf("ImageCnt=%s", $imagelist_count) ."\n".	
    		$image_list . "\n" .
    		$image_print_info);
      
               fclose($fp);
		$fe = fopen( $this->destination . "End.txt","w");
               fwrite($fe,"");
               fclose($fe);
      
      
   }
   
   public function papertype(){
      
      $papertype = DB::query( 'SELECT artikkelnr FROM historie_ordrelinje WHERE ordrenr = ? AND artikkelnr in( 11, 12) ', $this->orderid)->fetchSingle();
      
      if(  $papertype == 12){
         $this->photolab = '570_1';
         return 'dp';   
      }
      else if( $papertype == 11){
         $this->photolab = '570_1';
         return 'mp';
      }
      else{
         return 'sp';
      } 
      
   }
 
   public function seperatorFile(){
      
      $res = DB::query( "SELECT navn, adresse1, postnr, sted FROM historie_kunde WHERE ordrenr =?", $this->orderid )->fetchRow();

      //$seperatorfilefolder = "/home/httpd/www.eurofoto.no/webside/grafikk/";
      
      $this->separator_file = glob( "/home/produksjon/xyz/*.jpg" );
      
      
      
         
      
      if(  $uid == 1370892){
        $seperatprfile="/home/produksjon/xyz/xyz_utestemme.jpg";
      }
      
      
      
      
       else{
         $seperatprfile = $this->array_random( $this->separator_file );
      }
      
      
      
       
      
     
      
      
      list( $name, $address, $postnr, $city ) = $res; 
      $xyz = new Imagick( $seperatprfile );
      $text = new ImagickDraw();
      $color = new ImagickPixel( 'white' );
      
      $text->setFillColor( $color );
      $text->setFont('/home/httpd/www.eurofoto.no/webside/font/verdana.ttf');
      $text->setFontSize( 32 );
      $xyz->annotateImage( $text, 30, 50, 0, sprintf( "%d\n\n%s\n%s\n%d %s", $this->orderid, $name,  $address, $postnr, $city ) );
      $xyz->rotateImage(new ImagickPixel('none'), 180); 
      
      $xyz->writeImage( $this->destination . 'xyz.jpg' );
         
   }
   
   
   public function array_random($arr, $num = 1) {
       shuffle($arr);
       
       $r = array();
       for ($i = 0; $i < $num; $i++) {
           $r[] = $arr[$i];
       }
       return $num == 1 ? $r[0] : $r;
   }
   
   
   
   private function subval_sort($a,$subkey) {
      foreach($a as $k=>$v) {
          $b[$k] = strtolower($v[$subkey]);
      }
      asort($b);
      foreach($b as $key=>$val) {
          $c[] = $a[$key];
      }
      return $c;
   }


}

?>