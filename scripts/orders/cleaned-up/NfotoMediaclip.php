<?php
   chdir( dirname( __FILE__ ) );
    include '../../bootstrap.php';
    
        config( 'website.config' );
        config( 'website.countries' );
        config( 'production.settings');
        import( 'system.cli' );
    //    import( 'website.projectordernfoto' );
     //   import( 'website.nfotom' );
        library( 'xml.xmlarray' );
    
require_once("/home/httpd/www.eurofoto.no/bin/class.get.files.php");

$mediaclipfolder = "/home/produksjon/Nfoto/Ordrer/Mediaclip/";

find_files( $mediaclipfolder  ,'/$/', 'mediaclip_create_NfotoOrder');

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
    }else if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
      find_files($fullname, $pattern, $callback);
    }else if (is_file($fullname) && preg_match($pattern, $entry)) {
      call_user_func($callback, $fullname);
    }
  }
}


      
function mediaclip_create_NfotoOrder($filename){
	if(!file_exists(dirname($filename)."/Done.txt")){
		
		$device = 'MyDevice';
		$folder =  dirname( $filename ) .	"/" ;
		
		$endtxt	= $folder."Done.txt";
		$files = new getfiles( $folder, $ext, false );
		$list = $files->getlist();
		
		        
     $relink = $filename;
$arr = array_filter(explode('/',$relink));
$out = array('/'.implode('/',$arr).'/');
while((array_pop($arr) and !empty($arr))){
    $out[] = '/'.implode('/',$arr).'/';
};

echo "Ordrepath" .($out [1]) ."\n";  //  /home/produksjon/Nfoto/Ordrer/Mediaclip/ Nfoto Ordrenummer



$str = $filename;
$folder_ordrenr = 4;  
$storleik_c8 = 5;
$storleik_c82 = 6;
$storleik_c83 = 7;
$storleik_c84 = 8;

$arr = explode('/',$str);


		$fe = fopen($endtxt,"w");
    	fwrite($fe,"");
        
     	fclose($fe);  
          echo "storleik folder: " . $arr[$storleik_c8]. "\n";
          echo "Mappenavnet: " . $arr[$storleik_c82]. "\n";
          echo "filnavnet med filendig: " . $arr[$storleik_c83]. "\n";
            
            
         $mappestring = $arr[$storleik_c82];
         echo "Mappestring start : " . $mappestring . "\n";
        
         		$image_info = explode("-", $mappestring);
            
            $projectiden =$image_info[0];
            $articlenumber =$image_info[1];
            $antalls = (int)$image_info[2];
            $usersid = (int)$image_info[3];
            
        echo "projectid : " . $projectiden . "\n";
        echo "articlenumber : " . $articlenumber . "\n";
        echo "antall : " . $antalls . "\n";
        echo "Brukers id : " . $usersid . "\n";
   
   DB::query( "UPDATE mediaclip_projects SET nfoto_status=1, nfoto=now() WHERE id = ?" , $projectiden  );
	}
	
}



?>