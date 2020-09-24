<?
/**
 * ******************************************************
 * Script for streaming xml width information about
 * album and images to mediclip
 *********************************************************/
import('website.project');
import('website.product');
model('user.mediaclipsession');

class Xml extends WebPage implements IView {

	protected $template = '';
	protected $anonymous = 639866;
	protected $example_images = 2302423;
	
	public function Execute(){
      
        header("HTTP/1.0 404 not found");
        exit( 0 );
      
      //echo $sessionid;
	}
	
	 public function customer( $sessionid ){
        
		//mail( "tor.inge@eurofoto.no", "session" , "session" . print_r(  $_GET , true )  );
		$mcsessionid = DB::query( "SELECT id FROM mediaclip_session WHERE sessionid  = ?", $sessionid )->fetchSingle();
		
		if( !$mcsessionid ){
			return false;
		}
		
		$mcsession = new DBmediaclipSession( $mcsessionid );
		$efcustomer = unserialize( $mcsession->efcustomer );
		
		header( 'content-type: text/xml' );
		
		$xml = new SimpleXMLElement('<customer></customer>');
		$user = $xml->addChild('user');
		
		$user->customerid =  $efcustomer[customerid];
		$user->Sessionid =  $efcustomer[Sessionid];
		$user->idcheck =  $efcustomer[idcheck];
		$user->location =  $efcustomer[location];
		$user->orderid =  $efcustomer[orderid];
		$user->art_type =  $efcustomer[art_type];
		$user->server =  $efcustomer[server];
		$user->predefinert =  $efcustomer[predefinert];
		
		echo $xml->asXML(); 

    }
	
	 /**
    * imagelist
    *
    * @return Xml with customers images.
    */
	public function imagelist($albumid=0, $id) {
	   
	   header( 'content-type: text/xml' );
	   $xml = new SimpleXMLElement("<albums></albums>");
	   
	   if( $albumid == 'uploaded_images' ) {
	      foreach ( UploadedImagesArray::get() as $imageid ){
	        $imageinfo = new image( $imageid );
	        $images[] = $imageinfo->asArray();
	      }
	   }
	   else {
	      if(Project::predefinedAlbumCheck($albumid)){
	         foreach (DB::query( "select bid, owner_uid, tittel, x, y, exif_date from bildeinfo where aid = ?;", $albumid )->fetchAll() as $row){
	            list($bid, $uid, $title, $x, $y, $exif_date) = $row;
	            $images[] = array(
	                "id" => $bid,
	                "title" => $title,
	                "x" => $x,
	                "y" => $y,
	                "exif_date" => $exif_date, 
	                "owner" => array(
	                "uid" => $uid
	                )   
	            );
	         }
   	   }
   	   else{ 
            if( $albumid > 0 ) {
               
               $album = new Album( $albumid );	
         		$images = $album->getImages();
   
            } else if( $albumid == 0 ) {
   
               $imagelist = array();
               $images = new Image();
               foreach( $images->collection( array( 'bid' ), array( 'owner_uid' => Login::userid(), 'aid' => NULL, 'deleted_at' => NULL ) )->fetchAll() as $row ) {
                  try {
                     $image = new Image( array_shift( $row ) );
                     $imagelist []= $image->asArray();
                  } catch( Exception $e ) {
   
                  }
               }
               
               $images = $imagelist;
            }  
   	      
   	   }
	   }
	   
	  
		$albumnode = $xml->addChild('album');
		$imagesnode = $albumnode->addChild('images');
	   
		foreach( $images as $image ) {
		    
		   $imagenode = $imagesnode->addChild('image');
			$imagenode->id = $image['id'];
			$imagenode->uid = $image['owner']['uid'];
			$imagenode->tittel = $image['title'];
			$imagenode->height = $image['y'];
			$imagenode->width = $image['x'];
			$imagenode->date = date('Y-m-d H:i:s',$image['imagedate']);
		   
		}
		
	   echo $xml->asXML(); 
	   
	}
	
    /**
    * Albumlist
    *
    * @return Xml with customers albums.
    */
	public function albumList( $id=0 ) {
	   
	   $project = new Project( $id );
	   
		header( 'content-type: text/xml' );
		
		$xml = new SimpleXMLElement('<albums></albums>');
		
		if($project->predefinert > 0){
         PermissionManager::current()->grantAccessTo( $project->predefinedAid(), 'album', PERMISSION_SHARED );
         $album = new Album( $project->predefinedAid());
		   
         $albumnode = $xml->addChild('album');
         $albumnode->id = $album['id'];
         $albumnode->default_bid = $album['default_bid'];
         $albumnode->uid = $album['ownerid'];
         $albumnode->label = $album['title'];
		}
		
		if( Login::isLoggedIn() ) {
         $albums = $this->owners_album(Login::userid());
         if(is_array($albums) > 0){
            foreach ($albums as $album){
               $albumnode = $xml->addChild('album');
               $albumnode->id = $album['id'];
               $albumnode->default_bid =  $album['default_bid'];
               $albumnode->uid = $album['ownerid'];
               $albumnode->label = $album['title'];
            }
         }
		} else {
		   if( UploadedImagesArray::count() > 0 ){
				$uploadedimages =  UploadedImagesArray::get() ;
				$albumnode = $xml->addChild('album');
				$albumnode->id = 'uploaded_images';
				$albumnode->uid = $this->anonymous;
				$albumnode->label = 'Uploaded Images';
				$albumnode->default_bid =  $uploadedimages[0];
		   }
		}
		if(!isset($xml->album)){
		   
            PermissionManager::current()->grantAccessTo( $this->example_images, 'album', PERMISSION_SHARED );
            $album = new Album(  $this->example_images);
            $albumnode = $xml->addChild('album');
            $albumnode->id = $album['id'];
            $albumnode->default_bid = $album['default_bid'];
            $albumnode->uid = $album['ownerid'];
            $albumnode->label = $album['title'];
		   
		}
				
		echo $xml->asXML(); 
	}
	
	
	public function usedImages($id) {
		
        header( 'content-type: text/xml' );
      
        $xml = new SimpleXMLElement("<albums></albums>");
      
        $projectxml = DB::query("SELECT project_xml FROM mediaclip_orders WHERE id = ?", $id )->fetchSingle();
      
      
      	$albumnode = $xml->addChild('album');
      	$imagesnode = $albumnode->addChild('images');
      
        $usedImages = $this->usedImagesarray($projectxml);
      
		try{
			
			if( is_array( $usedImages)){
				foreach( $usedImages as $image ) {
					
				   $imagenode = $imagesnode->addChild('image');
					$imagenode->id = $image['id'];
					$imagenode->orgName = $image['orgName'];
					$imagenode->uid = $image['uid'];
					$imagenode->label = $image['title'];
					$imagenode->height = $image['y'];
					$imagenode->width = $image['x'];
					$imagenode->date = date('Y-m-d H:i:s',$image['imagedate']);
				   
				}
			}
		}catch( Execption $e ){
			
		};
      	
         echo $xml->asXML(); 
 
   }
   
   public function saveXml(){
      
      import('website.projectorder');
      
      if( Login::userid()){
         $user =  Login::userid();
      }else{
         $user = $this->anonymous;
      }
      
      $mediaclip_server = 'mia.eurofoto.no';
      $id = $_POST['orderid'];
      
      try{
         $project_xml = file_get_contents('http://' . strtolower($mediaclip_server) . '/ECommerceBridge/temp/project_' . $id .'.xml');
         file_put_contents( '/tmp/project_' . $id .'.xml', $project_xml );
        
			$project_xml = substr_replace( $project_xml, '', 0, 3 );
		  
			$xmlData = simplexml_load_string( $tmpXML );

			$project = new ProjectOrder($id);
            $project->projectxml = $project_xml;
            $project->processed = date( 'Y-m-d H:i:s' );
            $project->save();		  
		  

			$orgXmlId = $xmlData["id"];
			
            $xmlId = $project->production_id . "-" . $project->product_id . "-" . $project->quantity . "-" . $project->user_id;
            
			$project_xml = str_ireplace( 'id="' . $orgXmlId . '"' , 'id="' . $xmlId . '"' , $project_xml );
            
			$xml_explode = explode("-",$xmlId);
			
			//if($project['production_id'] != $xml_explode[0]){
			
			
			//Her blir det erstatta  <orderRequest med <orderRequest id="xml navnet til prosjektet" feks id="4309187-7238000-1-589513"
				$project_xml = str_ireplace( '<orderRequest' , '<orderRequest id="' . $xmlId . '"' , $project_xml );
			//}
        
			$filename = sprintf('%s-%s-%s-%s', $project->production_id, $project->product_id, $project->quantity, $project->user_id );
			file_put_contents( '/mnt/produksjon/mediaclip/' . $filename .'.xml' , $project_xml );
            file_put_contents( "/tmp/debug.txt" , "\nsaved\n" );
   
            //$connection = ssh2_connect('zetta.eurofoto.no', 22);
            //ssh2_auth_password($connection, 'produksjon', 'produksjon');
			
            //ssh2_scp_send($connection, '/tmp/project_' . $id .'.xml', '/mnt/mediaclip/DropBox/' . $filename .'.xml', 0644);
            //ssh2_scp_send($connection, '/tmp/project_' . $id .'.xml', '/mnt/clipproducer2/mediaclip/DropBox/' . $filename .'.xml', 0644);
            
        }catch(Exception  $e){
            file_put_contents( "/tmp/debug.txt" , print_r( $e, true ) );
        }
        
        
   }
   
   public function projectXml($id){
      
        $xml_project = DB::query("SELECT project_xml FROM mediaclip_orders WHERE id = ?", $id )->fetchSingle();
      
        //temporary fix for christmascard bug.
        if(strrpos($xml_project,"00_11_Christmas") > 1){
              $xml_project = str_ireplace("00_11_Christmas",	"11_Christmas",	$xml_project);
        }
        
        if(strrpos($xml_project,'Photobook\Themes\30x30cm\01_Babyalbum_jente\Babyalbum_jente\theme.xml') > 1){
              $xml_project = str_ireplace('Photobook\Themes\30x30cm\01_Babyalbum_jente\Babyalbum_jente\theme.xml',	
                                            'Photobook\Themes\30x30cm\01_Babyalbum_jente\theme.xml',
                                            $xml_project);
        }
        
        
        if(strrpos($xml_project,"c:\\mediaclip\\ready\\") > 1){
           $xml_project = str_ireplace("c:\\mediaclip\\ready\\",	"{userFiles}",	$xml_project);
        }
        
        header ( 'content-type: text/xml' );
        echo $xml_project;
      
    }
	
   public function loginCheck(){
      $project_id = $_POST['id'];
      
      $project = new Project($project_id);
      
      
      if( Login::isLoggedIn()){
         $logged_as = Login::userid();
      }else{
         $logged_as = $this->anonymous;
      }
      
      if($project->user_id == $logged_as){
         echo "true";
      }
      else echo "false";
   }
   
   private function owners_album( $owner ){
      
      $albums = array();
            
      #IMAGES WITHOUT ALBUM
      $default_bid_inbox = DB::query( 'SELECT bid FROM bildeinfo WHERE owner_uid = ? AND aid IS NULL AND deleted_at IS NULL Limit 1', $owner )->fetchSingle();
      if($default_bid_inbox){
         $albums[] = array(
            "id" => 0,
            "ownerid" => $owner,
            "title" => "Inbox",
            "default_bid" => $default_bid_inbox, 
         );
      }
      
      #OWNERS ALBUMS
      $album = new album();
      foreach ( $query = $album->collection( array( 'aid', 'uid', 'namn', 'default_bid' ), array( 'uid' => $owner, 'deleted_at' => NULL ), "aid desc" )->fetchAll() as $query ){
         list( $id, $ownerid, $title, $default_bid) = $query;
         
         if( !$default_bid ){
            $default_bid = DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL Limit 1', $id )->fetchSingle();
            if( $default_bid ) {
               
               $album = new Album( $id );
               $album->default_bid = $default_bid;
               $album->save();
            }
         }
         
         if( $default_bid ){
            $albums[] = array(
               "id" => $id,
               "ownerid" => $ownerid,
               "title" => $title,
               "default_bid" => $default_bid, 
            );
         }
      }
      
      #ALBUMS SHARED WITH USER 
      foreach(DB::query( 'SELECT aid, uid, namn, default_bid, deleted_at from bildealbum WHERE aid IN (SELECT aid FROM tilgangtilalbum_dedikert WHERE uid=?)', $owner )->fetchAll() as $query_shared){
         list( $id, $ownerid, $title, $default_bid, $deleted_at ) = $query_shared;    
         if( !$default_bid ){
            $default_bid = DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ? Limit 1', $id )->fetchSingle();
         }
         
         if( $default_bid && !$deleted_at ){
            $albums[] = array(
            "id" => $id,
            "ownerid" => $ownerid,
            "title" => $title,
            "default_bid" => $default_bid, 
            );
         }
      
      }
      return $albums;
   }
   
    public function usedImagesarray($origProjectXML){
            
        if( strpos( $origProjectXML, "collage:photoElement") > 1 || strpos( $origProjectXML, "collage:backgroundElement") > 1 || strpos( $origProjectXML, "model:photo") > 1){
           $tmpXML = preg_replace("/[\n]/", "", $origProjectXML);
           $tmpXML = preg_replace("/\t\t+/", "", $tmpXML);
           // Hack to fix non-standard namespace definitions from Mediaclip.
           $tmpXML = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $tmpXML );
           $tmpXML = utf8_encode( $tmpXML );
           $userid = $image["user_id"] . "-" . $image["id"];
           
           $xmlData = simplexml_load_string( $tmpXML );
           
           // Find all paths to photos and backgrounds.
           $result = array();
           $imageIds = array();
           $image_test = array();
           $collect_nodes = array( '//collage:photoElement', '//collage:backgroundElement');
           if(strpos( $origProjectXML, "calendar:photo" ) !== false ){
              array_push( $collect_nodes, '//calendar:photo' );
           }
           if( strpos( $origProjectXML, "model:photo") !== false){
              array_push( $collect_nodes, '//model:photo' );
           }
           foreach($collect_nodes as $node){
              $result = array_merge( $result, $xmlData->xpath( $node ) );
           }	
           // Process paths if any was found.
           if( count( $result ) > 0 ) {
           // Loop through all paths.
           foreach( $result as $imagepath ) {
              // Filter out library files.
              if($imagepath['fileName']){
                 $resFileName = $imagepath->attributes()->fileName;
              }else $resFileName = $imagepath;
              if ( substr( $resFileName, 0, 9 ) != '{library}' ) {
                 // Extract file path.
                 
                 
                 $resFileName = str_replace( '{userFiles}' , 'c:\\mediaclip\\ready\\', $resFileName );
                 
                 $imagepatharray = explode( '\\', $resFileName );
                 // Get base file name.
                  
                 $fullname = end( $imagepatharray );
                 $owner = $imagepatharray[3];
                 
                 //adds image id to array
                 $imageId = basename( $fullname, ".jpg" );
                 
                 if( !strncmp($imageId, 'facebook', strlen('facebook')) ){
                     
                   $imagefolder = '/data/pd/ef28/facebookimages/' . $owner . '/';
                    $imagefile = $imagefolder . $imageId  . '.jpg';
                   
                    try{
                       list($x, $y, $type, $attr) = getimagesize($imagefile);
                    }
                    catch( Exception $e ){
                       
                    }
                    $imageIds[] = array(
                           "id"=>$imageId,
                           "uid"=>$owner,
                           "x"=>(int)$x,
                           "y"=>(int)$y,
                           "exif_date" => '', 
                           "title"=>$imageId,
                           "orgName" => $resFileName,
                    );
                 
                 }
                 else if($imageId > 0 && !in_array( $imageId, $image_test )){
                    $image_test[] = $imageId;
                    //$image_info = new Image($imageId);
                    $image_info = DB::query( "select x, y, exif_date, tittel from bildeinfo where bid = ?", $imageId )->fetchRow();
                    
                    list($x, $y , $exif_date, $title) = $image_info;
                    
                    $imageIds[] = array(
                                 "id"=>$imageId,
                                 "uid"=>$owner,
                                 "x"=>(int)$x,
                                 "y"=>(int)$y,
                                 "exif_date" => $exif_date, 
                                 "title"=>$title
                                 );
                 }
              }
              }
           }
        
        }
         
      return  $imageIds;	
      }
}



?>