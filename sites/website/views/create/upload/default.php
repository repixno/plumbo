<?PHP

   import( 'website.album' );
   import( 'website.uploadhelper' );
   
   class CreateUpload extends WebPage implements IView {
      
      protected $template = 'create.upload';
      
      public function Execute() {

         // for flash         
         $this->batchid = UploadHelper::getBatchId();
         $this->albums = Album::enum( 0, 0, false, false, true );

         // for jajva
         $this->uploadaid = $_POST['uploadid'];
         
         $redirecturl = '';
         
         if( isset( $_POST['uploadid'] ) ) {
            
            try {
               
               $album = new Album( $_POST['uploadid'] );
               if( $album->uid == Login::userid() ) {
                  
                  $_SESSION['upload_aid'] = $album->aid;
                  $redirecturl = $album->albumurl;
                  
               }
               
            } catch( Exception $e ) {}
            
         } elseif( $_SESSION['upload_aid'] > 0 ) {
            
            try {
               
               $album = new Album( $_POST['uploadid'] );
               if( $album->uid == Login::userid() ) {
                  
                  $redirecturl = $album->albumurl;
                  
               }
               
            } catch( Exception $e ) {}
            
         } else {
            
            if( Login::isLoggedIn() ) {
               
               $redirecturl = '/myaccount/album/0/'.util::urlize( __( 'Inbox' ) );
               
            }
            
         }
         
         $uploadreturnurl = Session::pipe( 'uploadreturnurl', null, false, true );
         
         if( $uploadreturnurl ) {
            
            $redirecturl = $uploadreturnurl;
            
         }
         
         if ( ( $reditecturl <> '/create//edit/0/0') && ( $redirecturl ) ) {
               
            $this->redirecturl = $redirecturl;

         }
         
         $this->albums = Album::enum( 0, 0, false, false, true );
         $this->selected = (int) $_SESSION['upload_aid'];
         $this->selectedalbumid = (int) $_SESSION['upload_aid'];
         
      }
      
   }
   
?>