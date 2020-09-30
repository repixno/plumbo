<?php
/**
 * ***************************************************
 * Script for � flytte fotobok pdf til hot foldera
 * 
 * 
 *****************************************************/
require_once("class.get.files.php");
$merkelappfolder = "/home/produksjon/merkelapp/orders";
find_files( $merkelappfolder, '/.pdf$/', 'move_merkelapp');

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
		if( file_exists($fullname . "/Done_Hotfolder.txt")
			 
		|| file_exists($fullname . "/1Done_Hotfolder.txt")
		|| file_exists($fullname . "/0Done_Hotfolder.txt")
		|| file_exists($fullname . "/2Done_Hotfolder.txt")
		|| file_exists($fullname . "/3Done_Hotfolder.txt")
		|| file_exists($fullname . "/4Done_Hotfolder.txt")
		|| file_exists($fullname . "/5Done_Hotfolder.txt")
		|| file_exists($fullname . "/1Done.txt")
		|| file_exists($fullname . "/2Done.txt")
		|| file_exists($fullname . "/3Done.txt")
		|| file_exists($fullname . "/4Done.txt")
		||  file_exists($fullname . "/5Done.txt")
		|| file_exists($fullname . "/exclude.txt")){
   // 	echo $fullname . "/*Done.txt";
    }
    
   
    else if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
      find_files($fullname, $pattern, $callback);
    } else if (is_file($fullname) && preg_match($pattern, $entry)) {
      call_user_func($callback, $fullname);
    }
  }
}

function move_merkelapp($filename) {

	$mcHotfolder = "/mnt/debian/autoprint/";
  
  //$mcHotfolder = "/home/produksjon/merkelapp/ferdig_merkelapp/";
	$arttype = explode("-",basename($filename));
//	$length = strlen($arttype[1]);
	//$artnr = substr( $arttype[1], 0, $length-3 );
  
 // die;
  
	if( $artnr == 7113 || $artnr == 7116 || $artnr == 7115){
	   return false;
	}
	
	//la til 7233, 7105 06.03 2017
	if( strpos( $filename, 'gift') ){  
	  $artarry = array(  7261, 7262,7700,7752,6073,6074,6075,6076,6077,6078,6079,6082,6083,8084,6085,6086,6087,6088);
	  echo "\n ***************** $artnr ************\n"; 
	  if( !in_array($artnr,  $artarry) ){
	    echo "\n ***************** N  O GOOD  ************\n"; 
	    return false;
	  }
	}
  	
  	$unique = (int)$arttype[0];
  	$antall = (int)$arttype[2];
	$path_info = pathinfo($filename);
	$ext = $path_info['extension']; 
  	

	if(!file_exists(dirname($filename)."/" . $unique . "done.txt") && file_exists($filename)){
  
    
echo "Unik " . $unique  . "\n";
echo "Antall " . $antall  . "\n";
echo "basename1 " . basename($filename)  . "\n";
      
$order_data = explode("_", $filename);
$order_data2 = explode("/", $filename);
$order_data3 = explode("-", $order_data2[8]);
  
  $str2=$order_data2[8];
  
  $str=$str2;  
print_r(explode(" ",$str));
  $ordernumb=  $order_data2[7];
  $merkelappantall= substr($order_data2[8], 0 ,3);
  $prosjektid= substr($order_data2[8], 4 ,7);
  
   echo "Antall " . $merkelappantall  . "\n";
   echo "Prosjektid " . $prosjektid  . "\n";
   echo "basename2 " . $artnr  . "\n";

	$destionation = $mcHotfolder;
	if(!file_exists($mcHotfolder . $artnr)){
		exec("mkdir " . $mcHotfolder . $artnr);
		//echo "folder created!!!" . br() .br() .br();
	}
	
	if($artarry ){
	   $artnr = $arttype[1];
	}

echo "filename1: " . dirname($filename) . "\n";
echo "Filename2: " . $filename2 . "\n";
echo "Destiantion: " . $destionation . "\n";
echo "dirname: " . $order_data2[6] . "\n";
echo "pid: " . $prosjektid . "\n";
echo "antall: " . $merkelappantall . "\n";
echo "artnr: " . $order_data2[7] . "\n";

  
  $filename2=$mcHotfolder . $artnr  . $order_data2[6] . "-" . $order_data2[7] . "-" . $merkelappantall . "-". $prosjektid    . "." . $ext;
  
 // exec("cp ". $filename .  " " . $filename2);
  
	     
			      if( $merkelappantall > 1 ){  
			         for ( $i = 1; $i <= $merkelappantall; $i++ ) {
			            $filename2 = sprintf( "%s/%d_%d-%03d-%03d-%d.pdf",$destionation,$order_data2[6],$i,$prosjektid,$order_data2[7], $merkelappantall,1,$artnr );
                //$newfile =   sprintf( "%s/%s/%d_%d-%03d-%03d-%d.jpg",$destionationDir,$orderid,$i,$unique,$fotohefte_bildenr,1,$artnr );
			            if (!copy($filename, $filename2)) {
       			        echo "failed to copy $filename...\n";
   			         }
			         } 
			      }
			      else{
			         if (!copy($filename, $filename2)) {
       			     echo "failed to copy $filename2...\n";
   			      }
			      } 

			   
	//exec("cp ". $filename .  " " . $mcHotfolder . $artnr . "/" . $order_data2[6] . "-" . $order_data2[7] . "-" . $merkelappantall . "-". $prosjektid    . "." . $ext);
//	exec("rm " . dirname($filename) . "/*.pdf");
  	
  	 		
  	 echo "Filename " . $filename  . "\n";
		$output = date("d.m.Y_H:i:s.u");
		$newfile=dirname($filename)."/" . $unique . "Done_Hotfolder.txt";
		$file = fopen ($newfile, "w");
		fwrite($file, $output);
		fclose ($file); 
	/*$newfile=dirname($filename)."/" . $order_id . "ferdig.txt";
	$file = fopen ($newfile, "w");
	fwrite($file, $output);
	fclose ($file); */
	

  }
}



?>

