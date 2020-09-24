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
         $kutoppenarray =  [6073,6074,6075,6076,6077,6078,6079,6080,6081,6082,6083,6084,6085,6086,6087 ];
         $readymerkelapps =  UserMerkelappOrder::toProduction();
         
         if( count( $readymerkelapps ) ){
            
            foreach( $readymerkelapps as $merkelapp ){
               
               $merkelapps = new DBMerkelappOrder( $merkelapp['id'] );
               
               if( in_array( $merkelapps->articleid, $kutoppenarray  )) continue;
               
               $id = $merkelapps->id;
               $articleid = $merkelapps->articleid;
               
               
               $orderid = $merkelapps->orderid;
               $date = date( 'Y-m-d' , strtotime( $merkelapps->date ) );
               
               
               $orderdate = DB::query( "SELECT tidspunkt FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
               
             
               $orderdate = date( 'Y-m-d' , strtotime( $orderdate ) );
               
               
               
               $hol  = DB::query( "SELECT * FROM historie_ordrelinje WHERE malid = ?", $id)->fetchAll(DB::FETCH_ASSOC);
               
               
               
               foreach( $hol as $line ){
                                    
                  $articleid = $line['artikkelnr'];
                  
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
                     
                  if( file_exists( "/data/global/merkelapp/$date/$id.png" )){	 
              	    copy ("/data/global/merkelapp/$date/$id.png",  $destinationfile );
		  }
               
					//7261= 140 fargelapper
					//7262= 300 fargelapper
					// 7700 = 140 fargelapper
					
                  if( $articleid == 7261 || $articleid == 7262 || $articleid == 7700){
                     
                     
                     $customer  = DB::query( 'SELECT * from historie_kunde where ordrenr = ?' , $orderid  )->fetchAll( DB::FETCH_ASSOC  );
                     
                     
                     util::Debug( $customer );
                     
                     
                     $customer = $customer[0];
                     
                     $portalid = DB::query( "SELECT kampanje_kode FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
   
                     if( $portalid == 'DM-SV'){
                        $imagepath = "/home/produksjon/merkelapp/maler/malSE.jpg";
                     }
                     elseif( $portalid == 'SK-001' ){
                        $imagepath = "/home/produksjon/merkelapp/maler/malsparelapp2018.jpg";
                     }
							
							elseif( $portalid == 'SL-001' ){
                        $imagepath = "/home/produksjon/merkelapp/maler/malseniorlappen2019.jpg";
                     }
							
							
							else{
                        $imagepath = "/home/produksjon/merkelapp/maler/ef2018.jpg";
                     }
                     
                     
                     
                     try{
                        $img = new Imagick( $destinationfile );
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
                        
                        /*
                        $draw->setFontSize(175);
                        $draw->setFont( '/var/www/repix/other/fonts/barcode/free3of9.ttf' );
                        
                        $draw->annotation( 1030, 95, "12345667" );*/
                        
                        $sheet->drawImage($draw);
                        $sheetspace = 2;
                        while( $y1 < $y ){
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
               
               }
                          
               $merkelapps->processed = date( 'Y-m-d');
               $merkelapps->save();
            }
         }
         else{
            util::Debug('no orders');
         }     
      }
   
   
   }
   

   CLI::Execute();

?>
