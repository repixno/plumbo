<?PHP

   import( 'reedfoto.page' );
   import( 'math.uuid' );
   
   model( 'reedfoto.correction' );
   
   class ReedFotoAdminUpload extends WebPage implements IView {
      
      protected $template = 'admin/upload';
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'uploadfile' => VALIDATE_STRING,
                  'correctionid' => VALIDATE_INTEGER,
               )            
            )
         );
         
      }
      
     /*
      *
      */
      
      public function Execute( ) {
         
         $correctionid = $_POST['correctionid'];

         $guid = UUID::create();

         $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
         $uploadfolder = Settings::Get( 'reedfoto', 'uploadfolder', 'upload' );
         
         if ( file_exists( $_FILES['uploadfile']['tmp_name'] ) && ( $correctionid > 0 ) ) {
            
            $this->correctionid = $correctionid;
            
            try {
               
               if ( $_FILES['uploadfile']['error'] == 0 ) {
                  
                  if ( move_uploaded_file( $_FILES['uploadfile']['tmp_name'],  sprintf( '%s/%s/%s.pdf', $storagepath, $uploadfolder, $guid ) ) ) {
                     
                     $this->uploadsuccess = true;
                     
                     $this->guid = $guid;
                  
                  } else {
                     
                     $this->uploadfailed = true;
                     $this->uploadmessage = "Failed moving uploaded file";
                     
                  }
                  
               } else {
                  
                  $this->uploadfailed = true;
                  $this->uploadmessage = sprintf( "File was marked with error code %s", $_FILES['uploadfile']['error'] );
                  
               }
               
            } catch (Exception $e) {
               
               $this->uploadfailed = true;
               $this->uploadmessage = sprintf( "Exception occurred while uploading file: %s", $e );
               
            }
           
         } else if ( $_FILES['uploadfile'] ) {
            
            relocate( WebsiteHelper::adminBasePath() );
            
         } else {
            
            relocate( WebsiteHelper::adminBasePath() );
            
         }
         
      }
      
   }
   
?>