<?PHP

   import( 'website.uploadhelper' );
   import( 'website.album' );
   
   class FlashUploader extends UserPage implements IView {
      
      protected $template = 'order-prints.transfer-photos-flash';
      
      public function Execute() {

         if( $aid > 0 ) {
            try {
               $album = new Album( $aid );
               $this->selectedalbumid = $aid;
               $this->selectedalbum = $album->asArray();
            } catch( Exception $e ) {}
         }
         
         $this->batchid = UploadHelper::getBatchId();
         $this->albums = Album::enum( 0, 0, false, false, true );
         
         $uploadreturnurl = Session::pipe( 'uploadreturnurl', null, false, true );
         
         if( $uploadreturnurl ) {
            
            $redirecturl = $uploadreturnurl;
            
         } else {
            $redirecturl = '/order-prints/choose-quantity/';
         }
         
         $this->redirecturl = $redirecturl;
         
      }
      
   }
   
?>