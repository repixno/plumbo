<?PHP
   
   import( 'website.uploadhelper' );
   import( 'website.album' );
   import( 'math.signer' );
   
   class OrderPrintsTransferPhotos extends UserPage implements IView {
      
      protected $template = 'order-prints.transfer-photos-new';
      
      public function Execute( $aid = 0 ) {
         
          if( UploadHelper::iOsDetection() ){
            
            $rootUrl = WebsiteHelper::rootBaseUrl();  
            $redirecturl = sprintf( "/order-prints/fromalbum/%s", 'inbox' );
            $redirecturl = $rootUrl . $redirecturl;
            
            $random =  Signer::createSignature();
            CacheEngine::write( $random[0] .'aid', $uploadid );
            CacheEngine::write( $random[0] .'uid', Login::userid() );
            
            if( strpos( $rootUrl, 'eurofoto.no' ) ){
               $uploadurl = sprintf( "http://www.eurofoto.no/upload/iphone/%s" , $random[0] );
               $returnurl = "http://www.eurofoto.no/myaccount";
               $licensekey = "79FF1-00026-6D060-0004B-0E9CA-4DD9B2";
            }
            else if( strpos( $rootUrl, 'vg.no' )){
               $uploadurl = sprintf( "http://foto.vg.no/upload/iphone/%s" , $random[0] );
               $returnurl = "http://foto.vg.no/myaccount";
               $licensekey = "79FF1-00078-6ABE0-000AB-0E1C8-1DD90B";
            }

             $this->appurl =  sprintf( 'aurup:?uploadUrl=%s&redirectUrl=%s&returnUrl=%s&licenseKey=%s&', $uploadurl , $redirecturl, $returnurl, $licensekey );
              
         }
         
         if( $aid > 0 ) {
            try {
               $album = new Album( $aid );
               $this->selectedalbumid = $aid;
               $this->selectedalbum = $album->asArray();
            } catch( Exception $e ) {}
         }
         
         $this->batchid = UploadHelper::getBatchId();
         $this->albums = Album::enum( 0, 0, false, false, true );
         
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
            
         } else {
            $redirecturl = '/order-prints/choose-quantity/';
         }
         
         if ( $redirecturl) {
            
            $this->redirecturl = $redirecturl;
            
         }
         
         $this->albums = Album::enum( 0, 0, false, false, true );
         $this->selectedalbumid = (int) $_SESSION['upload_aid'];

      }
      
   }
   
?>