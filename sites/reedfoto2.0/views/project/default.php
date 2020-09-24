<?PHP

   import( 'reedfoto.projectparticipants' );
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   
   
   
   class reedfotoProject extends WebPage implements IView {
      
      private $securecode = '2f1f4567bad3ce1cfa';
      
      
      //protected $template = 'project.prelist';
      protected $template = 'project.listimage';
      
      
      public function Execute(){
         
         
         $string = $_GET['q'];
         
         if( !empty($string) ){
            
         $res = ProjectParticipants::find( $string );
         $image = array();
         $splitnamearray = array(
                  'split1' => '35 km',
                  'split4' => 'Mål'
         );
         foreach ( $res as $part ){
            
            PermissionManager::current()->grantAccessTo( $part['bid'], 'image', PERMISSION_SHARED );
            
            $tmpimage = new Image( $part['bid'] );
            
            $securecode = base64_encode( md5(  $part['bid'] . $this->securecode ) . '_' . $part['bid']  );

            $key = $splitnamearray[$part['splitname']];
            
            $image[$key][] = array(
               'image' => $tmpimage->asArray(),
               'securecode' => $securecode,
               'splitname'      => $part['splitname']
               );
         }
         $this->searchresult = array(
               'q'   => $string,
               'images' => $image
         );
         }else{
            
         }
         $this->searchstring = $string;
         //util::Debug( $image );
      }
      
      
      public function OrderImage( $check ){
         
         //$check = base64_encode( md5(  $imageid . $this->securecode ) . '_' . $imageid  );
         $check_access = base64_decode( $check );

         list( $sec , $imageid ) = explode( '_' , $check_access );
         $owner = DB::query( "SELECT owner_uid FROM bildeinfo where bid = ?;", $imageid )->fetchSingle();
         
         if( $sec == md5(  $imageid . $this->securecode ) ){
            $this->template = 'project.order';
            $product = array();
            $productarray = array( 2758, 2750, 2752, 2754, 2756 );
            
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
      
      
      
     public function FindNames(){
        $this->template = null;
        $term = isset( $_GET['term'] ) ? $_GET['term'] : null;
        $res = ProjectParticipants::findParticipants( $term );
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