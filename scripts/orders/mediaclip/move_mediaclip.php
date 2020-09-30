<?PHP

find_files('/mnt/clipproducer2/mediaclip/prepare', '/txt$/', 'move_toQenhancer');
find_files('/mnt/clipproducer2/mediaclip/prepare_fotobok', '/txt$/', 'move_toQenhancer2');

#find_files('/mnt/mediaclip/ready', '/xml$/', 'move_toProduction');

foreach (glob("/mnt/clipproducer2/mediaclip/ready/*/*.xml") as $filename) {
    move_toProduction($filename);
}



function move_toQenhancer($filename) {

       // $enhance_folder = "/mnt/mediaclip/enhance/";
		 $enhance_folder = "/mnt/clipproducer2/mediaclip/enhance/";

        $path_parts = pathinfo($filename);

        $dir = $path_parts['dirname'];

        $user_id = explode("/" , $dir);
        
        //echo substr($user_id[5],0,strpos($user_id[5],"-"));
        //die();

        //echo  substr($user_id[5],0,strpos($user_id[5],"-")-1) . "\n";

        if(file_exists($dir . "/proceed.txt")){
               # $user_uid = substr($user_id[5], 0, strpos($user_id[5], "-"));
                $user_uid = substr($user_id[5], 0, strpos($user_id[5], "-"));
                $destination = $enhance_folder . $user_uid;
                if(!file_exists($destination)){
                        exec("mkdir " . $destination);
                        echo "folder created!!!" . "\n";
                }
                echo "mv -f " . $dir . "/*.* " . $destination . "/";
				
                exec("mv -f " . $dir . "/*.* " . $destination . "/");
		
                //exec("mv -f " . $dir . "/*.xml " . $destination . "/");

                //exec("mv -f " . $dir . "/*.txt " . $destination . "/");

                exec("rmdir " . $dir);

        }
}


function move_toQenhancer2($filename) {

        $enhance_folder = "/mnt/clipproducer2/mediaclip/enhance_fotobok/";

        $path_parts = pathinfo($filename);

        $dir = $path_parts['dirname'];

        $user_id = explode("/" , $dir);
        
        //echo substr($user_id[5],0,strpos($user_id[5],"-"));
        //die();

        //echo  substr($user_id[5],0,strpos($user_id[5],"-")-1) . "\n";

        if(file_exists($dir . "/proceed.txt")){
               # $user_uid = substr($user_id[5], 0, strpos($user_id[5], "-"));
                $user_uid = substr($user_id[4], 0, strpos($user_id[4], "-"));
                $destination = $enhance_folder . $user_uid;
                if(!file_exists($destination)){
                        exec("mkdir " . $destination);
                        echo "folder created!!!" . "\n";
                }
                echo "mv -f " . $dir . "/*.* " . $destination . "/";

                exec("mv -f " . $dir . "/*.* " . $destination . "/");
		
                //exec("mv -f " . $dir . "/*.xml " . $destination . "/");

                //exec("mv -f " . $dir . "/*.txt " . $destination . "/");

                exec("rmdir " . $dir);

        }
}


function move_toProduction($filename) {

	print_r( $filename );

	$production_folder = "/mnt/clipproducer2/mediaclip/DropBox/";
	
	$path_parts = pathinfo($filename);

	$dir = $path_parts['dirname'];
	$xml = $path_parts['basename'];
	
	$owner = explode('-' , basename($xml, ".xml"));
	
  	if(file_exists($dir . "/proceed.txt") && !file_exists('/mnt/clipproducer2/mediaclip/enhance/' . $owner[3])  && !file_exists('/mnt/clipproducer2/mediaclip/enhance_fotobok/' . $owner[3] ) ){

		exec("mv -f " . $filename . " " . $production_folder . $xml);

  	}
	
}

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
    if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
      find_files($fullname, $pattern, $callback);
    } else if (is_file($fullname) && preg_match($pattern, $entry)) {
      call_user_func($callback, $fullname);
    }
  }
}

?>
