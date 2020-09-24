<?PHP
   
   import( 'pages.admin' );
   import( 'website.menu' );
   
   class ContentMenuUpload extends AdminPage implements IView {
      
      protected $template = 'content.menuimageupload';
      
      public function Execute( $menuid = 0 , $code = '') {
         
         $this->menuid = $menuid;
         
         if( $menuid > 0 && isset( $_FILES['image'] ) && !$_FILES['image']['error'] ) {
            
            $item = new MenuItem( $menuid );
            
            if( $item->isLoaded() && is_uploaded_file( $_FILES['image']['tmp_name'] ) ) {
               
               $oldfile = $item->image ? sprintf( '%s/data/images/menuimages/%s', getRootPath(), $item->image ) : '';
               
               $uuid = UUID::create();
               $efilename = explode( '.', $_FILES['image']['name'] );
               $extension = strtolower( end( $efilename ) );
               $image = sprintf( '%s.%s', $uuid, $extension );
               
               $filename = sprintf( '%s/data/images/menuimages/%s', getRootPath(), $image );
               
               move_uploaded_file( $_FILES['image']['tmp_name'], $filename );
               
               $this->imageuuid = $image;
               
               $item->image = $image;
               $item->save();
               
               /*
               if( $oldfile && file_exists( $oldfile ) && is_file( $oldfile ) ) {
                  unlink( $oldfile );
               }
               */
               
            }
            
         }
         
      }
      
      
     public function language() {
        
        $this->template = null;
         
         if( isset( $_FILES['image'] ) && !$_FILES['image']['error'] && $_POST['filelang'] ) {
            
            //$item = new MenuItem( $menuid );
            
            if( is_uploaded_file( $_FILES['image']['tmp_name'] ) ) {
               
               $language = $_POST['filelang'];
               $filename = $_POST['filename'];
               
               $filefolder = sprintf( '%s/data/images/menuimages/%s/', getRootPath(), $language );
               
               $menuid   = explode( '/', $_POST['menuid'] );
               $efilename = explode( '.', $_FILES['image']['name'] );
               $extension = strtolower( end( $efilename ) );
               
               
               $item = new MenuItem( $menuid[3] );
               $uuid = UUID::create();
               $newfilename  = $uuid . "." . $extension;
               
               util::Debug( $newfilename );
               
               $languages = array( '', '/nb_NO', '/en_US' , '/de_DE', '/sv_SE' );
               
               foreach ( $languages as $langid ){
                  
                  if( file_exists( sprintf( '%s/data/images/menuimages%s/%s', getRootPath() , $langid, $filename ) ) ){
                     
                     rename( sprintf( '%s/data/images/menuimages%s/%s', getRootPath() , $langid, $filename ), sprintf( '%s/data/images/menuimages%s/%s', getRootPath() , $langid, $newfilename ) );
                     util::Debug( "rename $filename to $newfilename ");
                     
                  }
                  
               }
               
               
               util::Debug( $filefolder ); 
               
               if( !file_exists( $filefolder ) ){
                  mkdir($filefolder ,0777); 
                  util::Debug( "Do not exist" .  $filefolder );  
               }

               $filename = $filefolder . $newfilename;
               
               if( file_exists( $filename )  && !is_dir( $filename ) ){
                     unlink( $filename );
               }
                              
               util::Debug( $filename );

               move_uploaded_file( $_FILES['image']['tmp_name'], $filename );
              
               $item->image = $newfilename;
               $item->save(); 
               
               
               relocate( '/menu' );
               
               
               /*
               if( $oldfile && file_exists( $oldfile ) && is_file( $oldfile ) ) {
                  unlink( $oldfile );
               }
               */
               
            }
            
         }
         
      }
      
   }
   
?>