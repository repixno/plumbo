<?PHP
   
   /**
    * Default page and functions for order-prints
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class OrderprintsIndex extends UserPage implements IView {
      
      protected $template = 'order-prints.order-prints';
      
      public function Execute() {
         
         if( Dispatcher::getPortal() == 'FC-001') relocate( "/frontpage" );
        
         Session::pipe( 'uploadreturnurl', sprintf( '%s/%s', rtrim( $_SERVER['REQUEST_URI'], '/' ), 'choose-quantity' ) );
         
         $res = DB::query( "
            SELECT po.prodno
            FROM site_product_option AS po LEFT JOIN site_textentity AS te ON po.id = te.id 
            WHERE po.tags 
            LIKE( '%print%' ) OR 
               ( po.tags LIKE( '%enlargement%' ) AND te.deleted IS NULL ) OR 
               ( po.tags LIKE( '%productionmethod%' ) AND te.deleted IS NULL ) OR 
               ( po.tags LIKE( '%correctionmethod%' ) AND te.deleted IS NULL ) OR 
               ( po.tags LIKE( '%paperquality%' ) AND deleted IS NULL )" );
         
         $prints = array();
         $enlargements = array();
         $productionmethods = array();
         $correctionmethod = array();
         $paperquality = array();
         
         while( list( $prodno ) = $res->fetchRow() ) {
            
            $product = ProductOption::fromProdNo( "$prodno" );
            if( $product->isLoaded() && $product instanceof ProductOption ) {

               $tmpproduct = new Product( $product->productid );
               if( $tmpproduct->isLoaded() ) {
                  $producttitle = $tmpproduct->title;
                  $productbody = $tmpproduct->body;
                  $productingress = $tmpproduct->ingress;
               }
               
               $productarray = $product->asArray();
               $productarray["title"] = $producttitle;
               $productarray["body"] = $productbody;
               $productarray["ingress"] = $productingress;
               
               if( stristr( $product->tags, 'print' ) ) {
                  $prints []= $productarray;
               } else if( stristr( $product->tags, 'enlargement' ) ) {
                  $enlargements []= $productarray;
               } else if( stristr( $product->tags, 'productionmethod' ) ) {
                  $productionmethods []= $productarray;                  
               } else if( stristr( $product->tags, 'correctionmethod' ) ) {
                  $correctionmethod []= $productarray;
               } else if( stristr( $product->tags, 'paperquality' ) ) {
                  $paperquality []= $productarray;
               }
               
            }
            
         }
         
         if( count( $prints ) > 0 ) {
            $this->prints = $prints;
         }
         
         if( count( $enlargements ) > 0 ) {
            $this->enlargements = $enlargements;
         }
         
         if( count( $productionmethods ) > 0 ) {
            $this->productionmethods = $productionmethods;
         }
         
         if( count( $correctionmethod ) > 0 ) {
            $this->correctionmethods = $correctionmethod;
         }
         
         if( count( $paperquality ) > 0 ) {
            $this->paperquality = $paperquality;
         }
         
         if( count( $order["imageobjects"] ) > 0 ) {
            $this->defaultprodno = $this->defaultPrintProduct( $this->order );
         }  
      }
      
      /**
       * Add an album to choosen images 
       * for use in order process
       *
       * @param integer $id
       */
      public function fromAlbum( $id = 0 ) {
         
         $this->setTemplate( '' );
         $id = (int) $id;
         if( $id == 0 ) relocate( '/myaccount/albums' );
         
         if( $id > 0 ) 
         {
            
            try {
               UserSessionArray::clearItems( 'choosenimages' );
               // Get the images available for purchase here
               //$choosenImages = UserSessionArray::getItems( "choosenimages" );
               //$choosenImages = reset( $choosenImages );
               
               $album = new Album( $id );
               
               if( !$album->isLoaded() || !$album instanceof Album ) relocate( '/myaccount/albums' );
            
               if( is_null( $album->numImages ) ) relocate( '/myaccount/albums' );
               
               
               $restricts = array(
                  'aid' => $album->id, 
                  'deleted_at' => null, 
                  'quarantined_at' => null
                  );
               // Support for non logged in purchase of link shared album.
               /*if ( !( $album->permission == 2 && !Login::isLoggedIn() ) ) {
                  
                  $restricts[ 'owner_uid' ] = Login::userid();
                  
               }*/
               
               $images = new Image();			      
               foreach( $images->collection( 'bid', $restricts )->fetchAll() as $row ) {
               
                  try {
                     list( $imageid )  = $row;
                     $image = new Image( $imageid );
                     $choosenImages[$image->bid] = $image->asArray();
                  } catch( Exception $e ) {
                  }
               
               }
               
               if( count( $choosenImages ) > 0 ) {
                  
                  UserSessionArray::clearItems( 'choosenimages' );
                  UserSessionArray::addItem( 'choosenimages', $choosenImages );
			         relocate( '/order-prints/choose-quantity' );
			         die();
               }
            
            } catch( Exception $e ) {
               
               relocate( '/myaccount/albums' );
               die();
               
            }
            
         }
         else if( $id == 'inbox' && Login::userid() ){
            $restricts = array(
               'aid' => null, 
               'deleted_at' => null, 
               'quarantined_at' => null,
               'owner_uid' => Login::userid()
            );
            
               $images = new Image();			      
               foreach( $images->collection( 'bid', $restricts )->fetchAll() as $row ) {
               
                  try {
                     list( $imageid )  = $row;
                     $image = new Image( $imageid );
                     $choosenImages[$image->bid] = $image->asArray();
                  } catch( Exception $e ) {
                  }
               
               }  
               if( count( $choosenImages ) > 0 ) {
                  
                  UserSessionArray::clearItems( 'choosenimages' );
                  UserSessionArray::addItem( 'choosenimages', $choosenImages );
			         relocate( '/order-prints/choose-quantity' );
			         die();
               }
         }
         
         UserSessionArray::clearItems( 'choosenimages' );
         UserSessionArray::addItem( 'choosenimages', $choosenImages );
         relocate( '/myaccount/albums' );
         die();
         
      }
      
      /**
       * Add selected images to choosen images 
       * for use in order process
       *
       */
      public function fromSelected( $id = 0 ) {

         $this->setTemplate( '' );

         if ( empty( $id ) ) relocate( WebsiteHelper::rootBaseUrl() );
         
         $imageIds = $_POST[ 'image' ];

         try {

            $album = new Album( $id );

            $choosenImages = array_filter( UserSessionArray::getItems( "choosenimages" ) );

            $imageColl = new Image();
            $imageList = array();
            foreach( $imageColl->collection( 'bid', array( 'bid' => array( 'IN', $imageIds), 'deleted_at' => null, 'quarantined_at' => null ) )->fetchAll() as $row ) {
               try{
                  list( $imageid ) = $row; 
                  $image = new Image( $imageid );
                  $imageData = $image->asArray();
                  if ( $album->purchaseaccess ) {
                     $choosenImages[ $image->imageid ] = $imageData;
               
                  }
               }
               catch( Exception $e ) {}

            }

            if( count( $choosenImages ) > 0 ) {

               UserSessionArray::clearItems( 'choosenimages' );
               UserSessionArray::addItem( 'choosenimages', $choosenImages );
               relocate( '/order-prints/choose-quantity' );
               die();

            }
            
         } catch( Exception $e ) {
         }   

         relocate( WebsiteHelper::rootBaseUrl() );
         die();
         
         
      }
      
      /**
       * Order a single image
       *
       * @param integer $id
       */
      public function fromImage( $id = 0 ) {
         
         $this->setTemplate( '' );
         
         $id = (int) $id;
         
         if( $id == 0 ) relocate( '/myaccount/albums' );
         
         if( $id > 0 ) {
            
            try {
               
               // Get the images available for purchase here
               $choosenImages = UserSessionArray::getItems( "choosenimages" );
               $choosenImages = reset( $choosenImages );
               
               $image = new Image( $id );
               
               if( !$image->isLoaded() || !$image instanceof Image ) relocate( '/myaccount/albums' );
               
               $choosenImages[$image->bid] = $image->asArray();

               UserSessionArray::clearItems( 'choosenimages' );
               UserSessionArray::addItem( 'choosenimages', $choosenImages );
               
			      relocate( '/order-prints/choose-quantity' );
               util::debug( 'hmm4' );
               die();
               
            } catch( Exception $e ) {
               
               relocate( '/myaccount/albums' );
               die();
            }
            
         }
         
         relocate( '/myaccount/albums' );
         die();
      }
      
      
      /**
       * Update the cart with new print/enlargement order
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function update() {
         
         $printproducts = $_POST['orderitem'];
         $numprints = 0;
         $cart = new Cart(); // Load cart
         
         // Reset all attributes for previous order
         $cart->removePrintAttribute( 'productionmethod' );
         $cart->removePrintAttribute( 'correctionmethod' );
         $cart->removePrintAttribute( 'paperquality' );
         
         // Loop through new order
         if( count( $printproducts ) > 0 ) {

            foreach( $printproducts as $prodno => $images ) {
               
               // Remove old order row
               $cart->removeItem( $prodno );
               
               if( count( $images ) > 0 ) {
                  
                  // Get number of prints in new order row
                  foreach( $images as $image => $qty  ) {
                     $numprints += $qty;
                  }
                  
               }
               
               // Add the new order row to cart
               if( $numprints > 0 ) {
                  $cart->addItem( $prodno, $numprints, array( "images" => $images ) );
               }
               
               // Reset
               $images = array();
               $numprints = 0;
               $qty = 0;
               
            }
            
         }
         
         // Add the new production-, correctionmethods and paperquality to cart
         if( isset( $_POST["white-borders"] ) ) {
            //$cart["productionmethod"] = $quantitydata["white-borders"];
            $cart->addPrintAttribute( $_POST["white-borders"] );
         } else {
            if( isset( $_POST["productionmethod"] ) ) {
               $cart->addPrintAttribute( $_POST["productionmethod"] );
            }
         }
         if( isset( $_POST["correctionmethod"] ) ) {
            $cart->addPrintAttribute( $_POST["correctionmethod"] );
         } 
         if( isset( $_POST["paperquality"] ) ) {
            $cart->addPrintAttribute( $_POST["paperquality"] );
         } 

         UserSessionArray::clearItems( "printorder" );
         UserSessionArray::clearItems( "choosenimages" );
         
         $cart->save();
         relocate( '/cart' );
         
         
      }
      
   }
   
?>