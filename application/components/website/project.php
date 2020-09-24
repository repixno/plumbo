<?php

   model( 'user.project' );
   model( 'user.projectunique' );
   model( 'user.projectpredefined' );
    model('user.projectversions');

   class Project extends DBUserProject {
	  

      public function asArray() {

         $imageList = array();
	      foreach ( $this->listImages( 3 ) as $image ) {

	         $imageList[] = $image->asArray();

	      }

	      $type = $this->type == null ? 'Photobook' : $this->type;

	      return array(
	         'id' => $this->id,
	         'title' => $this->title,
	         'description' => $this->description,
	         'url' => '/create/' . $type . '/edit/' . $this->id,
	         'isShared' => $this->share ? true : false,
	         'shareurl' => $this->shareurl,
	         'date' => $this->saved,
	         'createddate' => $this->created,
	         'images' => $imageList,
	         'product' => $this->getProductArray(),
	         'type' => $type,
	         'sheetcount' => $this->sheetcount,
	         'extrapages' => $this->extrapages
         );

      }

      public function listImages( $limit = 0 ) {

         $images = array();
         $imIds = array();

         $imCount = 0;
         if ( preg_match_all( '/\\\ready\\\(\d+)\\\(\d+)\.\w+/', $this->projectxml, $matches ) ) {

            foreach ( $matches[ 2 ] as $match ) {

               if ( $limit && $imCount++ >= $limit ) {

                  break;

               }

               if ( !in_array( $match, $imIds ) ) {

                  $imIds[] = $match;

                  try{

                     $images[] = new Image( $match );

                  } catch( Exception $e ) {}

               }

            }

         }

         return $images;

      }

      public function share() {

         $uniqeId = uniqid();

         if ( !$this->share ) {

            $shareUrl = sprintf( "%s/shared/project/%s", WebsiteHelper::rootBaseUrl(), $uniqeId );

            $this->share = 1;
            $this->share_id = $uniqeId;
            $this->save();

            return array( $shareUrl, $uniqeId );

         } else {

            return array( false );

         }

      }

      public function unshare() {

         if ( $this->share ) {

            $this->share = 0;
            $this->share_id = '';

         }

         return true;

      }

      public function getShareUrl() {

         if ( $this->share ) {

            return sprintf( "%s/shared/project/%s", WebsiteHelper::rootBaseUrl(), $this->share_id );

         }

         return false;

      }

      public function duplicate($uid){

         $duplicated_project = new project();

         $duplicated_project->userid=$this->userid;
         $duplicated_project->title=$this->title;
         $duplicated_project->description=$this->description;
         $duplicated_project->share=$this->share;
         $duplicated_project->type=$this->type;
         $duplicated_project->projectxml=$this->projectxml;

         return $duplicated_project->save();

      }

      static function fromShareId( $shareid ) {

         try {

            return Project::fromFieldValue(
               array(
                  'share_id' => $shareid
               ),
               'Project'
            );

         } catch ( Exception $e ) {

            return false;

         }

      }

      public function getUnique() {

         try {

            return DBUserProjectUnique::fromFieldValue(
               array(
                  'project' => $this->id
               ),
               'DBUserProjectUnique'
            );

         } catch ( Exception $e ) {

            return false;

         }

      }
      
      public function usedImages(){  

            $origProjectXML =  $this->projectxml;
            
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
      
      static function productInfo($productid, $type="artnr" ){
         
         // Valid product colors
         $colors = array(
            "0"   => "black",
            "1"   => "white",
            "2"   => "blue",
            "3"   => "red",
            "4"   => "chalk",
         );
         
         $length  = strlen( $productid );
         
         if( $type == 'artnr' ){
            $artinfo = substr( $productid, 0, $length-3 );
         }
         else if ( $type == 'keyhole' ){
            $artinfo = substr( $productid, $length-2, 1 );
         }
         else if ( $type == 'color' ){
            $artinfo = substr( $productid, $length-1, 1 );
            $artinfo = $colors[$artinfo];
         }
         
         return $artinfo;
         
      }
      
      public function predefinedAid(){
         
         try{
            $ref = DBUserProjectPredefined::fromFieldValue(
               array(
                  'projectid' => $this->predefinert
               ),
               'DBUserProjectPredefined'
            );
         }catch (Exception  $e){
            return false;
         }
         return $ref->aid;
      }
      
      static function predefinedAlbumCheck($aid){
         
         try{
         foreach(DB::query( "SELECT uid FROM bildealbum WHERE aid = ?;", $aid )->fetchAll() as $test){
            if($test[0] == 642124){
               return true;
            }
           } 
 
         }catch( Exception $e ) {}
      }
      
      public function predefinedFromHash($hash){
         
         $predefid = DBUserProjectPredefined::fromFieldValue( 
                     array( 'projecthash' => $hash),
                      'DBUserProjectPredefined' 
                      );
                      
         return $predefid;
         
      }
      
      public function duplicatePredef($uid){
      
         $duplicated_project = new project();
         
         $duplicated_project->userid=$uid;
         $duplicated_project->title=$this->title;
         $duplicated_project->description=$this->description;
         $duplicated_project->share=$this->share;
         $duplicated_project->type=$this->type;
         $duplicated_project->projectxml=$this->projectxml;
         $duplicated_project->predefinert=$this->id;
         $duplicated_project->predefined_project_id=$this->id;
	 $duplicated_project->productid=$this->productid;
         
         $duplicated_project->save();
         
         return $duplicated_project->id;
      
      }
      
      public function saveThumb(){
         
         $mediaclip_server = Settings::Get( 'mediaclip', 'server', 'jasmin.eurofoto.no' );
		 
		 $mediaclipfolder = Settings::Get( 'mediaclip', 'folder', 'ECommerceBridge' );  
		 
         $thumbname = md5($this->id);
         $dir = sprintf( '/data/pd/ef28/mediaclip/thumbs/%s/', date("Y-m-d", strtotime($this->created)));
         
         if(!file_exists($dir)){
            mkdir($dir);
         }
         
         try{
            	$url = 'http://' . strtolower($mediaclip_server) . '/' . $mediaclipfolder . '/cart/' . $thumbname .'.jpg';
            
		$testheader = get_headers($url);
		
		if( strpos(  $testheader[0], '404'  ) ){
		  return false;		
		}

		file_put_contents($dir . $thumbname . ".jpg", file_get_contents($url));
         }catch(Exeption $e){
            //throw new Exception( 'Missing thumb on disk.' );
            //mail('tor.inge@eurofoto.no', "mediaclip bughunt", "feil med lagring av thumb. " . $this->id . " " . $mediaclip_server );
         }
      
      }
   }

?>
