<?PHP

   /******************************************
    * Script for handling CD/DVD archiveorders.
    * runst the converts script and moves
    * orders to correct location
    * 
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

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
         
      $kutoppenarray =  [6073,6074,6075,6076,6077,6078,6079,6080,6081, 6082,6083,6084,6085,6086,6087,6088 ];
         
       $readymerkelapps =  UserMerkelappOrder::toProduction();
         
       /*$readymerkelapps = DB::query( "
            SELECT 
               id
            FROM 
               merkelapp_orders 
            WHERE 
               orderid = 2297561
         ")->fetchAll( DB::FETCH_ASSOC );*/
         
         if( count( $readymerkelapps ) ){
            
            foreach( $readymerkelapps as $merkelapp ){
               
               $merkelapps = new DBMerkelappOrder( $merkelapp['id'] );
               Util::Debug( $merkelapps );
               if( !in_array( $merkelapps->articleid, $kutoppenarray  )) continue;

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
               $date = date( 'Y-m-d' , strtotime( $merkelapps->date ) );
               $orderdate = DB::query( "SELECT tidspunkt FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
               $orderdate = date( 'Y-m-d' , strtotime( $orderdate ) );
               $hol  = DB::query( "SELECT * FROM historie_ordrelinje WHERE malid = ? AND ordrenr = ? limit 1", $id, $orderid )->fetchAll(DB::FETCH_ASSOC);
               
               foreach( $hol as $line ){
                  
                  Util::Debug($line);
                  
                  $articleid = $line['artikkelnr'];
                  $template = $this->template( $line['artikkelnr']);
                  
                  Util::debug( $template  );
                  
                  $destinationfolder = "/home/produksjon/merkelapp/orders/$orderdate/$orderid/$articleid";
               
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
            
                $backgroundsrc = "/home/produksjon/merkelapp/kutoppen/$mal/" . $filenumber . '.png';
          
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
                
                //høgare tall flytter mot høgre
                $template->compositeImage( $canvas, $canvas->getImageCompose() ,  140 ,  $topmargin + $bleed  );
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
                     $customer = $customer[0];
                     
                     $portalid = DB::query( "SELECT kampanje_kode FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
   
                     if( $portalid == 'DM-SV'){
                        $imagepath = "/home/produksjon/merkelapp/maler/malSE.jpg";
                     }
                     elseif( $portalid == 'SK-001' ){
                        $imagepath = "/home/produksjon/merkelapp/maler/malsparelapp2018.jpg";
                     }else{
                        $imagepath = "/home/produksjon/merkelapp/maler/ef2018.jpg";
                     }
                     
                     
                     
                     try{
                        $autoeditfile = $destinationfolder . '/autodedit/' .  0 . '.png';
                        $img = new Imagick( $autoeditfile );
                        $sheet = new Imagick( $imagepath );
         
                        $imageheight = $img->GetImageHeight();
                        $imagewidth = $img->GetImageWidth();
                        
                        $bleed = 10;               
                        $x = 4;
                        $y = 35;
                        $y1 = 0;
                        $yposition = $imageheight - $bleed;
         
                        $text = sprintf( "%s\n%s\n%s %s\nOrdrenr:%s ", $customer['navn'] , $customer['adresse1'] , $customer['postnr'], $customer['sted'], $orderid );
                        $draw = new ImagickDraw();
                        /* Font properties */
                        $draw->setFontSize(30);
                        /* Create text */
                        $draw->annotation( 474, 40, $text );
                        
                        $sheet->drawImage($draw);
                        $sheetspace = 2;
                        $z = 0;
                        $f =0;
                        while( $y1 < $y ){
                           if( $z > 5 ){
                              $f++;
                              $autoeditfile = $destinationfolder . '/autodedit/' .  $f . '.png';
                              $img = new Imagick( $autoeditfile );
                              $z = 0;
                           }
                           $z++;
                           $x1 = 0;
                           $xposition = 24 - $bleed;
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
                        
                        $inputFile = sprintf( "%s/%s-%s_printfile.jpg", $destinationfolder,  $quantity , $id );
                        $sheet->writeImage(  $inputFile  );
                        
                        //create pdf printfile
                        $outFile = sprintf( "%s/%s-%s_printfile.pdf", $destinationfolder,  $quantity , $id );
                        $cutFilename = $this->webspoolFolder . "maler/kuttekant_2018_1.pdf" ; 
                        $pdf = new FPDI( 'P', 'mm', array( 137, 591 ) );
                        $pdf->AddPage();
                        $pdf->Image( $inputFile , 2.5, 2.5, -300 );
                        
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
      
      private function template( $artnr ){
        
        $kobling = [6073 => 'mix',
					6074 => 'mosk',
					6075 => 'klara',
					6076 => 'bernt',
					6077 => 'gaute',
					6078 => 'rosa',
					6079 => 'chickolina',
					6080 => 'fobetron',
               6081 => 'team',
               6082 => 'blomster',
               6083 => 'transport',
               6084 => 'jente',
               6085 => 'monster',
               6086 => 'dyr',
               6087 => 'gutt',
               6088 => 'bw'];
        
        $textplassering = [
    'mix' => [
                               0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                     'mosk' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                    'klara' => [
                                 0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                  'bernt' => [
                                 0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                         
                  'gaute' => [
                               0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                   'rosa' => [
                               0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                    'chickolina' => [
                                 0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => -5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                    'fobetron' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                    
                    'team' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                    
                     'blomster' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                     
                      'transport' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                      
                      'jente' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                      
                      'monster' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                      
                      
                      'dyr' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                      
                      'gutt' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                            5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ],
                      
                   
                      
                      'bw' => [
                                0 => [ 'x' => 40, 'y' => -5, 'textcolor' => '#000000'],
                            1 => [ 'x' => 250, 'y' => -5, 'textcolor' => '#000000'],
                            2 => [ 'x' => 460, 'y' => 5, 'textcolor' => '#000000' ], 
                            3 => [ 'x' => 40, 'y' => 90,'textcolor' => '#000000'],
                            4 => [ 'x' => 250, 'y' => 90,'textcolor' => '#000000'],
                         5 => [ 'x' => 460, 'y' => 90,'textcolor' => '#000000'] 
                    ]
                     
                      
                     
                    
                ];
        
        return $textplassering[$kobling[$artnr]];
      }
   
   
   }
   

   CLI::Execute();

?>