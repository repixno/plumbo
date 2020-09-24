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

   class MerkelappImportScript extends Script {
      
      public $webspoolFolder = '/home/produksjon/webspool/';
      public $stabbfolder = '/home/produksjon/stabburet/';
      
      public $date = '2012-08-23';
      
      
      Public function Main(){
         
         
        foreach ( glob( $this->webspoolFolder . $this->date . '/*' ) as $ordre ){
            
            if( !file_exists(  $this->stabbfolder  . $this->date )){
               
               mkdir( $this->stabbfolder  . $this->date );
               
            }
            
            if( is_dir( $ordre . '/7196' ) ){
               
               util::Debug( glob( $ordre . '/7196/*jpg' ));
               
               foreach ( glob( $ordre . '/7196/*jpg' ) as $image  ){
                  
                  //439 == liten
                  //516 == stor
                  
                  $imagesize =  getimagesize( $image );
                  
                  if( !file_exists(  $this->stabbfolder  . $this->date . "/" . $imagesize[0] )){
                        mkdir( $this->stabbfolder  . $this->date . "/" . $imagesize[0] );
                  }
                  
                  copy( $image , $this->stabbfolder  . $this->date . "/" . $imagesize[0] . "/" . basename( $image ) );
                  
                  util::Debug( $imagesize[0] );
               }
               
            }
            else{
               
               //util::Debug( "ikkje stabburet ordre ");
               
            }

         }
         
         
         $this->CompositeSmall();
         $this->CompositeLarge(); 
            
      }
      
      
      public function CompositeSmall(){
        
        $small = 439;
        
        $sheet = new Imagick();
        $sheet->newImage( 7677, 1028 , new ImagickPixel('white'));
        $sheet->setResolution(150,150);
        $sheet->setImageFormat('jpg');
        $sheet->setImageCompressionQuality( 95 );
        //$sheet->newImage( 7677, 1028 , new ImagickPixel('white'));
        
        
        
        $i=0;
        $x = 50;
        $y = 50;
        $xcounter = 0;
        $ycounter = 0;
        $sheetcounter = 0;
        
        $sheetcount  = 1;
        $count = count( glob( $this->stabbfolder . $this->date . "/439/*0.jpg") );
       
        foreach ( glob( $this->stabbfolder . $this->date ."/439/*0.jpg") as $imagepath ){
           
           $i++;
           $xcounter++;
           $img2 = new Imagick( $imagepath );
           $sheet->compositeImage( $img2, $img2->getImageCompose(), $x, $y );
           $x  = $x + 50 + 439;
           
           if( $xcounter == 15 ){
               $xcounter = 0;
               $x = 50;
               $y = 539;
           }
           
           util::Debug( $i . ' == ' . $count); 
           
           if( $sheetcounter == 30 ||  $i == $count ){
              $sheet->writeImage(  $this->stabbfolder . $this->date . "/439/sheeet_$sheetcount.jpg"  );
              $sheet->newImage( 2677, 1028 , new ImagickPixel('white'));
              $x = 50;
              $y = 50;
              $sheetcounter = 0;
              $sheetcount++;
              $xcounter = 0;
           }

           
           $sheetcounter++;
           
        }
      }
      
     public function CompositeLarge(){
        
        $size = 516;
        
        $sheetheight = 1182;
        $sheetwidth = 7677;
        
        $margin = 50;
        $sheet = new Imagick();
        $sheet->newImage( $sheetwidth, $sheetheight , new ImagickPixel('white'));
        $sheet->setResolution(150,150);
        $sheet->setImageFormat('jpg');
        $sheet->setImageCompressionQuality( 95 );
        //$sheet->newImage( 7677, 1028 , new ImagickPixel('white'));

        $i=0;
        $x = $margin;
        $y = $margin;
        $xcounter = 0;
        $ycounter = 0;
        $sheetcounter = 0;
        $sheetcount  = 1;
        
        $count = count( glob( $this->stabbfolder . $this->date . "/$size/*0.jpg") );
       
        foreach ( glob( $this->stabbfolder . $this->date ."/$size/*0.jpg") as $imagepath ){
           
           $i++;
           $xcounter++;
           $img2 = new Imagick( $imagepath );
           $sheet->compositeImage( $img2, $img2->getImageCompose(), $x, $y );
           $x  = $x + $margin + $size;
           
           if( $xcounter == 13 ){
               $xcounter = 0;
               $x = 50;
               $y = $y + $size + $margin;
           }
           
           util::Debug( $i . ' == ' . $count); 
           
           if( $sheetcounter == 26 ||  $i == $count ){
              $sheet->writeImage(  $this->stabbfolder . $this->date . "/$size/sheeet_$sheetcount.jpg"  );
              $sheet->newImage( $sheetwidth, $sheetheight , new ImagickPixel('white'));
              $x = $margin;
              $y = $margin;
              $sheetcounter = 0;
              $sheetcount++;
              $xcounter = 0;
           }

           
           $sheetcounter++;
           
        }
      }
   
   
   }
   

   CLI::Execute();

?>