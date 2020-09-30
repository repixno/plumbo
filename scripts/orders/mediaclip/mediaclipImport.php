<?PHP
    chdir( dirname( __FILE__ ) );
    include '../../../bootstrap.php';
    
    config( 'website.config' );
    config( 'website.countries' );
    config( 'production.settings');
    import( 'system.cli' );
    import( 'website.projectorder' );
    library( 'xml.xmlarray' );
    
    
    class MediaclipImportScript extends Script {
        
        private $photobookArray = array( 877, 879, 889, 890, 891, 897, 908, 7021 );
		//private $photobookArray = array( 877, 879, 889, 890, 891, 897, 908, 7021,7601,7602,7603,7604,7605,7606,7607,7608,7609,7610,7611,7612,7613,7614,7615,7616,7617,7618,7619,7620,7621,7622,7623,7624,7625 );
		
        private $mediaclip_not_logged_in_user  = 639866;
        //private $medacipfolder =  "/home/produksjon/mediaclip/";
          //private $medacipfolder =  "/mnt/mediaclip/";
        private $medacipfolder =  "/mnt/clipproducer2/mediaclip/";
        private $savepath = "";
        private $projectXml = "";
        private $userid = "";
        private $imageserver = "http://therese.eurofoto.no/production/index.php";
        private $securecode = "p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ";
        
        //img server= eva
        private $imgserver = "10.64.1.134";
        
        private $sshuser = 'www-data';
        private $sshpass = 'Kefir4ever';
        
        
        
        Public function Main(){
            
         $current_orders = ProjectOrder::readyOrders();
        //   $current_orders = DB::query( "SELECT * FROM mediaclip_orders WHERE order_id in ( 2402585,2402448,2402443,2402881,2403064,2403284,2403053,2402251,2402419 )" )->fetchAll( DB::FETCH_ASSOC );
				// $current_orders = DB::query( "SELECT * FROM mediaclip_orders WHERE  production_id in (4232057)" )->fetchAll( DB::FETCH_ASSOC );
            
            
           
            if( count( $current_orders ) > 0  ){
                    
                foreach( $current_orders as $order ) {  
                    $this->projectXml = $order['project_xml'];

                    //$this->xmlFix();
                    
                    $this->userid = $order['user_id'];
                    
                    $this->getsavePath( $order['no_qenhancer'], $order['article_id'] );
                    $this->replaceOwnerId();
                    $this->stringReplace();
                    
                    
		//Util::debug($this->projectXml);
			
                    $calenderbug = '          <calendar:dayContent day="1">
            <calendar:holidayText text="2. påskedag" />
          </calendar:dayContent>';
          
                    $calenderfix = '          <calendar:dayContent day="28">
            <calendar:holidayText text="2. påskedag" />
          </calendar:dayContent>';
          
          
                             
                     $this->projectXml  = str_replace( $calenderbug, $calenderfix, $this->projectXml );
                    
                    $tmpXML = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $this->projectXml  );

                    $xmlData = simplexml_load_string( $tmpXML );
                    
                    $orgXmlId = $xmlData["id"];
                    $xmlId = $order['production_id'] . "-" . $order['product_id'] . "-" . $order['quantity'] . "-" . $order['user_id'];
                    
                    if( $orgXmlId ){
                        $this->projectXml = str_ireplace( 'id="' . $orgXmlId . '"' , 'id="' . $xmlId . '"' , $this->projectXml );
                    }else{
                        $this->projectXml = str_ireplace( 'creationDate' , 'id="' . $xmlId . '" creationDate' , $this->projectXml );
                    }
                    
                    
                    
                    $xml_explode = explode("-",$xmlId);
                    if($order['production_id'] != $xml_explode[0]){
                        $this->projectXml = str_ireplace( 'id="' . $order['production_id'] . '"' , 'id="' . $xmlId . '"' , $this->projectXml );
                    }
                    if($order['article_id'] ==  7039){
                        echo "Folded Greetingcard\n";   
                        $this->addGreetingcardXml( $order['order_id'] );
                    }
                    
                    if( strlen( $xmlId ) > 0 ) {
                        
                        $result = array();
                        $collect_nodes = array('//collage:photoElement', '//collage:backgroundElement');
                        if(strpos($this->projectXml,"calendar:photo")!==false){
                            array_push($collect_nodes, '//calendar:photo');
                        }
			 if(strpos($this->projectXml,"collage:photo")!==false){
                            array_push($collect_nodes, '//collage:photo');
                        }

                        
                        // Find all paths to photos and backgrounds.
                        foreach($collect_nodes as $node){
                            $result = array_merge($result, $xmlData->xpath($node));
                            
                        }

                        // Process paths if any was found.
                        if( count( $result ) > 0 ) {
                            $imageList = array();
                            $facebookImageList = array();
                            $minSkyImages = array();
                            // Loop through all paths.
                            
                            foreach( $result as $imagepath ) {
							//Util::debug( $imagepath );
                                // Filter out library files.
                                $resFileName = $imagepath->attributes()->fileName;
                                
                                if ( substr( $resFileName, 0, 9 ) != '{library}' ) {
                                    // Extract file path.
                                    $imagepatharray = explode( '\\', $resFileName );
                                    // Get base file name.
                                    $fullname = end( $imagepatharray );
									$imagename = basename( $fullname );
                                    list( $imageId ) = explode( ".", $imagename );
									Util::debug("imageid" .  $imageId);
                                    if(is_numeric($imageId)){
                                        $imageList[] = $imageId;
                                    }else if( strpos( $imageId, 'facebook' ) === 0  ){
                                        $facebookImageList[] = (string)$imageId;
                                    }else if( strpos( $resFileName, 'minSky' ) ){
                                        $minSkyImages[]  = $imageId;
                                    }
                                }
                            }
                        }
                       
						Util::Debug( $imageList ); 
                        if( count( $imageList) > 0 ){
                            // Image id string for database lookup.
                            $images = implode( ",", $imageList );
                            // Get file data from database.
                            $queryString = "SELECT bid, filnamn, filtype FROM bildeinfo WHERE bid IN( %s )";
                            $imagepaths = DB::query( sprintf( $queryString, $images ) )->fetchAll( DB::FETCH_ASSOC );
                        }
                        
                        //$filesToCopyStr = '';
                        // Loop through all photo files as seen from database.
                        if( $order['no_qenhancer'] == 't' ){
                            $this->savepath = $this->savepath . "/" . $this->userid; 
                        }else{
                            $this->savepath = $this->savepath . "/" . $this->userid . '-' . $order["id"]; 
                        }
                        
                        try{
                            if( !file_exists( $this->savepath ) ){
                                mkdir( $this->savepath, 0755, true );
                            }
                        }catch( Exception $e ){
                            Util::Debug( $e->getMessage() );
                            
                        Util::Debug($order);
                        }
                        
                        
                        
                        //Util::Debug($minSkyImages);
                        
                        if( count( $minSkyImages  > 0 ) && is_array( $minSkyImages ) ){
                            
                            foreach( $minSkyImages as $minSkyImage ){
                                
                                $bid = basename($minSkyImage);
                                
                                //Util::Debug($bid);
                                
                                $imgeinfo = explode('_', $bid );
                                $imageid = $imgeinfo[1];
                                $id = $imgeinfo[0];
                                
                                $remoteImage = "https://t0.cptr.no/th/3/" . $imageid . "?share=" . $id . "&area=1280&clip=1&pri=1&auth=&key=KVEz-WDMsj";
                                
                                
                                $img = $this->savepath . "/minSky-" . basename( $minSkyImage ) . ".jpg";
                                
                                
                                //Util::Debug($img);
                                
                                
                                file_put_contents( $img, file_get_contents($remoteImage) );   
                            } 
                        }
                        
                        $this->projectXml = str_ireplace( 'minSky\\' , $order["user_id"] . '\\' . 'minSky-' , $this->projectXml );
                        
                        
                        
                        
                        //Util::Debug($facebookImageList);

                        
                        if( count( $facebookImageList ) > 0 ){
                            foreach ( $facebookImageList as $facebookImage ){
                                $origPath = '/data/pd/ef28/facebookimages/' . $order["user_id"] . '/' . $facebookImage . ".jpg";
                                $url = $this->imageserver .  "?path=" . base64_encode( $origPath ) . "&secure=" . $this->securecode;
                                $img = $this->savepath . "/" . basename( $origPath);
                                
                                Util::Debug($url);
                                
                                try{
                                    file_put_contents( $img, file_get_contents($url) );
                                }
                                catch (Exception $e){
                                    echo $e->getMessage() . "\n";
                                }
                              
                            }
                        }
                        
                        if( is_array( $imagepaths )){
                            foreach( $imagepaths as $image ) {
                                
                                //$url = $this->imageserver .  "?path=" . base64_encode( $image["filnamn"] ) . "&secure=" . $this->securecode;
                                //$img = $this->savepath . "/" . basename( $image["filnamn"] );
                                $img = $this->savepath . "/" . $image["bid"] . ".jpg";
                                // Check if original file exists.
                                try{
                                    
                                    //file_put_contents( $img, file_get_contents($url) );
                                    
                                    $hashcode = DB::query( "SELECT hashcode FROM bildeinfo WHERE bid = ?", $image['bid'] )->fetchSingle();
                                    $checksum = null;
                                    //Util::Debug( $hashcode );
                                    
                                    $count = 0;
                                    while( $checksum != $hashcode ){
                                       $count++;
                                       if( file_exists( $img )  ){
                                          $checksum = md5_file($img);
                                          if( $checksum ==  $hashcode ){
                                             continue;
                                          }else{
                                             unlink( $img );
                                          }
                                        }
                                       
                                        //$connection = ssh2_connect('therese.eurofoto.no', 22);
                                        $connection = ssh2_connect($this->imgserver, 22);
                                        //ssh2_auth_password($connection, 'www-data', 'Kefir4ever!');
                                        ssh2_auth_password($connection, 'www', 'Kefir4ever!');
                                        
                                        ssh2_scp_recv( $connection, '/data/bildearkiv/' . $image["filnamn"] , $img);
                                       
                                        Util::Debug('/data/bildearkiv/' . $image["filnamn"]);
                                       
                                        //file_put_contents( $img, file_get_contents($url) );
                                        $checksum = md5_file($img);
                                        
                                        if( $count > 1 ){
                                            util::Debug( "filename: " .  $image["filnamn"] . "-" .$count . "#BUUUUUUUUUUUUUUUUUUUUUUUUUGS " );
                                            Util::Debug( "Checksumm = " . $checksum );
                                            Util::Debug( "Hashcode = " . $hashcode );
                                        }
                                        
                                        if( $count > 10 ){
                                           $checksum = $hashcode;
                                           util::Debug( "FEIL MED NEDLASTING AV FIL!!!!! " );
                                        }
                                        //Util::Debug( "Checksumm = " . $checksum );
                                    }
                                }
                                catch (Exception $e){
                                    util::debug(  $e->getMessage() );
                                }
                            }
                        }
                        if( $error != 1 ) {
                            //save xml file
                            
                            try{         
                                file_put_contents( $this->savepath . "/$xmlId.xml", $this->projectXml  );
                            }
                            catch (Exception $e){
                                echo $e->getMessage() . "\n";
                            }
                            //leave xml file
                            try{
      
                                file_put_contents( $this->savepath . "/proceed.txt" , date( "Y-d-m H:i:s" ) );
                            }
                            catch (Exception $e){
                                echo $e->getMessage() . "\n";
                            }
                            // Update processed status of orders.
                            DB::query( "UPDATE mediaclip_orders SET processed=now() WHERE id = ?" , $order[ 'id' ] );
                        } else {
                            $error = 0;
                        }
                    }
                    
                    Util::Debug( $orgXmlId );
                       
                }   
            }
        }
        
        
        private function xmlFix(){
            // Hack to fix non-standard namespace definitions from Mediaclip.
            $this->projectXml = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $this->projectXml );
            $tmpXML = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $origProjectXML );

        }
        
        
        private function getsavePath( $no_qenhancer, $article_id  ){
            
            if($no_qenhancer== 't'){
                $this->savepath = $this->medacipfolder . "ready";
            }
            /*else if( in_array( $article_id , $this->photobookArray ) ){
                $this->savepath = $this->medacipfolder .  "prepare_fotobok";
            }*/
            else{
                $this->savepath = $this->medacipfolder .  "prepare";
             }
            
            
        }
        
        
        private function replaceOwnerId( ){
            $replaceId = $this->userid;
	    $this->projectXml = str_replace( '{userFiles}0/', '{userFiles}'. $replaceId . '/', $this->projectXml );
       
       // endra så denn  {userFiles}\235402758.jpg var til denne {userFiles}1363520\235402758.jpg
         $this->projectXml = str_replace( '{userFiles}/', '{userFiles}'. $replaceId . '/', $this->projectXml );
	    $origProjectXML = $this->projectXml; 
            
	 if(strpos($origProjectXML,"collage:photoElement") > 1 || strpos($origProjectXML,"collage:backgroundElement") > 1 || strpos($origProjectXML,"model:photo") > 1){
                
                $tmpXML = preg_replace("/[\n]/", "", $origProjectXML);
                $tmpXML = preg_replace("/\t\t+/", "", $tmpXML);
                // Hack to fix non-standard namespace definitions from Mediaclip.
                $tmpXML = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $tmpXML );
                $tmpXML = utf8_encode( $tmpXML );
                $userid = $image["user_id"]."-".$image["id"];
     
                $xmlData = simplexml_load_string( $tmpXML );
                
                // Find all paths to photos and backgrounds.
                $result = array();
                $imageIds = array();
                $image_test = array();
                $collect_nodes = array('//collage:photoElement', '//collage:backgroundElement');
                if(strpos($origProjectXML,"calendar:photo")!==false){
                    array_push($collect_nodes, '//calendar:photo');
                }
                if(strpos($origProjectXML,"model:photo")!==false){
                    array_push($collect_nodes, '//model:photo');
                }
                foreach($collect_nodes as $node){
                    $result = array_merge($result, $xmlData->xpath($node));
                }	
                // Process paths if any was found.
                $owners = array();
                if( count( $result ) > 0 ) {
                    // Loop through all paths.
                    foreach( $result as $imagepath ) {
                        // Filter out library files.
                        if($imagepath['fileName']){
                            $resFileName = $imagepath->attributes()->fileName;
                        }else $resFileName = $imagepath;
                
                        //Util::Debug("resFileName" . $resFileName);
                
                        //if ( substr( $resFileName, 0, 9 ) != '{library}' && substr( $resFileName, 0 , 9 ) !=  "$(package" && $resFileName !=  "{resources}" ) {
                        if ( substr( $resFileName, 0, 11 ) == '{userFiles}'  && !strpos( $resFileName, 'minSky' )  ) {
                            
							 Util::Debug($resFileName);
							
                            $resFileName = str_ireplace( "{userFiles}" , "" , $resFileName );
                            
                            $imagepatharray = explode( '\\', $resFileName );
							
							Util::Debug("imagepatharray");
							Util::Debug($imagepatharray);
							
							if( count( $imagepatharray ) == 1  ){
								if( strpos( $imagepatharray[0] , "$replaceId") === 0  ){
									$newimagefile = str_replace( "/", "\\", $imagepatharray[0] );
								}else{
								$newimagefile = str_replace( "/", "", $imagepatharray[0] );
							
								$this->projectXml = str_ireplace( "{userFiles}"  . $imagepatharray[0] , "{userFiles}"  . $replaceId . "\\" . $newimagefile , $this->projectXml );
								}
								util::Debug( $imagepatharray );
							}
                            if($imagepatharray[0] != "" && count( $imagepatharray ) > 1 ){
                                $owners[] = $imagepatharray[0];
                            }
                            if($imagepatharray[3] != ""){
                                $owners[] = $imagepatharray[3];
                            }
                        }
                    }
                }
                
                foreach( $owners as $owner){
                    if( $this->userid != $owner ){
			Util::debug( $owner );
                        $this->projectXml = str_ireplace( "\"".$owner."\"", "\"".$replaceId."\"", $this->projectXml );
                        $this->projectXml = str_ireplace( "\\".$owner."\\", "\\".$replaceId."\\", $this->projectXml );
                        $this->projectXml = str_ireplace( "{userFiles}" . $owner , "{userFiles}" . $replaceId , $this->projectXml );
                    }
                }
				
				$this->projectXml = str_ireplace( "/\\", "\\", $this->projectXml );
        
            }
			
			
			//Util::Debug($this->projectXml );
        }
        
        
        
        private function stringReplace(){
            
            $strings = array(
                0 => array(  "old" => "Insert your text here", "new" => " " ),
                1 => array(  "old" => "Her kan du sette inn tekst", "new" => " " ),
                2 => array(  "old" => "Sett inn tekst her", "new" => " " ),
                3 => array(  "old" => 'color="000000"', "new" => 'color="#000000"' ),
                3 => array(  "old" => 'fontFamily="Edwardian"', "new" => 'fontFamily="Edwardian Scr ITC TT"' ),
            );
            
            
            foreach( $strings as $string ){
                $this->projectXml = str_ireplace( $string['old'], $string['new'], $this->projectXml  );
                
            }
            
        }
        
        private function addGreetingcardXml( $ordrenr ){
   
            $xmlarray = xml2ary( $this->projectXml );
   
            $width = $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]['_a']['width'];
            $height = $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]['_a']['height'];
            
            
            $x_pos = $width - 320;
            $y_pos = $height - 100;
              
            $extra_text = array(
               "_a" => array(
                     "x"=>$x_pos,
                     "y"=>$y_pos,
                     "width"=>250,
                     "height"=>60,
                     "scaleToFit"=>"true",
                     "text"=>"www.eurofoto.no{br}$ordrenr",
                     "wordWrap"=>"false"
                  )
                  ,
                  "_c" => array(
                     "collage:textStyle" => array(
                           "_a" => array(
                              "fontSize"=>6,
                               "vAlign"=>"top",
                               "fontFamily" => "Trebuchet MS"
                            ),
                            "_c" => array(
                               "collage:straightBackground" => array(
                                    "_a" => array(
                                    "opacity"=>"68"
                                    ),
                                    "_v" => ""
                               )
                            )
                        ),
                  )
               );
            
           
            
            $textarray = $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement'];
            
            #print_r($xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement']);
            
            
            if($textarray[0]){
               $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement'][] = $extra_text;
            }
            else if($textarray){
               $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement']  = array(
               0 => $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement'],
               1 => $extra_text
               );
            }
            else{
               $xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement'] = $extra_text;
            }
            
             
             
            #$xmlarray['orderRequest']['_c']['projects']['_c']['greetingCard:greetingCard']['_c']['greetingCard:page'][3]["_c"]['collage:textElement'] = $extra_text;
         
                    
               
             $this->projectXml = ary2xml($xmlarray);
            
         }
        
        
    }
    





CLI::Execute();

?>
