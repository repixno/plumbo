<?PHP
   
   import( 'storage.util' );
   import( 'website.image' );

  // class UploadIFrame extends WebPage implements IValidatedView {
   class UploadIPhone extends WebPage implements IView {
      
      public function Execute( $securetoken = null) {
         
         if( $securetoken ){ 
            $albumid = CacheEngine::read( $securetoken . 'aid' );
            $userid = CacheEngine::read( $securetoken . 'uid' );  
         }         
         
         $fileCount = $_POST["PackageFileCount"];
         
         for ($i = 0; $i < $fileCount; $i++) {
         
            $param = "File0_" . $i;
            
            if ( isset( $_FILES[$param] ) ) {
            
               try {
                  

                  // did we receive a file correctly?
                  if( count( $_FILES ) && isset( $_FILES[$param] ) ) {
                  
                     try {
                        
                        if( is_uploaded_file( $_FILES[$param]['tmp_name'] ) && !$_FILES[$param]['error'] ) {
                        
                        $imageid = StorageUtil::uploadImage(
                           $userid,
                           $albumid ? $albumid : null,
                           $_FILES[$param]['tmp_name'],
                           $_FILES[$param]['type'],
                           $_POST['SourceName_' . $i]
                        );
                        
                        if( $imageid ) {
                        
                           // render the imageid
                           header("HTTP/1.0 200 OK");
                           
                           // draw the imageid
                           echo $imageid;
                        
                        } else {
                        
                           // something went terribly wrong :(
                           throw new Exception( 'Upload failed' );
                        }
                        
                        }
                     
                     } catch( Exception $e ) {
                     
                        header("HTTP/1.0 500 Internal Server error");
                        exit( 0 );
                     
                     }
                  
                  }
               
               } catch( Exception $e ) {
               
                  throw new Exception( $e->getMessage(), $e->getCode() );
               
               }
               
            }
         
         }
      
      }
      
   }
   
?>