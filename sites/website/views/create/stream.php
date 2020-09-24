<?PHP
config( 'website.storage' );
model('user.mediaclipsession');
   class Stream extends Webpage implements IView {
      
      protected $template = '';
      private $secret_code = "yz9987gd";
      
      public function execute(){
         relocate( '/frontpage/' );
         die();
      }
      
      public function facebook( $bid=0, $code ){
         $code = explode("-", $code);
         $CustomerID = explode("-", base64_decode( $code[0]) ); 
         $user = new User( $CustomerID[0] );
         $idcheck = md5(md5($user->registrert . $this->secret_code) . $bid);
         
         if( $idcheck == $code[1] ){
              
         }
      }
	  
	  public function thumbnailMinSky( $bid = 0){
		 
		 $imgeinfo = explode('_', $bid );
		 
		 $imageid = $imgeinfo[1];
		 $id = $imgeinfo[0];
		 
		 $remoteImage = "https://t0.cptr.no/th/3/" . $imageid . "?share=" . $id . "&area=256&clip=1&pri=1&auth=&key=KVEz-WDMsj";
		 $imginfo = getimagesize($remoteImage);
		 header( "Content-Type: image/jpeg" );
		 readfile($remoteImage);
		 
	  }
      
	  public function newimageMinSky( $bid = 0){
		 $imgeinfo = explode('_', $bid );
		 $imageid = $imgeinfo[1];
		 $id = $imgeinfo[0];
		 
		 $remoteImage = "https://t0.cptr.no/th/3/" . $imageid . "?share=" . $id . "&area=1280&clip=1&pri=1&auth=&key=KVEz-WDMsj";
		 $imginfo = getimagesize($remoteImage);
		 header( "Content-Type: image/jpeg" );
		 readfile($remoteImage);
		 
	  }
	  
	  public function MinSkyLarge( $bid = 0){
		 $imgeinfo = explode('_', $bid );
		 $imageid = $imgeinfo[1];
		 $id = $imgeinfo[0];
	
		 $imgfolder = "/data/pd/minsky/";
		 $imagefile = $imgfolder. $id . "/" . $imageid . "_screen.jpg";
		 
		 $imginfo = getimagesize($imagefile);
		 header( "Content-Type: image/jpeg" );
		 readfile($imagefile);
		 
	  }
      
      public function thumbnail($bid=0, $code){
         $code = explode("-", $code);
         $CustomerID = explode("-", base64_decode( $code[0]) ); 
         $user = new User( $CustomerID[0] );
         $idcheck = md5(md5($user->registrert . $this->secret_code) . $bid);
         
         if( $idcheck == $code[1] ){
            
         try{
            $thumb = new DBObject( $bid );
            
               $imagespath = Settings::Get( 'storage', 'path');
               $filsrc = $imagespath . $thumb->filnamn  . ".preview.jpg";
                  
               $headers = getallheaders();
               
               if( isset( $headers['If-None-Match'] ) && ereg( $thumb->hashcode, $headers['If-None-Match'] )) {
                  // Output a 304 Not Modified header
                  header( 'HTTP/1.1 304 Not Modified' );
                  header( 'Content-Length: 0' );
                  exit;
               }   
               else{
                  
                  header( "Content-Type: image/jpeg" );
                  header( 'content-length: ' . filesize( $filsrc ) );
                  // setup caching headers
                  header( "ETag: \"" . $thumb->hashcode . "\"");
                  header( "Accept-Ranges: bytes");
                  header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                  header( 'Cache-Control: public' );
                  header( 'Pragma: public' );
                  
                  readfile ($filsrc);
               }
            
            }catch (Exception $e){
               //util::Debug($e->getMessage());
            }
         }
      }
      
	  
	  public function newimage( $bid, $facebookimage = null  ){
		 
		 $sessionid = $_GET['sessionId'];
		 $code = $_GET['customerid'];
		 $filter = $_POST['filters'];

		 if( $filter ){
			$bw = 1;
		 }
		 
		 
		 $mcsession = DBmediaclipSession::fromFieldValue(
               array(
                  'sessionid' => $sessionid
               ),
               'DBmediaclipSession'
            );
		 
		 $userid = $mcsession->userid;
		 //$userid = DB::query( "SELECT userid FROM mediaclip_session WHERE sessionid =?", $sessionid  )->fetchSingle();
		 
		 $code = explode("-", $code);
         $CustomerID = explode("-", base64_decode( $code[0]) ); 
         $user = new User( $CustomerID[0] );
         $idcheck = md5(md5($user->registrert . $this->secret_code) . $bid);

		 //$mcsessionid = DB::query( "SELECT id FROM mediaclip_session WHERE sessionid  = ?", $sessionid )->fetchSingle();
		 
		 		 
		 if( $facebookimage  ){
			
			   $facebookimage = str_replace( ' ', '/' , $facebookimage );
			
			   $imagefolder = '/data/pd/ef28/facebookimages/' . $userid ;
			   
			   if( strpos( $bid, 'userFiles' )  ){
				  $imagefile = $imagefolder . '/' . $facebookimage;
			   }else{
				  $imagefile = $imagefolder . '/' . $bid;
			   }
			   if( !file_exists( $imagefolder ) ){
				  mkdir( $imagefolder );
			   }
			   
			   //Util::Debug( $imagefile );
			   //exit;
			   
			   if( file_exists( $imagefile ) ){
				  $stream = file_get_contents( $imagefile );
			   }
			   else{
				   $image = base64_decode( $facebookimage );
				   $stream = file_get_contents($image);
				   file_put_contents(  $imagefile , $stream );
				   
			   }
			   
				 header("Content-Type: image/jpeg");
			   echo $stream; 
		   
		}
		else{
		 
		 if( $idcheck !== $code[1]  ){
			 return false;
		 }
		 
		 try{
			$width = $_GET['dx'];
			$height = $_GET['dy'];
			
			if($width == 0){
			   $width = 800;
			}
			if($height == 0){
			   $height = 600;
			}
			$image = new DBObject( $bid );
			
			$imagespath = Settings::Get( 'storage', 'path');
			
			$filesrc = $imagespath . $image->filnamn;
			
			   $cachefilename = "/var/tmp/" .  $image->hashcode . "_" . $width . "_" . $height . "_$rotate" .  "$bw.jpg";
			   
			   if($image->hashcode && file_exists( $cachefilename )){
				  session_write_close();
				  // grab all request headers
				  $headers = getallheaders();
				  // if browser sent id, we check if they match
				  if( isset( $headers['If-None-Match'] ) && ereg( $image->hashcode, $headers['If-None-Match'] ) ) {
					 // Output a 304 Not Modified header
					 header( 'HTTP/1.1 304 Not Modified' );
					 header( 'Content-Length: 0' );
					 exit;
				  
				  } else {
					 header("Content-Type: image/jpeg");
					 header( 'content-length: '.filesize( $cachefilename ) );
					 // setup caching headers
					 header( "ETag: \"" . $image->hashcode . "\"");
					 header( "Accept-Ranges: bytes");
					 header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
					 header( 'Cache-Control: public' );
					 header( 'Pragma: public' );
					 readfile ($cachefilename);
				  }
			   }
			   else{

				  $image = new Imagick($filesrc);
				  $image->thumbnailImage( (int) $width, (int) $height, true);
				  
				  if( $bw == 1 ){
                     $image->modulateImage(100,0,100);
                  }
				  
				  $image->writeImage($cachefilename);
				  header( "Content-Type: image/jpeg" );           
				  header( 'content-length: ' . filesize( $cachefilename ) );
				  // setup caching headers
				  
				  //header( "ETag: \"" . $image->hashcode . "\"");
				  header( "Accept-Ranges: bytes");
				  header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
				  header( 'Cache-Control: public' );
				  header( 'Pragma: public' );
				  
				  readfile ($cachefilename);
			   }
			   
			}catch (Exception $e){
		 
		  }
		}
		 
		 
	  }
	  
      public function image( $bid, $code, $facebookimage = null  ){
         
         $code = explode("-", $code);
         $CustomerID = explode("-", base64_decode( $code[0]) ); 
         $user = new User( $CustomerID[0] );
         $idcheck = md5(md5($user->registrert . $this->secret_code) . $bid);
         
         
         
         if( $idcheck == $code[1] ){
            
            if( !strncmp($bid, 'facebook', strlen('facebook') ) ){
               	   $imagefolder = '/data/pd/ef28/facebookimages/' . $CustomerID[0] ;
               	   $imagefile = $imagefolder . '/' . $bid  . '.jpg';
               	   
               	   if( !file_exists( $imagefolder ) ){
               	      mkdir( $imagefolder );
               	   }
               	   
               	   if( file_exists( $imagefile )){
					 if( filesize( $imagefile ) > 5 ){
               	      $stream = file_get_contents( $imagefile );
					 }else{
						unlink($imagefile);
						$image = base64_decode( $facebookimage );
						$stream = file_get_contents($image);
						file_put_contents(  $imagefile , $stream );
					 }
               	   }
               	   else{
               	       $image = base64_decode( $facebookimage );
               	       $stream = file_get_contents($image);
               	       file_put_contents(  $imagefile , $stream );
               	       
               	   }
               	   
                     header("Content-Type: image/jpeg");
               	   echo $stream; 
               
            }
            else{
            
               try{
                  $width = $_GET['dx'];
                  $height = $_GET['dy'];
                  
                  if($width == 0){
                     $width = 800;
                  }
                  if($height == 0){
                     $height = 600;
                  }
                  $image = new DBObject( $bid );
                  
                  $imagespath = Settings::Get( 'storage', 'path');
                  
                  $filesrc = $imagespath . "/" . $image->filnamn;
                  
                     $cachefilename = "/var/tmp/" .  $image->hashcode . "_" . $width . "_" . $height . "_$rotate" . ".jpg";
                     
                     if($image->hashcode && file_exists($cachefilename)){
                        session_write_close();
                        // grab all request headers
                        $headers = getallheaders();
                        // if browser sent id, we check if they match
                        if( isset( $headers['If-None-Match'] ) && ereg( $image->hashcode, $headers['If-None-Match'] ) ) {
                           // Output a 304 Not Modified header
                           header( 'HTTP/1.1 304 Not Modified' );
                           header( 'Content-Length: 0' );
                           exit;
                        
                        } else {
                           header("Content-Type: image/jpeg");
                           header( 'content-length: '.filesize( $cachefilename ) );
                           // setup caching headers
                           header( "ETag: \"" . $image->hashcode . "\"");
                           header( "Accept-Ranges: bytes");
                           header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                           header( 'Cache-Control: public' );
                           header( 'Pragma: public' );
                           readfile ($cachefilename);
                        }
                     }
                     else{
                        $image = new Imagick($filesrc);
                        $image->thumbnailImage( (int) $width, (int) $height, true);
                        $image->writeImage($cachefilename);
                        header( "Content-Type: image/jpeg" );           
                        header( 'content-length: ' . filesize( $cachefilename ) );
                        // setup caching headers
                        header( "ETag: \"" . $image->hashcode . "\"");
                        header( "Accept-Ranges: bytes");
                        header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                        header( 'Cache-Control: public' );
                        header( 'Pragma: public' );
                        
                        readfile ($cachefilename);
                     }
                     
                  }catch (Exception $e){
               
                }
            }
         }
      }  
   }


?>