<?PHP
   
   import( 'website.album' );
   import( 'website.uploadhelper' );
   
   class MyAccountUploadFlash extends UserPage implements IValidatedView {
      
      protected $template = 'myaccount.upload.flash';
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      public function Execute( $aid = 0 ) {
         
         if( $aid > 0 ) {
            try {
               $album = new Album( $aid );
               $this->selectedalbumid = $aid;
               $this->selectedalbum = $album->asArray();
            } catch( Exception $e ) { $this->selectedalbumid = 0; }
         } else $this->selectedalbumid = 0;
         
         $this->batchid = UploadHelper::getBatchId();
         $this->albums = Album::enum( 0, 0, false, false, true );
         
      }

   }
?>