<?PHP
/**
 * ***************************************************
 * Script for å lage c8 mapper 
 *****************************************************/
   chdir( dirname( __FILE__ ) );
   include '../../../bootstrap.php';
require_once("/var/www/repix/scripts/orders/telenor/class.get.files.php");


find_files('/home/produksjon/Telenor-C8/', '/done_c8.txt$/', 'mediaclip_createC8');

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


function mediaclip_createC8($filename){
	if(!file_exists(dirname($filename)."/End.txt")){
		
		$device = 'MyDevice';
		$folder =  dirname( $filename ) .	"/" ;
		$condition	= $folder."Condition.txt";
		$endtxt	= $folder."End.txt";
    	$colorspace = "sRGB";
    	$resize = 'FILLIN';
    //	$valg_size = art_array();
    	
    	
		$ext = array("jpeg","jpg","tif","tiff");	
		$files = new getfiles( $folder, $ext, false );
		$list = $files->getlist();
		
		//sortera bilda etter filnavn
		if(is_array($list)) rsort($list);
		
		$antall_bilder = count($list);
		$imagelist_count  = 0;
		$imagecount = $antall_bilder;
		foreach ($list as $image){
                    echo "nytt bildenavn: " . $image . "\n";	
                    $pathinfo = pathinfo($image);
                    $imagelist_count++;
                    $image_list .= $imagelist_count."=".$image."\n";
                    $image_info = explode("-", $pathinfo['filename']);
                    $image_info = explode("-", $pathinfo['filename']);
                    $antall = ltrim($image_info[1], "0");
                    $order_data = explode("_", $filename);
                    $order_data = explode("_", $filename);
                    $order_data5 = explode("/", $filename);
                    $order_data6 = explode("-", $filename);
                    
                    $order_data_path = explode("/", $filename);
                    $image_info = explode("-", $pathinfo['filename']);
                    $image_info = explode("-", $pathinfo['filename']);
                    echo "antall print c8: " . $image_info[2] . "\n";
                    //	$antall = ltrim($image_info[1], "0");
                    $order_data = explode("_", $filename);
                    $order_data9 = explode("-", $image);
                    $order_data5 = explode("/", $filename);
                    $order_data6 = explode("-", $filename);
                    $artnr2 = $order_data2[6];
                    $unique = $prosjektidfotono;
                    
                    $fotofolder_unique = explode('_', $unique );
    		echo "artnr2: " . $artnr2 . "\n";	
    		if( $fotofolder_unique[1] > 0 ){
    		   $unique = $fotofolder_unique[1];
    		}
    		
    		if( is_numeric( $unique ) ){
       		//$query = get_sql( sprintf( "SELECT * FROM mediaclip_orders WHERE production_id = %d", $unique ) );
       	   $ordrenr = $telenororderuniqueen;
    		}
            $image_print_count = $image_info[1];
            $sizeName = $valg_size[$artnr];
            $c8order_antall = explode("_", $image);
        
    		 
                if(  $order_data5[5] == '01__kalender' || $order_data5[5] == '02__kalender'  || $order_data5[5] == '03__kalender' || $order_data5[5] == '04__kalender' ||  $order_data5[5] == '05__kalender' || $order_data5[5] == '06__kalender' || $order_data5[5] == '07__kalender'){
                $sizeName = '2035';
                }
                
                if(  $order_data5[5] == '01__yng_retrobilder' || $order_data5[5] == '02__yng_retrobilder' || $order_data5[5] == '03__yng_retrobilder'  || $order_data5[5] == '04__yng_retrobilder' || $order_data5[5] == '05__yng_retrobilder' || $order_data5[5] == '06__yng_retrobilder' || $order_data5[5] == '07__yng_retrobilder' || $order_data5[5] == '08__yng_retrobilder' || $order_data5[5] == '09__yng_retrobilder' || $order_data5[5] == '10__yng_retrobilder' || $order_data5[5] == '11__yng_retrobilder' || $order_data5[5] == '12__yng_retrobilder'){
                $sizeName = '10X13';
                }
                
                if(  $order_data5[5] == '01__retroboks_24_bilder'  || $order_data5[5] == '02__retroboks_24_bilder' || $order_data5[5] == '03__retroboks_24_bilder' || $order_data5[5] == '04__retroboks_24_bilder' || $order_data5[5] == '05__retroboks_24_bilder' || $order_data5[5] == '06__retroboks_24_bilder' || $order_data5[5] == '07__retroboks_24_bilder' || $order_data5[5] == '08__retroboks_24_bilder' || $order_data5[5] == '09__retroboks_24_bilder' || $order_data5[5] == '10__retroboks_24_bilder'){
                $sizeName = '10X13';
                }
								
								  if(  $order_data5[5] == '01__10x13_cm_bilder_24_stk'  || $order_data5[5] == '02__10x13_cm_bilder_24_stk' || $order_data5[5] == '03__10x13_cm_bilder_24_stk' || $order_data5[5] == '04__10x13_cm_bilder_24_stk' || $order_data5[5] == '05__10x13_cm_bilder_24_stk' || $order_data5[5] == '06__10x13_cm_bilder_24_stk'  || $order_data5[5] == '07__10x13_cm_bilder_24_stk' || $order_data5[5] == '08__10x13_cm_bilder_24_stk' || $order_data5[5] == '08__10x13_cm_bilder_24_stk'  || $order_data5[5] == '09__10x13_cm_bilder_24_stk' || $order_data5[5] == '10__10x13_cm_bilder_24_stk'    ){
                $sizeName = '10X13';
                }
								
								
               
                if(  $order_data5[5] == '01__kort' ||  $order_data5[5] == '02__kort' ||  $order_data5[5] == '03__kort'  ||  $order_data5[5] == '04__kort' ||  $order_data5[5] == '05__kort' ||  $order_data5[5] == '06__kort'){
                $sizeName = '15X15';
                }
                
                
                  if( $image == '0.jpg' ){
    		   $image_info[2] = '1';
    		}
            
            
            		
            
    		
			$image_print_info .="[".$image."]
	SizeName=" .$sizeName. "
  PrintCnt=" .   $image_info[2] . "
	BackPrint=FREE
	BackPrintLine1=" ."Onr:" . $order_data_path[6]  .  "
	BackPrintLine2=www.fremkalle.no" .  " " .$image_print_count . "av" .	$antall_bilder . "
	Resize=" . $resize ."
	DSC_Chk=FALSE
	Color_Space=".$colorspace."\n\n";
			$imagecount--;
   		}
	
    	//start devicename
    	
		$fp = fopen($condition,"w");
    	fwrite($fp,
    		"[OutDevice]\nDeviceName=".$device . 
    		"\n\n[ImageList]\n" .	
    		sprintf("ImageCnt=%s", count($list)) ."\n".	
    		$image_list . "\n" .
    		$image_print_info);
   		fclose($fp);
   		
   		
		$fe = fopen($endtxt,"w");
    	fwrite($fe,"");
     	fclose($fe);
	}
	
}

    function valg_size($artnr){
				

    
    $production_mappe = $artnr;  
    return $production_mappe;
    }

function used_images($telenororderunique){
if($telenororderunique > 0){

}
	return  $imageIds;	
}
?>