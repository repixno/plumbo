<?PHP
   import( 'session.usersessionarray' );
   import( 'website.cart' );
   import( 'website.order.merkelapporder' );
   
   class BestillingMerkelappIndex extends WebPage implements IView {
      
      protected $template = 'bestilling.index';
      private $orderfolder = '/data/global/merkelapp/';
      
      public function Execute() {

         $project = new UserMerkelappOrder();
         $project->date = date( 'Y-m-d H:i:s');
         
         if( Login::isLoggedIn() ){
            
            $project->userid = Login::userid();
            
         }
         
         $project->save();
         $selected = array();
         
         $productoptionids = array( 2734, 2732, 2730 );
         
         foreach ( $productoptionids as $ret ){
            
            $productoption = new ProductOption( $ret ); 
            $product = new Product( $productoption->productid );
            $selected[] = array(
               'productoptionid' => $ret,
               'productoption' => $productoption->asArray(),
               'productid' => $productoption->productid,
               'product' => $product->asArray()
            );  
         } 
         $this->projectid = $project->id;
         $this->selected = $selected;
        
      }
      
      /**
      * Add product and quantity to cart
      *
      * @param integer $productoptionid
      * @param integer $quantity
      * @param array $attributes
      */
      public function addItemByProductOptionId( $productoptionid = 0, $attributes = 0, $path = '') {
         
         
         if( $path == "fargelapp" ){
            $path = "/bestilling/" . $path;
         }
         else if( $path == 'stempel'){
            $path =  "/bestilling/" . $path;
         }
         else{
            $path = "/bestilling";
         }
         
         if( isset( $_REQUEST['productoptionid'] ) ) $productoptionid = $_REQUEST['productoptionid'];
         if( isset( $_REQUEST['quantity'] ) ) $quantity = $_REQUEST['quantity'];
         if( isset( $_REQUEST['attributes'] ) ) $attributes = $_REQUEST['attributes'];
         
         $quantity = 1;
         
         $attributes = array(
               'projectid' => $attributes
         
         );
         
         $cart = new Cart();
         $cart->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
         $cart->save();
         
         relocate( $path );
         
      }
      
      public function addItemByProductOptionIdAPI() {
         
         $this->template = null;
         
         if( isset( $_POST['productoptionid'] ) ) $productoptionid = (int)$_POST['productoptionid'];
         if( isset( $_POST['quantity'] ) ) $quantity = (int)$_POST['quantity'];
         if( isset( $_POST['projectid'] ) ) $attributes = (int)$_POST['projectid'];
         
         $quantity = 1;
         $attributes = array(
               'projectid' => $attributes
         
         );
         
         $cart = new Cart();
         $cart->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
         $cart->save();
         
         
      }
      
            
      /**
      * Add product and quantity to cart
      *
      * @param integer $productoptionid
      * @param integer $quantity
      * @param array $attributes
      */
      public function Rebuy( $productoptionid = 0, $attributes = 0) {
         
         if( isset( $_REQUEST['productoptionid'] ) ) $productoptionid = $_REQUEST['productoptionid'];
         if( isset( $_REQUEST['quantity'] ) ) $quantity = $_REQUEST['quantity'];
         if( isset( $_REQUEST['attributes'] ) ) $attributes = $_REQUEST['attributes'];
         
         $project = new UserMerkelappOrder( $attributes );
         $newProject = new UserMerkelappOrder();
         $date = date( 'Y-m-d',  strtotime( $project->date ) );
         $newdate = date( 'Y-m-d' );
         $filesrc = $this->orderfolder . $date . '/' . $project->id . '.png';
         
         $newProject->userid = $project->userid;
         $newProject->articleid = $productoptionid;
         $newProject->userid = $project->userid;
         $newProject->line1 = $project->line1;
         $newProject->line2 = $project->line2;
         $newProject->line3 = $project->line3;
         $newProject->clipart = $project->clipart;
         $newProject->date = date( 'Y-m-d H:i:s');
         $newProject->quantity = 1;
         $newProject->save();
         
         $newfile = $this->orderfolder . $newdate . '/' . $newProject->id . '.png';
         
         if( !file_exists( $this->orderfolder . $date  ) ){
            mkdir( $this->orderfolder . $date , 0777 );
         }
         copy( $filesrc, $newfile );              
         $attributes = array(
               'projectid' => $newProject->id
         
         );

         $cart = new Cart();
         $cart->addItemByProductOptionId( $productoptionid, 1, $attributes );
         $cart->save();
         
         //relocate( '/bestilling/fargelapp' );
         relocate( '/cart' );
         
      }
      
      public function thumb( $id ){
         $this->template = null;
         
         $project = new UserMerkelappOrder( $id );
         $date = date( 'Y-m-d',  strtotime( $project->date ) );
         
         $filesrc = $this->orderfolder . $date . '/' . $id . '.png';
         
         $thumb = new Imagick( $filesrc );
         $thumb->setImageFormat( "png" );
         $thumb->thumbnailImage( 75, 75, true);
         
         header( "Content-Type: image/jpeg" ); 
         echo $thumb;      
         
         
         
      }
      /**
       * Remove item from cart
       *
       * @param string $prodno
       * @param integer $referenceid
      */
      public function Remove( $prodno = '', $referenceid = '' ) {
         
         $cart = new Cart();
         $cart->removeItem( $prodno, $referenceid );
         $cart->save();
         $this->cart = $cart->enum();
         relocate( '/bestilling/fargelapp' );
         
      }
      
   }
   
?>