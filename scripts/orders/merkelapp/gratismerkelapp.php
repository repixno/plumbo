<?PHP

   /******************************************
    * Script for handling CD/DVD archiveorders.
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
   import( 'website.order.merkelapporder');
   model( 'order.merkelapp' );
   /*library( 'pdf.fpdf' );*/
   library( 'pdf.fpdi' );

   class MerkelappImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/merkelapp/orders/';
      
      Public function Main(){
         
      $navnelappmixarray =  [6096];
         
    $readymerkelapps =  UserMerkelappOrder::toProduction();
       
       
   /* $readymerkelapps = DB::query( "
            SELECT 
               id
            FROM 
               merkelapp_orders 
            WHERE 
               orderid in (2398132)
         ")->fetchAll( DB::FETCH_ASSOC );
         
        */
         
      
         // 2393654 2393589 
         if( count( $readymerkelapps ) ){
            
            foreach( $readymerkelapps as $merkelapp ){
               
               $merkelapps = new DBMerkelappOrder( $merkelapp['id'] );
               Util::Debug( $merkelapps );
               if( !in_array( $merkelapps->articleid, $navnelappmixarray  )) continue;
               

               $line1 = (string)$merkelapps->line1;
               $line2 = (string)$merkelapps->line2;
               $line3 = (string)$merkelapps->line3;
               $mal = (string)$merkelapps->backgroundfile;
               $text = $line1;
               
               if( !empty($line2 ) ){
                    $text .= "\n" . $line2;
               }
               if( !empty( $line3 ) ){
                    $text = $text . "\n" . $line3;
               }
               
               Util::debug( $merkelapps );
               
               $id = $merkelapps->id;
               $articleid = $merkelapps->articleid;
               $orderid = $merkelapps->orderid;
                $backgroundfile = $merkelapps->backgroundfile;
               $date = date( 'Y-m-d' , strtotime( $merkelapps->date ) );
               $orderdate = DB::query( "SELECT tidspunkt FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
               $orderdate = date( 'Y-m-d' , strtotime( $orderdate ) );
               $hol  = DB::query( "SELECT * FROM historie_ordrelinje WHERE malid = ? AND ordrenr = ? limit 1", $id, $orderid )->fetchAll(DB::FETCH_ASSOC);
               
               foreach( $hol as $line ){
                  
                  Util::Debug($line);
                  
                  
                  $articleid = $line['artikkelnr'];
                 
                   Util::Debug('bakgrunnsfil');
                  Util::Debug($backgroundfile);
               $badcodetext = base_convert( $orderid, 10, 36  ) . " " . $orderid;
                  
                  
              //    $template = $this->template( $backgroundfile);
                     $template = $this->template( $backgroundfile);
                 //  $template2 = $backgroundfile;
              
                  
             
                  
                  $destinationfolder = "/home/produksjon/merkelapp/gratismerkelapp/$orderdate/$orderid/$articleid";
               
                  util::Debug( $destinationfolder );

                  if(!file_exists( $destinationfolder )){
                     mkdir( $destinationfolder , 0755, 1 );
                  }
                  
                  $quantity = $line['antall'];
                  //$quantity = DB::query( "SELECT antall FROM historie_ordrelinje WHERE product_id = ? AND ordrenr = ?", $id, $orderid )->fetchSingle();
                  $quantity = sprintf( '%03d', $quantity );
      
                  $destinationfile = sprintf( "%s/%s-%s.png", $destinationfolder,  $quantity, $id );
                  
                  //exec ("rsync -a monica.eurofoto.no::dataglobal/merkelapp/$date/$id.png $destinationfile");
                  //exec ("rsync -a remus.eurofoto.no::dataglobal/merkelapp/$date/$id.png $destinationfile");
             
            
            $filenumber = 0;     
            foreach( $template as $textmal ){
                
                $black = new ImagickPixel( $textmal['textcolor'] );
                $white = new ImagickPixel( none );
                list( $x1, $y1, $width, $height, $colormode2 ) = explode( '-',  $crop );
                $template = new Imagick();
                
                $this->width = 354;
                $this->height = 165;
                $ppi = 300;
                $border = 11;
                $offset = 110;
                $bleed = 10;
                $template->setResolution(300,300);
                $template->newImage( $this->width + 20 , $this->height + 20, new ImagickPixel('transparent') );
            
                $backgroundsrc = "/home/produksjon/merkelapp/navnelappmix/$mal/" . $filenumber . '.png';
          
                if( file_exists( $backgroundsrc ) ){
                  $background = new Imagick( $backgroundsrc );
                  //$background->scaleImage( $template->getImageWidth() ,  $template->getImageWidth());
                  $template->compositeImage( $background, $background->getImageCompose(),  0,  0 );
               }  
           
                $template->setImageFormat('png');
                $textgravity = Imagick::GRAVITY_CENTER;
                
                if( empty( $text ) ){
                    $text = ' ';   
                }
                
                $draw = new ImagickDraw();
                $draw->setFont( '/var/www/repix/data/fonts/' . "verdanab.ttf" );
              
                $draw->setFontSize( 296 );
                $draw->setGravity( $textgravity );
                $draw->setFillColor( $black );
                $draw->setStrokeColor('#ffffff ');
                $draw->setStrokeWidth(4);
                $canvas = new Imagick();
                $metrics = $canvas->queryFontMetrics( $draw, $text );
                $canvas->newImage( $metrics['textWidth'], $metrics['textHeight'], $white, "png");
                $canvas->annotateImage($draw,0,0,0,$text);
                $templategeo =  $template->getImageGeometry();
               
                $textratio =  $metrics['textWidth'] / $metrics['textHeight'];
                
                $texplaceholderwidth = $templategeo['width'] - 150;
             // Kor stor lengde teksten skal ha
             //$texplaceholderwidth = $templategeo['width'] - 150;
                $textplaceholderheight = ( $templategeo['height'] ) - ( $border * 2 );
                $templateratio =  $texplaceholderwidth  / $textplaceholderheight;
    
                if ( $textratio > $templateratio ){
                   $canvas->scaleImage( $texplaceholderwidth - $bleed , 0);
                }
                else{
                   $canvas->scaleImage( 0,$textplaceholderheight - $bleed );
                }
                
                $topmargin = ( ( $templategeo['height'] - ( $bleed * 2 ) - $canvas->getImageHeight() ) / 2 );
                $canvas->setImageFormat('PNG');
                
                //høgare tall flytter mot høgre 86 = med clipart 140=  uten
                
                
        switch ($backgroundfile) {
  case "0":
    echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
  case "1":
  echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
  case "2":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
     case "3":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
     case "4":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
     case "5":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
    
     case "14":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
     case "15":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
     case "16":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
     case "17":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
      case "18":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
      case "19":
 echo "med clipart";
    $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
    break;
   
  default:
    echo "uten clipart";
   $template->compositeImage( $canvas, $canvas->getImageCompose() ,  86 ,  $topmargin + $bleed  );
}
                  
                  
                
                
                //  $template->compositeImage( $canvas, $canvas->getImageCompose() ,  86 ,  $topmargin + $bleed  );
             //   $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
                $orderfolder =  $this->webspoolFolder . date( 'Y-m-d');
        
                if( !file_exists($destinationfolder . '/autodedit') ){
                    mkdir( $destinationfolder . '/autodedit' , 0755 );
                }
                $autoeditfile = $destinationfolder . '/autodedit/' .  $filenumber . '.png';
                Util::Debug( $autoeditfile );
                $template->writeImage( $autoeditfile );
                $filenumber++;
            }   

               $customer  = DB::query( 'SELECT * from historie_kunde where ordrenr = ?' , $orderid  )->fetchAll( DB::FETCH_ASSOC  );
                     
                     util::Debug( $customer );
                      util::Debug( $portalid );
                  
                     $customer = $customer[0];
                     
                     $portalid = DB::query( "SELECT kampanje_kode FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
   
                   
                     // gratis merkelapp
                     if( $portalid == 'DM-002'){
                        $imagepath = "/home/produksjon/merkelapp/kuttekanter/august.jpg";
                     }
                     
                     // merkelapp
                     else{
                        $imagepath = "/home/produksjon/merkelapp/kuttekanter/ef2018.jpg";
                     }
                     
                     
                     
                     try{
                        $autoeditfile = $destinationfolder . '/autodedit/' .  0 . '.png';
                        $img = new Imagick( $autoeditfile );
                        $sheet = new Imagick( $imagepath );
         
                        $imageheight = $img->GetImageHeight();
                        $imagewidth = $img->GetImageWidth();
                        
                        $bleed = 10;               
                        $x = 4;
                    $y = 6;
                        $y1 = 0;
                        $yposition = $imageheight - $bleed;
         
                        $text = sprintf( "%s\n%s\n%s %s ", $customer['navn'] , $customer['adresse1'] , $customer['postnr'], $customer['sted'] );
                     //     $text = sprintf( "%s\n%s\n%s %s\nOrdrenr:%s ", $customer['navn'] , $customer['adresse1'] , $customer['postnr'], $customer['sted'], $orderid );
                        $draw = new ImagickDraw();
                        /* Font properties */
                        $draw->setFontSize(30);
                        /* Create text */
                        $draw->annotation( 474, 50, $text );
                           $draw->annotation( 1004, 80, "Ordrenummer:" . $orderid );
                        $sheet->drawImage($draw);
                        $sheetspace = 2;
                        $z = 0;
                        $f =0;
                        
                        
                        // her stepper en bare opp 4x6 
                        while( $y1 < $y ){
                           if( $z = $y1){
                              $f++;
                              $autoeditfile = $destinationfolder . '/autodedit/' .  $f . '.png';
                              $img = new Imagick( $autoeditfile );
                              $z = 0;
                           }
                           
                           
                      /*  while( $y1 < $y ){
                           if( $z > 5 ){
                              $f++;
                              $autoeditfile = $destinationfolder . '/autodedit/' .  $f . '.png';
                              $img = new Imagick( $autoeditfile );
                              $z = 0;
                           }
                           
                           */
                           
                           $z++;
                           $x1 = 0;
                           // var på 24, flytta til 14 for å få png til å fra seg til venstre :) :) 
                           $xposition = 14 - $bleed;
                           while( $x1 < $x ){
                              $sheet->compositeImage( $img, $img->getImageCompose(), $xposition, $yposition );
                             //imagewidth 5 er mm er lufta i på arket .denne var 12 før
                              $xposition += ( $imagewidth + 5 );
                              $x1++;
                           }
                           if( $sheetspace == 12 ){
                               //$imageheight 50 er mm luft mellom arka på arket. Denne var 140 
                              $yposition += ( $imageheight + 50 );
                              $sheetspace = 0;
                           }else{
                               //imagewidth 5 er mm er lufta i på arket .denne var 12 før
                              $yposition += ( $imageheight + 5 );
                           }
                          
                           $sheetspace++;
                           $y1++;
                        }
                        
                        
                                 $orderinfo = DB::query( "SELECT * FROM merkelapp_orders WHERE orderid = ? AND articleid = ? ORDER BY orderid DESC" , $orderid, $artnr )->fetchAll( DB::FETCH_ASSOC );
         $orderinfo  = $orderinfo[0];
         
         
                        $inputFile = sprintf( "%s/%s-%s_printfile.jpg", $destinationfolder,  $quantity , $id );
                        $sheet->writeImage(  $inputFile  );
                        
                        //create pdf printfile
                        $outFile = sprintf( "%s/%s-%s_printfile.pdf", $destinationfolder,  $quantity , $id );
                    //    /home/produksjon/merkelapp/maler/gratismerkelappmal/gratis_merkelapp.jpg
                        $cutFilename =  "/home/produksjon/merkelapp/kuttekanter/gratislapp_2020.pdf" ; 
                        $pdf = new FPDI( 'P', 'mm', array( 137, 205 ) );
                        $pdf->AddPage();
                        $pdf->Image( $inputFile , 2.5, 2.5, -300 );
                        
                        
                    
               $badcodetext = base_convert( $orderid, 10, 36  ) . " " . $orderid;
               
               
             //    $pdf->Code39(35, $size + 185,$badcodetext,0.7,9); 0,7 er bredda 9 er høgda på koden
                  $pdf->Code39(35, $size + 185,$badcodetext,1,10);
                  
                  echo $orderinfo['orderid'];
                  util::Debug( $orderid );
                  
                 //
                $pdf->SetFont('Arial','',9);
                        
                        $pdf->setSourceFile( $cutFilename );
                        $tplIdx = $pdf->importPage(1);
                        $pdf->useTemplate($tplIdx, 0, 0, 0);
                        
                        $pdf->Output( $outFile , 'F');
                     }catch( Exception $e ){
                        Util::Debug( $e->getMessage() );
                     }
               
               }
                          
               $merkelapps->processed = date( 'Y-m-d');
               $merkelapps->save();
            }
         }
         else{
            util::Debug('no orders');
            
         }     
      }
      
       
      
     private function template( $backgroundfile ){
      
            
  $textplassering = [
    '0' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#ffffff'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#ffffff'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#ffffff'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#ffffff'] 
],
'1' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
],
'2' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
],
'3' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],
'4' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],
'5' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
],
'6' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#000000']
],
'7' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#000000'] 

],
'8' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#000000']  

],
'9' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#fffef2'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#ffffff'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#69325b' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#636466'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#6a547f'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#6b2998'] 

],
'10' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#fffef2'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#15777a'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#6d6e71' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#414042'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#ffffff'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#106366'] 

],
'11' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#78c26e'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#66cad8'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#ef575b' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#da0203'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#fee63e'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#ffffff'] 

],
 '12' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#78c26e'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#66cad8'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#ef575b' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#fb9e33'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#fee63e'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#ffffff']
],
                      
'13' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#ffffff'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#ffffff'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#ffffff' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#ffffff'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#ffffff'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#ffffff'] 

],
'14' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000']  

],
'15' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],
'16' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],
'17' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],
'18' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],
'19' => [
0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 

],

'20' => [
0 => [ 'x' => 0, 'y' => -5, 'textcolor' => '#000000'],
1 => [ 'x' => 210, 'y' => -5, 'textcolor' => '#000000'],
2 => [ 'x' => 420, 'y' => -5, 'textcolor' => '#000000' ], 
3 => [ 'x' => 0, 'y' => 90,'textcolor' => '#000000'],
4 => [ 'x' => 210, 'y' => 90,'textcolor' => '#000000'],
5 => [ 'x' => 420, 'y' => 90,'textcolor' => '#000000'] 
]


                     
                    
                ];
        
        return $textplassering[$backgroundfile];
      }
   
   
   }
   

   CLI::Execute();

?>