<?PHP

   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';



   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   //library( 'pdf.fpdf' );
   library( 'pdf.fpdi' );
    
   
   library('barcodegen.class.BCGFontFile');
   library('barcodegen.class.BCGColor');
   library('barcodegen.class.BCGDrawing');
   
   library('barcodegen.class.BCGcode39');
   
   model('order.felix');
    
   class FelixImportScript extends Script {
    
      private $jsonfolder = '/data/pd/felix/canvas/';
      private $templatefolder = '/home/produksjon/felix/templates/';
      private $clipartfolder = '/home/produksjon/felix/clipart/';
     
     
      //private $imageserver = "http://therese.eurofoto.no/production/index.php";
      //private $securecode = "p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ";   
     
      Public function Main(){
            
            
            Util::Debug("start");

            $felix_orders = DB::query("SELECT * FROM order_felix where id > 56779  AND processed is null AND orderid is not null" )->fetchAll(DB::FETCH_ASSOC );
            //$felix_orders = DB::query("SELECT * FROM order_felix where orderid > 1712651 AND orderid is not null order by orderid" )->fetchAll(DB::FETCH_ASSOC );

            util::debug($felix_orders);
            
            //$felix_orders = DB::query("SELECT * FROM order_felix where orderid = 2014350" )->fetchAll(DB::FETCH_ASSOC );
            
            if( !$felix_orders  ){
               echo "null ordre";
               exit;
            }
            
            foreach( $felix_orders as $felixorder ){
               
               $felixobject = new DBFelix( $felixorder['id'] );
                
               $felix_template = file_get_contents( $this->jsonfolder  . $felixorder['id'] );
                
               //$felix['orderid'] = 1631705;
               $orderid = $felixorder['orderid'] ;
                
               $order = DB::query( "SELECT * FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchAll( DB::FETCH_ASSOC );
                
               $order = $order[0];
                
               $orderfolder =  sprintf( "/home/produksjon/felix/%s/%s/" , date ('Y-m-d' , strtotime( $order['tidspunkt'] ) ), $orderid );
               if( !file_exists( $orderfolder ) ){
                  mkdir( $orderfolder, 0774, true );
               }
                
               chdir( $orderfolder );
                
               $filename = $orderfolder . "/$orderid.jpg";
                
               $felix = json_decode( $felix_template );
               
               
               //Util::Debug($felix);
                
               //exit;
               
               $clipart = $felix->canvasimage;
               $background = $felix->templateimage;
               $text = $felix->textimage;
               $template = $this->templatefolder . str_replace( '%20', ' ', basename( $background->src ) );
                
               $white =  new ImagickPixel('white');
                
               $bgimg = new Imagick( $template );
                
               $imageheight = (int)$bgimg->getImageHeight() + 200;
                
               $img = new Imagick();
               $img->newImage((int)$bgimg->getImageWidth(), $imageheight , new ImagickPixel('white'));
               $img->setImageFormat('jpeg');
               $img->setImageCompression( Imagick::COMPRESSION_JPEG ); 
               $img->setImageCompressionQuality( 95 ); 
               $img->setImageResolution( 300,300 );
               $img->compositeImage($bgimg, $bgimg->getImageCompose(), 0, 0);
               $ratio = $bgimg->getImageHeight() / $background->height;
                
               $txtcolor = new ImagickPixel( "#e01111" );
               
               $printtext = (string)$text->text;
               
               if( $clipart->imagetype  ){
                  
                  if( $clipart->imagetype == 'userimage'){
                     
                     $userimage = $this->fetchImage( $clipart->imageid, $orderfolder);
                     $clipartimg = new Imagick( $userimage );
                     
                     
                     if( $clipart->angle ){
                        $clipartimg->rotateImage(new ImagickPixel(), $clipart->angle ); 
                     }
                     if( $clipart->angle == 90 ||  $clipart->angle == 90 ){
                        
                        $clipartheight = $clipart->width * $clipart->scaleX * $ratio;
                        $clipartwidth = $clipart->height * $clipart->scaleY * $ratio;
                     }else{
                        $clipartwidth = $clipart->width * $clipart->scaleX * $ratio;
                        $clipartheight = $clipart->height * $clipart->scaleY * $ratio;
                     }
                    
                     $clipartimg->scaleImage( $clipartwidth, $clipartheight );
                     
                     
                     $cliparttop = ( $clipart->top * $ratio  )  - (  $clipartheight / 2 );
                     $clipartleft = ( $clipart->left * $ratio ) - ( $clipartwidth / 2 );
                     
                     if( $felixorder['productid'] == 7441 ){
                        $clipartimg->cropImage ( 641 , 408 , 114 - $clipartleft , 595 - $cliparttop );
                        
                        if( $clipartimg->getImageWidth() < 641 ){
                           
                           $x = ( ( 641 - $clipartimg->getImageWidth() ) / 2) + 114;
                        }
                        else{
                           $x = 114;
                        }
                        
                        if( $clipartimg->getImageHeight() < 408 ){
                           
                           $y = ( ( 408 - $clipartimg->getImageHeight() ) / 2) + 595;
                        }
                        else{
                           $y = 595;
                        }
                        
                        $img->compositeImage($clipartimg, $clipartimg->getImageCompose(), $x , $y );
                        
                     }
                     else if( $felixorder['productid'] == 7554 ){
                        $clipartimg->cropImage ( 516 , 254 , 91 - $clipartleft , 396 - $cliparttop );

                        if( $clipartimg->getImageWidth() < 516 ){

                           $x = ( ( 516 - $clipartimg->getImageWidth() ) / 2) + 91;
                        }
                        else{
                           $x = 91;
                        }

                        if( $clipartimg->getImageHeight() < 254 ){

                           $y = ( ( 254 - $clipartimg->getImageHeight() ) / 2) + 396;
                        }
                        else{
                           $y = 396;
                        }

                        $img->compositeImage($clipartimg, $clipartimg->getImageCompose(), $x , $y );
                  
                    }	
                    else{
                        $clipartimg->cropImage ( 641 , 306 , 114 - $clipartleft , 535 - $cliparttop );
                        
                        if( $clipartimg->getImageWidth() < 641 ){
                           $x = ( ( 641 - $clipartimg->getImageWidth() ) / 2) + 114;
                        }
                        else{
                           $x = 114;
                        }
                        
                        if( $clipartimg->getImageHeight() < 306 ){
                           
                           $y = ( ( 306 - $clipartimg->getImageHeight() ) / 2) + 535;
                        }
                        else{
                           $y = 535;
                        }
                        
                        $img->compositeImage($clipartimg, $clipartimg->getImageCompose(), $x, $y );
                     }
                     
                  }
                  else{
                     $clipartfile = basename( $clipart->src ); 
                     $clipartimg = new Imagick( $this->clipartfolder . $clipartfile );
                     $clipartwidth = $clipart->width * $ratio;
                     $clipartheight = $clipart->height * $ratio;
                     $clipartimg->scaleImage($clipartwidth, $clipartheight );
                     $cliparttop = ( $clipart->top * $ratio )  - (  $clipartheight / 2 );
                     $clipartleft = ( $clipart->left * $ratio ) - ( $clipartwidth / 2 );
                     $img->compositeImage($clipartimg, $clipartimg->getImageCompose(), $clipartleft, $cliparttop );
                  }
               }
               
               if( $text && $text->height > 0 ){
                  
                  $draw = new ImagickDraw();
                  $draw->setFont( $this->templatefolder . 'felixscript.ttf' );
                  $draw->setFontSize( 196 );
                  $draw->setGravity( Imagick::GRAVITY_CENTER );
                  $draw->setFillColor( $txtcolor );
                  $draw->setStrokeColor($txtcolor);
                  $draw->setStrokeWidth(8);
                  $draw->setStrokeAntialias(true);
                  $draw->setTextAntialias(true);
                   
                  $txtimg = new Imagick();
                  $metrics = $txtimg->queryFontMetrics( $draw, $printtext );
                  
                  $txtimg->newImage( $metrics['textWidth'] + 36, $metrics['textHeight'] + 36,  new ImagickPixel('none'), "png");
                  
                  $txtimg->annotateImage($draw,+8,+8,0,$printtext);
                  $txtimg->writeImage($orderfolder . "/testrext.png");
                  //$txtwidth = ( $text->width  ) * $ratio;
                  $txtwidth =  200 * $ratio;
                  $txtheight = ( $text->height  ) * $ratio;
                  
                  system("convert testrext.png -alpha Extract -blur 0x8 -shade 120x43 -alpha On -background gray50 -alpha background -auto-level aqua_shade.png");
                  $alpha = new Imagick('aqua_shade.png');
                  system("convert testrext.png aqua_shade.png -compose Hardlight -composite  aqua_result.png");
                  $txtimg->compositeImage( $alpha, imagick::COMPOSITE_HARDLIGHT, 0 , 0 );
                  $txtimg->scaleImage((int)$txtwidth, (int)$txtheight, true);
                   
                  $txttop = ( $text->top  * $ratio ) - ( $txtheight / 2 );
                  $txtleft = ( (int)$bgimg->getImageWidth() / 2 ) - ( $txtimg->getImageWidth() / 2);
                  //$txtimg->shadeImage(0, 120, 2);
                  $img->compositeImage($txtimg, $txtimg->getImageCompose(), $txtleft, $txttop - 4 );
                
               }
               
               $colorFront = new BCGColor(0, 0, 0);
               $colorBack = new BCGColor(255, 255, 255);
               
               $font = new BCGFontFile('/var/www/repix/application/library/barcodegen/font/Arial.ttf', 22);
               
               $code = new BCGcode39(); // Or another class name from the manual
               $code->setScale(3); // Resolution
               $code->setThickness(30); // Thickness
               $code->setForegroundColor($colorFont); // Color of bars
               $code->setBackgroundColor($colorBack); // Color of spaces
               $code->setFont($font); // Font (or 0)
               
               
               $artnr = 1;
               $malid = null;
               $station = 1;
               
               $ordrenr = $this->dec2_36($orderid);
               $artnr = $this->dec2_36($artnr);
               $malid = $this->dec2_36($malid);
               $station = $this->dec2_36($station);
               $paddedordrenr = $station." ".$ordrenr." ".$artnr." ".$malid;
               
               
               $badcodetext = base_convert( $orderid, 10, 36  ) . " " . $orderid;
               
               $code->parse($badcodetext); // Text
               
               $drawing = new BCGDrawing('barcode.png', $colorBack);
               $drawing->setDPI(300);
               $drawing->setBarcode($code);
               $drawing->draw();
               $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
               
               $barcode = new Imagick('barcode.png');
               
               
               $barcodeleft = ( (int)$bgimg->getImageWidth() / 2 ) - ( $barcode->getImageWidth() / 2 );
               
               $img->compositeImage($barcode, $barcode->getImageCompose(), $barcodeleft, (int)$bgimg->getImageHeight() + 12 ); 
               
               if( file_exists('aqua_shade.png') ){
                  unlink('aqua_shade.png');
               }
               if( file_exists( 'testrext.png') ){
                  unlink('testrext.png');
               }
               if( file_exists('aqua_result.png') ){
                  unlink('aqua_result.png');
               }
               
               
                
               //Util::Debug($text);
               $img->writeImage($filename);
               
               $outFile = sprintf( $orderfolder . "%s_printfile.pdf", $orderid );
               
               if( $felixorder['productid'] == 7441 ){
                  $cutFilename = '/home/produksjon/felix/templates/kuttekant125.pdf';
                  $pdf = new FPDI( 'P', 'mm', array( 74 , 162 ) );
               }
               else if( $felixorder['productid'] == 7554 ){
                  $cutFilename = '/home/produksjon/felix/templates/kuttekant05.pdf';
                  $pdf = new FPDI( 'P', 'mm', array( 58 , 104 ) );
               }
               else{
                  $cutFilename = '/home/produksjon/felix/templates/kuttekant1.pdf';
                  $pdf = new FPDI( 'P', 'mm', array( 74 , 127 ) );
               }
               
               $pdf->AddPage();
               $pdf->Image( $filename , 0, 0, -300 );
               
               $pdf->setSourceFile( $cutFilename );
               $tplIdx = $pdf->importPage(1);
               $pdf->useTemplate($tplIdx, 0, 0, 0);
               //$pdf->BarCode("PKG1234", "C128B");
               $pdf->Output( $outFile , 'F');
               
               
               
               $produksjonfolder = '/home/produksjon/felix/produksjon/' . date('Y-m-d');
               
               if( !file_exists($produksjonfolder) ){
                  mkdir($produksjonfolder, 0775, true );
               }
               
               
               copy( $outFile, sprintf( $produksjonfolder . "/%s_printfile.pdf", $orderid )  );
               
               
               $felixobject->processed = date( 'Y-m-d H:i:s' ); 
               $felixobject->save();
               
            
            }

                 
         }
            
            
            
      public function fetchImage( $imageid, $savepath  ){
         
         $orderdate = DB::query( "SELECT tidspunkt FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         
         try{
            $imagepath = DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", $imageid)->fetchSingle();
 
            
            Util::debug($savepath);
            
            if( !file_exists( $savepath ) ){
               mkdir( $savepath , 0755 , true );
            }
            
            $img = sprintf( '%s/org_%s.jpg', $savepath , $imageid  );
            
            $hashcode = DB::query( "SELECT hashcode FROM bildeinfo WHERE bid = ?", $image['bid'] )->fetchSingle();
            $checksum = null;
            $count = 0;
            //Util::Debug("hash" . $hashcode );
            //while( $checksum != $hashcode && $count < 100 ){
               $count ++;
               if( file_exists( $img )  ){
                  $checksum = md5_file($img);
                  if( $checksum ==  $hashcode ){
                     continue;
                  }else{
                     unlink( $img );
                  }
               }
               
               Util::Debug('/data/bildearkiv/' . $imagepath);
               
               $connection = ssh2_connect('10.64.1.134', 22);
               ssh2_auth_password($connection, 'www', 'Kefir4ever!');
               ssh2_scp_recv( $connection, '/data/bildearkiv/' . $imagepath, $img );
               
               //file_put_contents( $img, file_get_contents($url) );
               $checksum = md5_file($img);
               
               if( $count > 1 ){
                  util::Debug( $count . "#BUUUUUUUUUUUUUUUUUUUUUUUUUGS " );
               }
               //Util::Debug( "Checksumm = " . $checksum );
            //}
            
            $pathinfo = pathinfo( $img );

            Util::Debug($pathinfo['extension']);
            
            if( $pathinfo['extension'] == 'png' ){
               
               
               $image['filename'] = str_replace( '.png', '.jpeg', $image['filename'] );
               DB::query( "UPDATE historie_bilde SET filename = ? WHERE id = ?", $image['filename'], $image['id'] );
               
               $img2jpg = sprintf( '%s/%s', $savepath , $image['filename']  );  
               
               $imagick = new Imagick( $img );
               $imagick->setImageFormat('jpeg');
               $imagick->writeImage( $img2jpg );
               
               
               unlink($img);
               
            }


            
         }catch( Exception $e){
            Util::Debug( $e->getMessage() );
         }
         
         return $img;
      }
        
        
        
        
 
        
        Private function Writetext(){
            
            
            
            return true;
        }
        
        
      Private function dec2_36($tall){
          if($tall == 0){
              return "0";
          }
          $conversiontable = $this->alphabet_table(1);
          $ordrenr = $tall;
          $grunn = count($conversiontable);
          $number = $ordrenr;
          while($number > 0){
              $tmp = $number;
              $number = floor($number/$grunn);
              $heltall = $number*$grunn;
              $streng[count($streng)] = $tmp - $heltall;
          }
          $n = count($streng);
          $converted = "";
          for($i=($n-1);$i>=0;$i--){
              $converted .= $conversiontable[$streng[$i]];
          }
          return $converted;
      }
                 
        
      Private function alphabet_table($reverse = 0){
         $ret = array(
             "0" => "0",
             "1" => "1",
             "2" => "2",
             "3" => "3",
             "4" => "4",
             "5" => "5",
             "6" => "6",
             "7" => "7",
             "8" => "8",
             "9" => "9",
             "A" => "10",
             "B" => "11",
             "C" => "12",
             "D" => "13",
             "E" => "14",
             "F" => "15",
             "G" => "16",
             "H" => "17",
             "I" => "18",
             "J" => "19",
             "K" => "20",
             "L" => "21",
             "M" => "22",
             "N" => "23",
             "O" => "24",
             "P" => "25",
             "Q" => "26",
             "R" => "27",
             "S" => "28",
             "T" => "29",
             "U" => "30",
             "V" => "31",
             "W" => "32",
             "X" => "33",
             "Y" => "34",
             "Z" => "35"
         );
         if($reverse){
             $tmp = array();
             $keys = array_keys($ret);
             $n = count($keys);
             for($i=0;$i<$n;$i++){
                 $tmp[$ret[$keys[$i]]]  = $keys[$i];
             }
             $ret = $tmp;
         }
     
         return $ret;
      }
        
   }
   
    CLI::Execute();

?>
