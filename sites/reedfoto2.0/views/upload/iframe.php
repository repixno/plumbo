<?PHP
   
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.uploadedimagesarray' );
   
   class UploadIFrame extends WebPage implements IValidatedView {
      
      protected $template = 'upload.iframe';
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'callback' => VALIDATE_STRING,
               ),
               'files' => array(
                  'image' => array(
                     'tmp_name' => VALIDATE_STRING,
                     'error' => VALIDATE_INTEGER,
                     'type' => VALIDATE_STRING,
                     'name' => VALIDATE_STRING,
                  ),
               ),
            ),
         );
         
      }
      
      public function Execute( $albumid = null, $callback = 'uploadcallback' ) {
         
         $this->callback = $callback;
         $this->albumid = $albumid;
         
         if( count( $_FILES ) ) {
            
            try {
               
               if( $albumid > 0 ) {
                  
                  $album = new Album( $albumid );
                  if( $album->uid != Login::userid() ) {
                     throw new SecurityException( 'Permission Denied', 403 );
                  }
                  
               }
               
               if( is_uploaded_file( $_FILES['image']['tmp_name'] ) && !$_FILES['image']['error'] ) {
                  
                  $imageid = StorageUtil::uploadImage(
                     Login::isLoggedIn() ? Login::userid() : 61224,
                     $albumid ? $albumid : null,
                     $_FILES['image']['tmp_name'],
                     $_FILES['image']['type'],
                     $_FILES['image']['name']
                  );
                  
                  if( $imageid ) {
                     
                     // store it in the template
                     $this->imageid = $imageid;
                     
                     // instanciate the image
                     $image = new Image( $imageid );
                     $array = $image->asArray();
                     
                     // store image info in the template
                     $this->image = $array;
                     $this->imagejson = json_encode( $array );
                     
                     // add the image to the array of images
                     UploadedImagesArray::add( $imageid );
                     
                  } else {
                     
                     // something went terribly wrong :(
                     throw new Exception( 'Upload failed' );
                     
                  }
                  
               }
               
            } catch( Exception $e ) {
               
               $this->failed = true;
               
            }
            
         }
         
      }
      
   }
   
?>