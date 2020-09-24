<?PHP
   
   import( 'website.album' );
   import( 'website.uploadhelper' );
   import( 'math.signer' );
   
   import( 'cewe.default' );
   
   class ceweImageUpload extends UserPage implements IView {
      
      protected $template = 'myaccount.cewealbum.upload';
      
      public function Execute( $uploadid = '' ) {
                           
         $this->uploadaid = $_POST['uploadid'];
         $api = new ceweApi();
         
         $this->credentials = $api->credetials();
         
         $redirecturl = '';
         
         if( isset( $_POST['uploadid'] ) ) {
            
            try {
               
               $album = new Album( $_POST['uploadid'] );
               if( $album->uid == Login::userid() ) {
                  
                  $_SESSION['upload_aid'] = $album->aid;
                  $redirecturl = $album->albumurl;
                  
               }
               
            } catch( Exception $e ) {}
            
         } else if( isset( $uploadid ) ) {
            
            try {
               
               $album = new Album( $uploadid );
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
         
         if( $redirecturl ) {
            
            $this->redirecturl = $redirecturl;
            
         }
         
         
         $cewealbums = $api->getApi( '/photoAlbums' );
		 
		 foreach( $cewealbums as $cewealbum ){
			$albumlist[] = $api->ceweAlbumArray( $cewealbum );
		 }
         
         $this->albums = $albumlist;
         
         $this->selectedalbumid = (int) $_SESSION['upload_aid'];
         
      }
      
   }
   
?>