<?PHP

   import( 'website.image' );
   import( 'session.usersessionarray' );
   import( 'website.subscription' );
   import( 'website.album' );
   import( 'website.credit' );
   import( 'website.order' );

   class MyAccountIndex extends UserPage implements IView {

      protected $template = 'myaccount.index';

      private $totalOrders = 0;

      public function Execute() {
		 
		 if( Dispatcher::getPortal() == 'ST-001' ){
			relocate('/lag-lokk/checkout/');
		 }

         // assign to the template
         #$this->numimages = $this->findNumImages();
         #$this->numalbums = $this->findNumAlbums();
         #$this->images = $this->enumLatestImages();
         $this->albums = $this->enumLatestAlbums();
         $this->reedfoto = $this->enumReedfoto();
         $this->sharedtome = $this->enumSharedToMe();
         $this->sharedbyme = $this->enumSharedByMe();
         $this->subscription = $this->subscription();
         $this->projects = $this->enumLatestProjects();
         $this->orders = $this->enumOpenOrders();
         $this->numorders = $this->totalOrders;
         $this->credits = $this->credits();
         #$this->stats = $this->stats();
         
         $this->hasoldcart = false; // $this->hasOldCart();
         

      }

      private function hasOldCart() {
         
         $cartfile = sprintf( '/data/global/cart/%d', login::userid() );
         return file_exists( $cartfile ) && filesize( $cartfile ) > 0;
         
      }
      
      private function findNumImages() {

         return (int) DB::query( 'SELECT COUNT(bid) FROM bildeinfo WHERE owner_uid = ? AND deleted_at IS NULL', Login::userid() )->fetchSingle();

      }

      private function findNumAlbums() {

         return (int) DB::query( 'SELECT COUNT(aid) FROM bildealbum WHERE uid = ? AND deleted_at IS NULL', Login::userid() )->fetchSingle();

      }

      /**
       * Enumerate user's latest uploaded images
       *
       * @param integer $limit
       * @return array
       *
       */
      private function enumLatestImages( $limit = 3 ) {

        $images = array();
         $lastimages = new Image();
         foreach( $lastimages->collection( 'bid', $where = array( 'owner_uid' => Login::userid(), 'deleted_at' => NULL ), 'dato DESC, time DESC', $limit )->fetchAllAs('Image') as $image ) {
            $images[] = $image->asArray();
         }

         return $images;

      }

      private function enumLatestProjects( $limit = 5 ) {

         $user = new User( Login::userid() );
		   $projects = array();
		   foreach ( $user->listProjects( $limit ) as $project ) {

		      $type = $project->type == null ? 'photobook' : $project->type;

		      $projects[] = array(
		         'id' => $project->id,
		         'title' => $project->title,
		         'description' => $project->description,
		         'url' => '',
		         'isShared' => $project->share ? true : false,
		         'date' => $project->created,
		         'product' => $project->product->asArray(),
		         'type' => $type,
		         'editurl' => '/create/' . $type . '/edit/' . $project->id
	         );

		   }

		   return $projects;

      }
      
      
      /**
       * Used to check for open orders, this now fetches 5 latest orders
       *
       * @return array $orders
       */
      private function enumOpenOrders() {

         $orders = Array();
         $collection = new Order();
         foreach( $collection->collection( array( 'id' ), array( 'uid' => Login::userid(), 'deleted' => null ), 'ordrenr DESC', 5 )->fetchAllAs( 'Order' ) as $order ) {
         	
            $this->totalOrders++;

            $orders[] = array(
               'id' => $order->id,
               'number' => $order->ordernum,
               'price' => $order->price,
            );


         }

         return $orders;

      }

      private function enumLatestAlbums( $limit = 6 ) {
         
         $albums = array();
         $lastalbums = new Album();
         foreach( $lastalbums->collection( 'aid', $where = array( 'uid' => Login::userid(), 'deleted_at' => NULL ), 'created_time DESC NULLS LAST', $limit )->fetchAllAs('Album') as $album ) {
            $albums[] = $album->asSimpleArray();
         }
         
         return $albums;

      }


      /**
       * Enumerate shared albums to user
       *
       * @param integer $limit
       * @return array
       */
      private function enumSharedToMe( $limit = 6 ) {

         $res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() )->fetchAll() );

         $sharedList = array();
         while( count( $res ) > 0 && count( $sharedList ) < $limit ) {
            list( $albumid ) = array_pop( $res );
            if( !isset( $sharedList[$albumid] ) ) {
               try {
                  $album = new Album( $albumid );
		  if( $album->uid != 943910 ){
		     $sharedList[$albumid]= $album->asSimpleArray();
		  }
               } catch( Exception $e ) {}
            }
         }

         return array_values( $sharedList );

      }
      
            /**
       * Enumerate shared albums to user
       *
       * @param integer $limit
       * @return array
       */
      private function enumReedfoto( $limit = 6 ) {

         $res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() )->fetchAll() );

         $sharedList = array();
         while( count( $res ) > 0 && count( $sharedList ) < $limit ) {
            list( $albumid ) = array_pop( $res );
            if( !isset( $sharedList[$albumid] ) ) {
               try {
                  $album = new Album( $albumid );
		  if( $album->uid == 943910 ){
		     $sharedList[$albumid]= $album->asSimpleArray();
		  }
               } catch( Exception $e ) {}
            }
         }

         return array_values( $sharedList );

      }

      private function enumSharedByMe( $limit = 6 ) {

         $res = array_reverse( DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE aid IN ( SELECT aid FROM bildealbum WHERE uid = ? AND deleted_at IS NULL )", Login::userid() )->fetchAll() );

         $sharedList = array();
         while( count( $res ) > 0 && count( $sharedList ) < $limit ) {
            list( $albumid ) = array_pop( $res );
            if( !isset( $sharedList[$albumid] ) ) {
               try {
                  $album = new Album( $albumid );
                  $sharedList[$albumid]= $album->asSimpleArray();
               } catch( Exception $e ) {}
            }
         }

         return array_values( $sharedList );

      }

      /**
       * Get possible subscription for user
       *
       * @return array
       */
      private function subscription() {

         return Subscription::staticAsArray( Login::userid() );
         
      }


      /**
       * Get possible credits for this user
       *
       * @return array
       */
      private function credits() {

         return Credit::enum( Login::userid() );

      }


      /**
       * Get stats about image quantity
       * and order quantity
       *
       * @return array
       *
       */
      private function stats() {

         // Get image quantity
         $res = DB::query( "
            SELECT
               count(bid)
            FROM
               bildeinfo
            WHERE
               owner_uid = ? AND
               deleted_at IS NULL
         ", Login::userid() );

         list( $imageQty ) =  $res->fetchRow();

         // Get order quantity
         $res = DB::query( "
            SELECT
               count(ordrenr)
            FROM
               historie_ordre
            WHERE
               uid = ? AND
               to_production IS NOT NULL AND
               deleted IS NULL
         ", Login::userid() );

         list( $orderQty ) = $res->fetchRow();

         return array(
            "imagequantity" => $imageQty,
            "orderquantity" => $orderQty,
         );

      }

   }

?>