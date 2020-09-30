<?php
/**
 * ***************************************************
 * Script for � flytte fotobok pdf til hot foldera
 * 
 * 
 *****************************************************/
$mediaclipfolder = "/mnt/clipproducer2/produksjon/WorkingFolder/";

 
 
find_files( $mediaclipfolder .  'photobook/', '/pdf$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'greetingcard/', '/pdf$/', 'move_mediaclip');
find_files( $mediaclipfolder .  'gift/', '/pdf$/', 'move_mediaclip');


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
    if(file_exists($fullname . "/exclude.txt")){
    	echo $fullname . "/exclude.txt";
    }
    else if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
      find_files($fullname, $pattern, $callback);
    } else if (is_file($fullname) && preg_match($pattern, $entry)) {
      call_user_func($callback, $fullname);
    }
  }
}

function move_mediaclip($filename) {

	$mcHotfolder = "/home/mediaclip/hotfolder/";
	$arttype = explode("-",basename($filename));
	$length = strlen($arttype[1]);
	$artnr = substr( $arttype[1], 0, $length-3 );
	
	if( $artnr == 7113 || $artnr == 7116 || $artnr == 7115){
	   return false;
	}
	
	//la til 7233, 7105 06.03 2017
	if( strpos( $filename, 'gift') ){  
	  $artarry = array(  7317, 7318, 7319, 7234, 7233,7105);
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
  	
  	
  	$rows = get_sql("select order_id from mediaclip_orders where production_id = $unique;");
  	
  	$order_id = $rows[0]['order_id']; 
  	
	if(!file_exists(dirname($filename)."/" . $unique . "done.txt") && file_exists($filename)){
  	//echo $artnr . "\n";
  	//echo basename($filename) . br();

	$destionation = $mcHotfolder . $artnr . "/" . basename($filename);
	if(!file_exists($mcHotfolder . $artnr)){
		exec("mkdir " . $mcHotfolder . $artnr);
		//echo "folder created!!!" . br() .br() .br();
	}
	
	if($artnr == 7039){
	   $artnr = $arttype[1];
	}

	echo "dirname: " . dirname($filename) . "\n";
	exec("cp ". $filename .  " " . $mcHotfolder . $artnr . "/" . $order_id . "-" . $unique . "-"  . $antall . "." . $ext);
	exec("rm " . dirname($filename) . "/*.jpg");
  	//echo $filename . br();
  	//echo $mcHotfolder . basename($filename) . br() . br();  		
  	
	
  	$output = date("d.m.Y_H:i:s.u");
	$newfile=dirname($filename)."/" . $unique . "done.txt";
	$file = fopen ($newfile, "w");
	fwrite($file, $output);
	fclose ($file); 
	$newfile=dirname($filename)."/" . $order_id . ".txt";
	$file = fopen ($newfile, "w");
	fwrite($file, $output);
	fclose ($file); 
	

  }
}

function get_sql($query){
	$connection = pg_connect("host=sidsel.eurofoto.no dbname=eurofoto user=www")
      or die ("Ups PostGres --> " . pg_last_error($conn)); 
      
	$result=pg_query($query);
	
	$rows = pg_fetch_all($result);

    pg_close($connection);
    
    return $rows;
}

?>

