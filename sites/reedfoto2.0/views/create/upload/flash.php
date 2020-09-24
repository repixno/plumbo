<?PHP
   
   import( 'website.album' );
   import( 'website.project' );
   import( 'website.uploadhelper' );
   
   class CreateUploadFlash extends WebPage implements IView {
      
      protected $template = 'create.uploadflash';
      
      public function Execute() {
         
         $this->batchid = UploadHelper::getBatchId();
         $this->albums = Album::enum( 0, 0, false, false, true );

         $uploadreturnurl = Session::pipe( 'uploadreturnurl', null, false, true );      
         
         Session::pipe( 'uploadreturnurl', $returnurl, null, false, true  );
         
         if( $uploadreturnurl ) {
            
            $redirecturl = $uploadreturnurl;
            
         }
         
         if( $redirecturl ) {
            
            $this->redirecturl = $redirecturl;
            
         }
         
      }
      
   }

?>