<?PHP
model( 'user.projectorder' );

   class ProjectOrder extends DBUserProjectOrder {
     
      public function usedImages(){

            $origProjectXML =  $this->xml;
            
            if( strpos( $origProjectXML, "collage:photoElement") > 1 || strpos( $origProjectXML, "collage:backgroundElement") > 1 || strpos( $origProjectXML, "model:photo") > 1){
               $tmpXML = preg_replace("/[\n]/", "", $origProjectXML);
               $tmpXML = preg_replace("/\t\t+/", "", $tmpXML);
               // Hack to fix non-standard namespace definitions from Mediaclip.
               $tmpXML = preg_replace( "/(xmlns(?::\w+?)*\=\")(.+?)(\")/", '${1}http://www.mediaclip.com/${2}${3}', $tmpXML );
               $tmpXML = utf8_encode( $tmpXML );
               
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
                     $imagepatharray = explode( '\\', $resFileName );
                     // Get base file name.
                     $fullname = end( $imagepatharray );
                     //adds image id to array
                     $imageId = (int)basename( $fullname, ".jpg" );
                     if($imageId > 0 && !in_array( $imageId, $image_test )){
                        $image_test[] = $imageId;
                        $imageIds[] = $imageId;
                     }
                  }
                  }
               }
            
            }
         
      return  serialize($imageIds);	
   }
   
   static function readyOrders(){
      
      
      return DB::query( "SELECT * FROM mediaclip_orders WHERE processed IS NULL AND order_id IS NOT NULL" )->fetchAll( DB::FETCH_ASSOC );
      
      
      
      
   }
  
  
  
 }



?>
