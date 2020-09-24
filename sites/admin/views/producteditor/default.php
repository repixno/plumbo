<?PHP
   
   import( 'pages.admin' );
   import( 'website.product' );
   import( 'website.article' );
   
   model( 'producteditor.category' );
   model( 'producteditor.assets' );
   
   class AdminProducteditorDefault extends AdminPage implements IView {
      
      protected $template = 'producteditor.default';
      private $assetpath = "/data/global/producteditor/";
      
      public function Execute( ) {
         
         
      }
      
      public function Categories(){
         $this->template = 'producteditor.categories';
         if( $_POST ){
            $newcat = new DBproducteditorCategory();
            $newcat->title = $_POST['title'];
            $newcat->type = $_POST['type'];
            $newcat->save();
            relocate('/producteditor/categories');  
         }
         $categories = DB::query( "SELECT * FROM producteditor_category")->fetchAll( DB::FETCH_ASSOC );
         $this->categories = $categories;
      }
      
      public function Backgrounds( $category = null ){
         $backgrounds = array();
         $categories = DB::query( "SELECT * FROM producteditor_category")->fetchAll( DB::FETCH_ASSOC );
         $this->template = 'producteditor.backgrounds';
         if( $_POST && $_FILES ){
            $this->saveasset( 'backgrounds' , $_POST, $_FILES );
            relocate('/producteditor/backgrounds');
         }
         $backgrounds = DB::query( "SELECT * FROM producteditor_assets WHERE type =? AND category = ?" ,'backgrounds', $category['id'] )->fetchAll( DB::FETCH_ASSOC);
         $this->assets = $backgrounds;
         $this->categories = $categories;
      }
      
      public function Cliparts( $category = null ){
         $cliparts = array();
         $categories = DB::query( "SELECT * FROM producteditor_category")->fetchAll( DB::FETCH_ASSOC );
         $this->template = 'producteditor.clipart';
         if( $_POST && $_FILES ){
            $this->saveasset( 'clipart' , $_POST, $_FILES );
            relocate('/producteditor/cliparts');
         }
         $cliparts = DB::query( "SELECT * FROM producteditor_assets WHERE type =? AND category = ?" ,'clipart', $category['id'] )->fetchAll( DB::FETCH_ASSOC);
         $this->assets = $cliparts;
         $this->categories = $categories;
      }
      
      
      private function saveasset( $category, $post, $file ){
         $tmpname = $file['image']['tmp_name'];
         $name = md5_file (  $file['image']['tmp_name'] );
         $ext = pathinfo( $file['image']['name'] , PATHINFO_EXTENSION );
         $filename =   $name . '.' . $ext;
         $printfile = $this->assetpath  . 'print/' . $category . '/' . $filename ;
         move_uploaded_file( $tmpname, $printfile );
         
         $newasset = new DBproducteditorAssets();
         $newasset->filename = $filename;
         $newasset->type = $category;
         $newasset->title = $post['title'];
         $newasset->description = $post['description'];
         $newasset->category = $post['category'];
         
         $webfile = $this->assetpath  . 'web/' . $category . '/' . $filename ;
         $webthumb = $this->assetpath  . 'web/' . $category . '/thumb/' . $filename ;
         $thumb = new Imagick();
         $thumb->readImage( $printfile );
         
         $newasset->width = $thumb->getImageWidth();
         $newasset->height = $thumb->getImageHeight();
         
         
         if(  $newasset->width < 1024 || $newasset->height < 1024 ){
            $webx =  $newasset->width;
            $weby = $newasset->height;
         }
         else{
            $webx =  1024;
            $weby = 1024;
         }
         
         $thumb->resizeImage($webx,$weby,Imagick::FILTER_LANCZOS,1,true);
         $thumb->writeImage($webfile);
         $thumb->resizeImage(150,150,Imagick::FILTER_LANCZOS,1,true);
         $thumb->writeImage($webthumb);
         $thumb->clear();
         $thumb->destroy();
         
         $newasset->created = date( 'Y-d-m H:i:s' );
         $newasset->save();
      }
      
   }
   
?>