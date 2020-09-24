<?PHP
/**
 * ***************************************************
 * Script for å lage c8 mapper 
 *****************************************************/
   chdir( dirname( __FILE__ ) );
   include '../../../bootstrap.php';
require_once("/home/httpd/www.eurofoto.no/bin/class.get.files.php");
	
	//include('/home/adele/barcoderphp/php-barcode.php');
    	include('/var/www/repix/scripts/orders/telenor/barcode.php');  
$mediaclipfolder = "/home/produksjon/Lablink_MinSky/";
find_files( $mediaclipfolder  , '/.jpg$/', 'move_telenorimages'); //leiter etter jpg filer. Finner den det flytter den

function find_files($path, $pattern, $callback) {
  $path = rtrim(str_replace("\\", "/", $path), '/') . '/';
  $matches = Array();
  $entries = Array();
  $dir = dir($path);
  while (false !== ($entry = $dir->read())) {
    $entries[] = $entry;
  }
  $dir->close();
  foreach ($entries as $entry) {
    $fullname = $path . $entry;
    
    if(file_exists($fullname . "/exclude_c8.txt")){
    
    	//echo $fullname . "/exclude.txt";
    }else if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
      find_files($fullname, $pattern, $callback);
    }else if (is_file($fullname) && preg_match($pattern, $entry)) {
      call_user_func($callback, $fullname);
    }
  }
}

function move_telenorimages( $filename ){
	
	if(!file_exists(dirname($filename)."/done_c8.txt")){
  $order_data = explode("_", $filename);
  $order_data2 = explode("/", $filename);
    $order_data3 = explode("-", $filename);
 // Denne er angitt som artnr og !!!$arttype = $order_data2[5];
      
        print_r( $order_data ); 		  	
        //		$length  = strlen( $arttype[1] );      
        $artnr = $order_data2[5];
        $production_mappe = valg_size($artnr);		


$telenororderunique= substr($order_data2[4], 0, 12);
echo "ORDRENR: " . $telenororderunique  . "\n";
$strekkode= substr($order_data2[4], 5, 8);
echo "STREKKODE:" . $strekkode . "\n";
$strekodethumb= "/tmp/$strekkode.png";
$lopenummer= substr($order_data2[4], 0, 4);
echo "LOPENUMMER:" . $lopenummer . "\n";
$filnavn= substr($order_data2[4], 0, 12);
$telenororderuniqueen= substr($order_data2[4], 0, 12);
$antall= substr($order_data2[6], 0, 3);
$stringaordrenr=substr($telenororderuniqueen, 0, 4);
$rtest=substr($order_data2[6], 0, -4);
$telenosplitpart= substr($artnr, 0, 2);
//echo "telenosplitpart: " . $telenosplitpart  . "\n";
echo "telenororderunique: " . $telenororderunique  . "\n";
echo "ARTNUMMER: " . $artnr  . "\n";
$stringaordrenr2=substr($artnr, 0, 2);
$telenororderunique= $telenororderunique . "_" .  $telenosplitpart;       	
$dir =  dirname($filename);
$kort = array("01__kort", "02__kort", "03__kort" , "04__kort" ,  "05__kort" , "06__kort");
$retroboks = array("01__retroboks_24_bilder" ,"02__retroboks_24_bilder", "03__retroboks_24_bilder", "04__retroboks_24_bilder", "05__retroboks_24_bilder", "06__retroboks_24_bilder", "01__10x13_cm_bilder_24_stk", "02__10x13_cm_bilder_24_stk" , "03__10x13_cm_bilder_24_stk" , "04__10x13_cm_bilder_24_stk" , "05__10x13_cm_bilder_24_stk" , "06__10x13_cm_bilder_24_stk" , "07__10x13_cm_bilder_24_stk" , "08__10x13_cm_bilder_24_stk" , "09__10x13_cm_bilder_24_stk" , "10__10x13_cm_bilder_24_stk");
$konvolutt = array("01__yng_retrobilder" , "02__yng_retrobilder" , "03__yng_retrobilder" , "04__yng_retrobilder" , "05__yng_retrobilder" , "06__yng_retrobilder", "07__yng_retrobilder", "08__yng_retrobilder", "09__yng_retrobilder", "10__yng_retrobilder");
$calendar = array( "01__kalender", "02__kalender",  "03__kalender", "04__kalender", "05__kalender", "06__kalender");
$alle = array_merge($kort,$retroboks, $calendar,$konvolutt);


if (!file_exists("/tmp/$strekkode.png")) {
      echo "\nLager Strekkoden\n";
      $im     = imagecreatetruecolor(800, 400);  
      $black  = ImageColorAllocate($im,0x00,0x00,0x00);  
      $white  = ImageColorAllocate($im,0xff,0xff,0xff);  
      imagefilledrectangle($im, 0, 0, 800, 400, $white);  
      $data = Barcode::gd($im, $black, 400, 200, 0, "code128", $strekkode, 6, 250);
      imagepng($im, "/tmp/$strekkode.png");
      echo "\nexit\n";
      imagedestroy($im);
      } else {
      echo "Strekkode eksisterer\n";
      
      }

$date = date( 'Y-m-d' , filemtime( $filename ) );
echo "Dato Ordre: " .  $date  . "\n";
//$mcHotfolder = "/home/produksjon/Ut";
$mcHotfolder = "/home/produksjon/Telenor-C8/" .$date ."/"  ;
//echo "mcHotfolder path: " .  $mcHotfolder  . "\n";
$ext = array("jpeg","jpg","tif","tiff");
 //   echo "production_mappe " .  $production_mappe  . "\n"; 
    
 
		if(!file_exists($mcHotfolder )){
			mkdir($mcHotfolder );
		}
	  	
	  	$destionationDir = $mcHotfolder  . "/" .$production_mappe;
		$destionation = $destionationDir . "/" . basename($filename);
		if(!file_exists($destionationDir)){
			mkdir($destionationDir);
		}
		
		if(!file_exists($destionationDir . "/" . $telenororderunique)){
			mkdir($destionationDir . "/" . $telenororderunique );
		}
		
		 $files = new getfiles(dirname($filename),$ext);
	    $list = $files->getlist();
	    if(is_array($list)) sort($list);
	    sort($list, SORT_REGULAR );
	   
	    $antall_bilder = count( $list );
	    $fotohefte_bildenr=0;
	    $imagenr = 0;
	    
		 foreach ($list as $image){
	      $imagenr++;
	    	$file = dirname($filename) .  "/" . $image;
	    	$file_name = basename($image, ".jpg"); 
	    	
	    	$basename = preg_replace("/[^0-4]/","", $file_name);
	    	
	    		if( $file_name == 'frontpage' ){
	    	   $basename = 0;
	    	}
	    	else if ( $file_name == 'lastpage' ){
	    	   $basename = 30;
	    	}

      $newfile = sprintf("%s/%s/%s-%s-%s.jpg",$destionationDir,$telenororderunique,$stringaordrenr,$rtests,$antall);
      $kort = array("01__kort", "02__kort", "03__kort" , "04__kort" ,  "05__kort" , "06__kort");
$retroboks = array("01__retroboks_24_bilder" ,"02__retroboks_24_bilder", "03__retroboks_24_bilder", "04__retroboks_24_bilder", "05__retroboks_24_bilder", "06__retroboks_24_bilder", "01__10x13_cm_bilder_24_stk", "02__10x13_cm_bilder_24_stk" , "03__10x13_cm_bilder_24_stk" , "04__10x13_cm_bilder_24_stk" , "05__10x13_cm_bilder_24_stk" , "06__10x13_cm_bilder_24_stk" , "07__10x13_cm_bilder_24_stk" , "08__10x13_cm_bilder_24_stk" , "09__10x13_cm_bilder_24_stk" , "10__10x13_cm_bilder_24_stk");
$konvolutt = array("01__yng_retrobilder" , "02__yng_retrobilder" , "03__yng_retrobilder" , "04__yng_retrobilder" , "05__yng_retrobilder" , "06__yng_retrobilder", "07__yng_retrobilder", "08__yng_retrobilder", "09__yng_retrobilder", "10__yng_retrobilder");
      $calendar = array( "01__kalender", "02__kalender",  "03__kalender", "04__kalender", "05__kalender", "06__kalender");
      $alle = array_merge($kort,$retroboks, $calendar,$konvolutt);
      $fotohefte = array( 7113, 7116, 7115 );

      
      if( in_array( $order_data2[5], $konvolutt ) ){
			  $calendar_bildenr++;
		      if( $antall > 0 ){  
		         for ( $i = 1; $i <= $antall; $i++ ) {
								 $newfile = sprintf( "%s/%s/%s_%04d-%03d-%03d.jpg",$destionationDir,$telenororderunique,$i,$stringaordrenr,$calendar_bildenr,1,$artnr );
                 
               $seperatorfilestandard="/home/produksjon/xyz/telenor/fremkalle10_cm.jpg";
   
            // Lager bildefil som strekoden og anna info skal inn i  
            $xyz = new Imagick( $seperatorfilestandard );
            $color = new ImagickPixel( 'black' );
            $text2 = new ImagickDraw();
            $color = new ImagickPixel( 'black' );
            $text2->setFillColor( $color );
            $text2->setFont('/home/httpd/www.eurofoto.no/webside/font/verdana.ttf');
            $text2->setFontSize( 60 );
            $xyz->annotateImage( $text2, 200, 500, 0, sprintf( "%s\n\n\n%s\n\n%s",  " ORDRENR:  $lopenummer", "LØPENUMMER:  $artnr", "ORDREID: $strekkode"  ) );
            $xyz->rotateImage(new ImagickPixel('none'), 180); 
            $xyz->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );
           // Leser 
            $image = new Imagick();
            $image->readImage($destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg');
            $telenorstrekode = new Imagick();
            $telenorstrekode->readImage($strekodethumb);
            // The start coordinates where the file should be printed
        $x = 265;
            $y = 150;
            // Draw watermark on the image file with the given coordinates
                   // Draw watermark on the image file with the given coordinates
            $image->compositeImage($telenorstrekode, Imagick::COMPOSITE_OVER, $x, $y);
            
            
            // Save image
            //$image->writeImage("image_watermark." . $image->getImageFormat());
             $image->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );       
                       
		            if (!copy($file, $newfile)) {
    			        echo "failed to copy $file...\n";
			         }
		         } 
		      }
					
		      else{
		         if (!copy($file, $newfile)) {
    			     echo "failed to copy $file...\n";
			      }
		      } 
			}
      
         // Her parser den ordrane som skal pakkast i pappboks
    
      if( in_array( $order_data2[5], $retroboks ) ){
        
         

			  $calendar_bildenr++;
		      if( $antall > 0 ){  
		         for ( $i = 1; $i <= $antall; $i++ ) {
								 $newfile = sprintf( "%s/%s/%s_%04d-%03d-%03d.jpg",$destionationDir,$telenororderunique,$i,$stringaordrenr,$calendar_bildenr,1,$artnr );
               $seperatorfile20="/home/produksjon/xyz/telenor/fremkalle10_cm.jpg";
      
            // Lager bildefil som strekoden og anna info skal inn i  
            $xyz = new Imagick( $seperatorfile20 );
            $color = new ImagickPixel( 'black' );
            
            $text2 = new ImagickDraw();
            $color = new ImagickPixel( 'black' );
            $text2->setFillColor( $color );
            $text2->setFont('/home/httpd/www.eurofoto.no/webside/font/verdana.ttf');
            $text2->setFontSize( 60 );
            $xyz->annotateImage( $text2, 200, 500, 0, sprintf( "%s\n\n\n%s\n\n%s",  " ORDRENR:  $lopenummer", "LØPENUMMER:  $artnr", "ORDREID: $strekkode"  ) );
        
            $xyz->rotateImage(new ImagickPixel('none'), 180); 
            $xyz->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );
           // Leser 
            $image = new Imagick();
            $image->readImage($destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg');
            $telenorstrekode = new Imagick();
            $telenorstrekode->readImage($strekodethumb);
            // The start coordinates where the file should be printed
            $x = 200;
            $y = 200;
            // Draw watermark on the image file with the given coordinates
            $image->compositeImage($telenorstrekode, Imagick::COMPOSITE_OVER, $x, $y);
            
            // Save image
            //$image->writeImage("image_watermark." . $image->getImageFormat());
             $image->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );       
                       
		            if (!copy($file, $newfile)) {
    			        echo "failed to copy $file...\n";
			         }
		         } 
		      }
					
		      else{
		         if (!copy($file, $newfile)) {
    			     echo "failed to copy $file...\n";
			      }
		      } 
			}
      
      
      // Her parser den ordrane som er kalender 
      else if( in_array( $order_data2[5], $calendar ) ){
      
			  $calendar_bildenr++;
		      if( $antall > 0 ){  
		         for ( $i = 1; $i <= $antall; $i++ ) {
								 $newfile = sprintf( "%s/%s/%s_%04d-%03d-%03d.jpg",$destionationDir,$telenororderunique,$i,$stringaordrenr,$calendar_bildenr,1,$artnr );
               $seperatorfile20="/home/produksjon/xyz/telenor/fremkalle20_cm.jpg";
    
    // Om det er kalender lager den denne xyz fila
            
            // Lager bildefil som strekoden og anna info skal inn i  
            $xyz = new Imagick( $seperatorfile20 );
            $color = new ImagickPixel( 'black' );
            $text2 = new ImagickDraw();
            $color = new ImagickPixel( 'black' );
            $text2->setFillColor( $color );
            $text2->setFont('/home/httpd/www.eurofoto.no/webside/font/verdana.ttf');
            $text2->setFontSize( 100 );
            $xyz->annotateImage( $text2, 800, 1200, 0, sprintf( "%s\n\n\n%s\n\n%s",  " ORDRENR:  $lopenummer", "LØPENUMMER:  $artnr", "ORDREID: $strekkode"  ) );
            $xyz->rotateImage(new ImagickPixel('none'), 180); 
            $xyz->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );
           // Leser 
            $image = new Imagick();
            $image->readImage($destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg');
            $telenorstrekode = new Imagick();
            $telenorstrekode->readImage($strekodethumb);
            // The start coordinates where the file should be printed
            $x = 600;
            $y = 600;
            // Draw watermark on the image file with the given coordinates
            $image->compositeImage($telenorstrekode, Imagick::COMPOSITE_OVER, $x, $y);
            
            // Save image
            //$image->writeImage("image_watermark." . $image->getImageFormat());
             $image->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );       
               
            
                       
		            if (!copy($file, $newfile)) {
    			        echo "failed to copy $file...\n";
			         }
		         } 
		      }
					
		      else{
		         if (!copy($file, $newfile)) {
    			     echo "failed to copy $file...\n";
			      }
		      } 
			}
      
      
      // Her parser den gjennnom om det er kort ordre
          else if( in_array( $order_data2[5], $kort ) ){
            
            
				$destionationDir2=  $destionationDir . "/" . $telenororderunique;
			
			$destionationDir3=	$mcHotfolder . $artnr . "/" . $telenororderunique;

			  
			       if( $basename & 1){
			      $fotohefte_bildenr++;
			      if( $antall > 1 ){  
			     
			      $newfile = sprintf( "%s/%s/%s_%03d-%03d-%d.jpg",$destionationDir,$telenororderunique,$stringaordrenr,$fotohefte_bildenr,1,$antall );
          
					  $seperatprfile="/home/produksjon/xyz/telenor/fremkalle15_cm.jpg";
            $xyz = new Imagick( $seperatprfile );
            $text2 = new ImagickDraw();
            $color = new ImagickPixel( 'black' );
            $text2->setFillColor( $color );
            $text2->setFont('/home/httpd/www.eurofoto.no/webside/font/verdana.ttf');
            $text2->setFontSize( 60 );
            
            $text3 = new ImagickDraw();
            $color2 = new ImagickPixel( 'red' );
            $text3->setFillColor( $color2 );
            $text3->setFont('/home/httpd/www.eurofoto.no/webside/font/verdana.ttf');
            $text3->setFontSize( 80 );
            $xyz->annotateImage( $text2, 380, 600, 0, sprintf( "%s\n\n\n%s\n\n%s",  " ORDRENR:  $lopenummer", "LØPENUMMER:  $artnr", "ORDREID: $strekkode"  ) );
             $xyz->annotateImage( $text3, 380, 775, 0, sprintf( "%s",  " OBS! HUSK KONVOLUTTER"  ) );
            $xyz->rotateImage(new ImagickPixel('none'), 180); 
            $xyz->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );
           // Leser 
            $image = new Imagick();
            $image->readImage($destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg');
            $telenorstrekode = new Imagick();
            $telenorstrekode->readImage($strekodethumb);
            // The start coordinates where the file should be printed
            $x = 650;
            $y = 520;
            // Draw watermark on the image file with the given coordinates
            $image->compositeImage($telenorstrekode, Imagick::COMPOSITE_OVER, $x, $y);
            
            // Save image
            //$image->writeImage("image_watermark." . $image->getImageFormat());
             $image->writeImage( $destionationDir . "/" . $telenororderunique  . "/"    . '0.jpg' );


			            if (!copy($file, $newfile)) {
       			        echo "failed to copy $file...\n";
   			         
			         }
						

						
			      }
			     
			   }
			   
			}
      
      // Om den ikkje finner match på nokre av koblingane bare lag fil
			else{
   			if (!copy($file, $newfile)) {
       			echo "failed to copy $file...\n";
   			}
   			
			}
			
	    }
	    
	    echo "\n\n****************  " . $production_mappe . " ****************\n\n";
	   	if(!strpos($production_mappe,"lab")){
		  	$autoedit = used_images($unique);
		 
	    }
	    //create and moves a triggerfil
	  	$output = $unique;
     // Her gjer vi ein sjekk at viss artikkelnr ikkje er match med kalender arrayet så blir der generert fil 
    if(!in_array($order_data2[5], $alle)){
        
        $donefile= dirname($filename)."/exclude_c8.txt";
		$file = fopen ($donefile, "w");
		fwrite($file, $output);
		fclose ($file);
	
        
      }
		else {
      
      $donefile= dirname($filename)."/done_c8.txt";
		$file = fopen ($donefile, "w");
		fwrite($file, $output);
		fclose ($file);
	   $newfile = sprintf("%s/%s/done_c8.txt",$destionationDir,$telenororderunique);
    
      
    }
			
		
		if (!copy($donefile, $newfile)) {
    		echo "failed to copy $file...\n";
		}
		
		$endfile = sprintf( "%s/%s/End.txt", $destionationDir, $telenororderunique );
		
		if( file_exists( $endfile )){
		   echo "REMOVING END FILE!!!!!!!!!!!!!!!!\n";
		   exec( "rm $endfile");
		}
		
		
	 }
	
}

    function valg_size($artnr){
				
  if ($artnr == $kort || $artnr == 955 || $artnr == 7244) {
          $production_mappe = "Kort";
          
          }
    
    
    
    $production_mappe = $artnr;  
    return $production_mappe;
    }

	 
	 
	 
	 
	 
	 
function used_images($telenororderunique){
if($telenororderunique > 0){

}
	return  $imageIds;	
}
?>