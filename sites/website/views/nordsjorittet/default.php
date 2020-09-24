<?PHP

   //import( 'reedfoto.projectparticipants' );
   import( 'reedfoto.projectparticipants_new' );
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   
   
   
   class reedfotoProject extends WebPage implements IView {
      
      private $securecode = '2f1f4567bad3ce1cfa';
      
      //protected $template = 'project.prelist';
      protected $template = 'project.listimage';
      
      public function Execute( $year = null ){

         if( $year == 2012){            
            $year = 2012;
            $projectid = 1;
            $topbanner = 'http://b.static.eurofoto.no/cms/images/nordsjorittet2.jpg';
            
         }else if( $year == 2013){
            $projectid = 2;
            $topbanner = 'http://a.static.eurofoto.no/cms/images/finn-ditt-bilde-2013_(2).jpg';
         }else{
            relocate( '/nordsjorittet/2013' );
         }

         $string = $_GET['q'];
         $project = $_GET['projectid'];
         
         if( !empty($string) ){
            
            $image = ProjectParticipantsNew::find( $string, $projectid );
            
            $this->searchresult = array(
               'q'   => $string,
               'images' => $image
            );
         }else{
            
         }
         $this->searchstring = $string;
         $this->year = $year;
         $this->projectid = $projectid;
         $this->topbanner = $topbanner;

      }
      
      
      public function OrderImage( $year  = null, $check = null ){
         
         //$check = base64_encode( md5(  $imageid . $this->securecode ) . '_' . $imageid  );
         
         if( !$year || !$check ){
            relocate( '/nordsjorittet/2013'  );
            die();
            
         }
         $check_access = base64_decode( $check );

         list( $sec , $imageid ) = explode( '_' , $check_access );
         $owner = DB::query( "SELECT owner_uid FROM bildeinfo where bid = ?;", $imageid )->fetchSingle();
         
         if( $sec == md5(  $imageid . $this->securecode ) ){
            $this->template = 'project.order';
            $product = array();
            
            if( $year == 2012 ){
               $productarray = array( 2758, 2750, 2752, 2754, 2756 );
               $topbanner = 'http://b.static.eurofoto.no/cms/images/nordsjorittet2.jpg';
            }
            else if( $year == 2013 ){
               $productarray = array( 3290, 3292, 3296, 3298, 3294, 3300 );
               $topbanner = 'http://c.static.eurofoto.no/cms/images/finn-ditt-bilde-2013.jpg';
            }else{
               relocate( '/nordsjorittet/2013'  );
               die();
            }
            
            
            foreach ( $productarray as $ret ){
               $productcontainer = new Product( $ret );
               
               $product[] = $productcontainer->asArray();
            }
            
            //util::Debug( $product->asArray() );
            PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_SHARED );
            
            $aid = DB::query( "SELECT aid FROM bildeinfo where bid = ?;", $imageid )->fetchSingle();
            
            PermissionManager::current()->grantAccessTo( $aid, 'album', PERMISSION_SHARED );
            
            $image = new Image($imageid);
            
            //util::Debug( $aid );
            
            $this->image = $image->asArray();
            $this->imageid = $imageid;
            $this->topbanner = $topbanner;
            $this->sections = array(
                     'products' => $product
            );
         }else{
            //relocate( '/frontpage');
         }
      }
      
      
      
     public function AddToCart() {
        
        $this->template = null; 
        
         $prodno = isset( $_POST['prodno'] ) ? $_POST['prodno'] : null;
         $imageid = isset( $_POST['imageid'] ) ? (int) $_POST['imageid'] : null;
         $quantity = isset( $_POST['quantity'] ) ? (int) $_POST['quantity'] : 1;
         $x1 = isset( $_POST['x1'] ) ? (int) $_POST['x1'] : 0;
         $y1 = isset( $_POST['y1'] ) ? (int) $_POST['y1'] : 0;
         $width = isset( $_POST['width'] ) ? (int) $_POST['width'] : 0;
         $height = isset( $_POST['height'] ) ? (int) $_POST['height'] : 0;
         
         $debug = serialize( $_POST  );
         
         //file_put_contents( "/home/toringe/project/cart.txt", "\n******DEBUG******\n" . date( 'Y-m-d H:i:s') . "\n" . $debug . "\n*****************\n" );
         
         $this->result = false;
         $this->message = 'Missing prodno';
         if( !isset( $prodno ) ) return false;
         
         $this->result = false;
         $this->message = 'Missing imageid';
         if( !isset( $imageid ) ) return false;
         
         $this->result = false;
         $this->message = 'Missing quantity';
         if( !isset( $quantity ) ) return false;
         
         try {
            
            $image = new Image( $imageid );
            
         } catch ( Exception $e ) {
            
            $this->result = true;
            $this->message = 'No image access';
            return false;
            
         }
         
         $cart = new Cart();
         $this->result = false;
         $this->message = 'Could not initialize cart';
         if( !$cart instanceof Cart ) return false;
         
         $attributes = array( 
            'images' => array( 
                  $imageid => $quantity,
                  'crop'   => array(
                        'x1' => $x1,
                        'y1' => $y1,
                        'height' => $height,
                        'width' => $width
                     )
                  )
               );
 
         $cart->addItem( $prodno, $quantity, $attributes );
         $cart->save();
         $this->result = true;
         $this->message = 'OK';
         
         $return['error'] = null;
         $return['msg'] = $this->message;
         echo json_encode( $return );

         return true;
         
      }
      
      
      
     public function FindNames( $id  = null ){
         
         if( !$id ){
            $id = 1;
         }
      
        $this->template = null;
        $term = isset( $_GET['term'] ) ? $_GET['term'] : null;
        $res = ProjectParticipantsNew::findParticipants( $term, $id );
        $ret = array();
        
        foreach ( $res as $test ){
           $ret[] = array(
               'id' => $test['startno'],
               'label' => $test['name'] . ', startnr: ' . $test['startno'],
               'value' => $test['name'] . ', startnr: ' . $test['startno']
           );
        }
        
        echo json_encode( $ret );
        
     }
            
   }


?>