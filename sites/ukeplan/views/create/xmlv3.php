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
		//$mcsessionid = DB::query( "SELECT id FROM mediaclip_session WHERE sessionid  = ?", $sessionid )->fetchSingle();
		
		
		$mcsession = DBmediaclipSession::fromFieldValue(
               array(
                  'sessionid' => $sessionid
               ),
               'DBmediaclipSession'
            );
		
		if( !$mcsession ){
			return false;
		}
		
		//$mcsession = new DBmediaclipSession( $mcsessionid );
		
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
		$user->adminuser =  $efcustomer[adminuser];
		
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
	   
	   try{
		$project = new Project( $id );
	   }catch( Exception $e ){
		
	   }
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
		
      if( Login::userid()){
         $user =  Login::userid();
      }else{
         $user = $this->anonymous;
      }
	   
     
      
      $xml = new SimpleXMLElement("<albums></albums>");
      
      $album = new Project($id);
	  
      if($album->user_id == $user){
		
		
		$albumnode = $xml->addChild('album');
      	$imagesnode = $albumnode->addChild('images');
		
		if( $album->share_id ){
		  import( 'website.minsky' );
		  
		  
		  try{
			
			$skyproject = minSky::projectFromProjectid( $album->share_id );
			$skyimagelist = json_decode( $skyproject->imagelist );
			
			
			
				if( count( $skyimagelist ) > 0 ){
					foreach( $skyimagelist as $image ) {
						
					    $imagenode = $imagesnode->addChild('image');
						$imagenode->id = $image->id;
						$imagenode->orgName = $album->share_id . "_" . $image->id . ".jpg";
						$imagenode->uid = 0;
						$imagenode->label = $image->path;
						$imagenode->height = $image->height;
						$imagenode->width = $image->width;
						$imagenode->date = date('Y-m-d H:i:s', strtotime( $image->date) );
				   
					}
				}
			}catch( Execption $e ){
				
			};
			  
		  
		  
		}
      	
		/*
		try{
			
			if( is_array( $album->usedImages() ) ){
				foreach( $album->usedImages() as $image ) {
					
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
			
		};*/
      	 header( 'content-type: text/xml' );
         echo $xml->asXML(); 
      }
   }
   
	/**
	* xmlPrice
	*
	* @return Xml with product price.
	*/
	public function xmlPrice( $productid=0, $quantity=1, $url='' ){
	//  $project = new Project();
	 // $productid = $project->productInfo($productid);
	  
	  /*$product = new ProductOption();
	  
	  $product->refid = $productid;
	  $price = $product->getPrice($quantity);
	  
	  $product->refid = 878;
	  $xtra = $product->getPrice($quantity);*/
	  
	  $price = $this->XmlGetPrice($productid, $quantity , $url);
	  
	  $xtra = $this->XmlGetPrice(878, 1 , $url);
	  
	  $xml = new SimpleXMLElement("<prices></prices>");
	  $xml->price = number_format($price, 2, ',', '');
	  $xml->xtra = number_format($xtra, 2, ',', '');
	  
	  header ( 'content-type: text/xml' );
	  echo $xml->asXML();  
	}
	
	
	public function xmlPrice2( $sessionid, $quantity  = 1, $url = '', $xtrapages = null  ){
		
		//$mcsessionid = DB::query( "SELECT id FROM mediaclip_session WHERE sessionid  = ?", $sessionid )->fetchSingle();
		$mcsession = DBmediaclipSession::fromFieldValue(
			array(
			   'sessionid' => $sessionid
			),
			'DBmediaclipSession'
		 );
		if( !$mcsession ){
			return false;
		}
		//$mcsession = new DBmediaclipSession( $mcsessionid );
		$efcustomer = unserialize( $mcsession->efcustomer );
		
		$url = $efcustomer['location'];
		
		$art_type = $efcustomer['art_type'];
		
	  /*$product = new ProductOption();
	  $product->refid = $productid;
	  $price = $product->getPrice($quantity);
	  $product->refid = 878;
	  $xtra = $product->getPrice($quantity);*/
	  
	  //$quantity = 100;
	  
	  if( strlen( $art_type ) > 4 ){
		$art_type = substr( $art_type , 0, -3);
	  }
	  try{
		$price = $this->XmlGetPrice($art_type, $quantity );
	  }catch( Exception $e ){
		
	  }
	  $xtra = $this->XmlGetPrice(878, 1 , $url);
	  
	  $xml = new SimpleXMLElement("<prices></prices>");
	  $xml->price = number_format($price, 2, ',', '');
	  $xml->xtra = number_format($xtra, 2, ',', '');
	  
	  header ( 'content-type: text/xml' );
	  echo $xml->asXML();  
	}
	
	private function XmlGetPrice( $refid, $quantity = 1, $url = '') {
		 $url = base64_decode( $url );
		 $url = substr($url, 7);
		 $sites = Settings::getSection( 'domainMap' );
		 $portalid = isset( $sites[$url]['customattr']['portalid'] ) ? $sites[$url]['customattr']['portalid'] : '';
		 $countryid = isset( $sites[$url]['customattr']['countryid'] ) ? $sites[$url]['customattr']['countryid'] : '';
		 if( empty( $countryid ) ){
			$countryid = Dispatcher::getCustomAttr( 'countryid' );
		 }
		 if(!is_numeric($portalid)){
		   $portalid = 0;
		 }
		 
		 if ( $regionid = $local_regionid[$countryid][$portalid] ) {	# Hardcoded most requested query, ugly-bugly but db is strained
			
		 } else {
		 
		   $data = DB::query( "
			   SELECT region.regionid 
			   FROM region_nations, region 
			   WHERE region_nations.regionid = region.regionid AND 
			   nationid=? AND 
			   portalid=?
		   ", $countryid, $portalid );
			
		   list( $regionid ) = $data->fetchRow();
		 }
		 
		 $productgroup = ProductOption::getProductParent( $refid, $regionid );
		 $identifier = sprintf( 'productoption-price-%s-%s-%d-%d', $regionid, $productgroup, $quantity, $refid );
		 if( $price = CacheEngine::read( $identifier ) ) return $price;
	
		 $price = DB::query( "
			SELECT
			   price
			FROM prices
			WHERE artnr=?
			   AND regionid=?
			   AND pricegroup
			   IN( SELECT quantumid
				   FROM group_quantum
				   WHERE arttype=?
				   AND min_antall<=?
				   ORDER BY min_antall
				   DESC LIMIT 1
			   )
		 ", $refid, $regionid, $productgroup, $quantity )->fetchSingle();
	
		 CacheEngine::write( $identifier, $price, 120 );
		 
		 return $price;
		 
	  }
	
	public function saveXml(){
	  
	  if( Login::userid()){
		 $user =  Login::userid();
	  }else{
		 $user = $this->anonymous;
	  }
	  
	  $mediaclip_server = Settings::Get( 'mediaclip', 'server', 'jasmin.repix.no' );
	  $id = $_POST['orderid'];
	  $product_id = $_POST['product_id'];
	  $xtra = $_POST['additionalSheetCount'];
	  $sheetcount = $_POST['sheetCount'];
	
	  try{
		 $project_xml = file_get_contents('http://' . strtolower($mediaclip_server) . '/ECommerceBridge/temp/project_' . $id .'.xml');
		 $project_xml = substr_replace( $project_xml, '', 0, 3 ); 
	  }catch(Exeption $e){
		 throw new Exception( 'Missing xml on disk.' );
	  }
	  
	  $project = new Project($id);
	
	  if($project->user_id == $user){
		 
		 try{
			$project->saveThumb();
		 }catch( Exception $e ){
			//mail( 'tor.inge@eurofoto.no', "Porblem saving thumd mc $product_id" , $e->getMessage() );
		 }
		 
		 $project->projectxml = $project_xml;
		 $project->productid = $product_id;
		 $project->saved = date( 'Y-m-d H:i:s' );
		 $project->xtra = $xtra;
		 $project->sheetcount = $sheetcount;
		 $project->save();
		 
		 
		 /**
		  * TODO create a cleanup script for versions table
		  */
		 $version = new DBUserProjectVersions();
		 $version->project_id = $id;
		 $version->project_xml = $project_xml;
		 $version->save();
		 
	  }
	}
	
	public function projectXml($id){
	  
	  
	  $project = new Project($id);
	  
	  if( Login::userid()){
		 $user =  Login::userid();
	  }else{
		 $user = $this->anonymous;
	  }
	  
	  if($project->user_id == $user){
		 $xml_project = $project->projectxml;
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
		 
		 
		 
		 //hack for å få åpne bugga fotobøke
		 /*if( strpos( $xml_project , 'creationVersion="HTML5' ) ){
			config('website.mediaclipkobling');
			$productkobling  = Settings::Get( 'mediaclip', 'produktkobling' );
   
			$pid = $project->productid;

			foreach ($productkobling as $key => $val) {
				if( is_array( $val )){
					if ($val['art_type'] == $pid) {
						$koblingkey = $key;
						continue;
					}
				}
			}
			
			if(  $koblingkey ){
				if(strrpos($xml_project,'productId="' .  $pid .'"') > 1){
				   $xml_project = str_ireplace('productId="' . $pid . '"',	'productId="' .  $productkobling[$koblingkey]['template'] . '"',	$xml_project);
				}
			}
		 
		 }*/
		 
		 header ( 'content-type: text/xml' );
		 echo $xml_project;
	  }
	  
	}
   
   
	public function Imageinfo($id = null){
		
		//$id = 219673740;
		
		//$uid = str_replace( '.jpg', '', $id );
		
		list( $width, $height ) = DB::query( "SELECT x, y FROM bildeinfo WHERE bid = ?", (int)$id )->fetchRow();
		
		$height =  $height ? $height: 100;
		$width = $width ? $width: 100;
		
		
		$xml = new SimpleXMLElement("<DataChunk></DataChunk>");
		
		$xml->imageInfo['height'] = $height;
		$xml->imageInfo['width'] = $width;
		
		$xml->imageInfo->width = $width;
		$xml->imageInfo->height = $height;
		
		header ( 'content-type: text/xml' );
		echo $xml->asXML();
		
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
}



?>