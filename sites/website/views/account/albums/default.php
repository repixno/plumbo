<?php

   import( 'website.subscription' );
   import( 'website.album' );
   import( 'pages.protected' );
   
   class AccountAlbums extends ProtectedPage implements IValidatedView  {

      protected $template = 'account.albums.index';
      
      /**
       * Validator
       *
       * @return array array of fields
       * 
       */

		public function Validate() {

         return array(
            'execute' => array( 
               'fields' => array(
                  'page' => VALIDATE_INTEGER,
                  'mode' => VALIDATE_STRING
               )
            ),
            'sort' => array(
               'fields' => array(
                  'sort' => VALIDATE_STRING,
                  'order' => VALIDATE_STRING
               )
            )
         );
         
		}
      
      /**
       * Execute
       *
       * @param Integer $page
       * @param String $mode page or all
       */

      public function Execute( $page = 1, $mode = 'all' ) {
         
         // user variables
         $this->usersubscription = $this->getSubscription();
         $this->usersubscriptionlastdate = $this->getSubscriptionLastDate();
         
         // user item counts
         $this->userimagecount = $this->getImageCount();
         $this->useralbumcount = $this->getAlbumCount();
         $this->userinboxcount = $this->getInboxCount();
         
         // fill view with albums by page, pagination size and mode
         $this->fillAlbums( $page, 20, $mode );
         

		}

		/**
		 * fill view with album-variables (albums, pagination, albumsorting) by page, paginationsize and mode
		 *
		 * @param Integer $page
		 * @param Integer $paginationsize
		 * @param String $mode page or all
		 */
		
		private function fillAlbums( $page = 1, $paginationsize = 20, $mode = 'page' ) {

		   // Get album sort values from session.
		   $sorting = UserSessionArray::getItems( 'albumsort' );
		   
		   if ( isset( $sorting[ 'sort' ] ) ) {
   
   	      // Template value if sort type is set in session.
   	      $albumsorting = $sorting[ 'sort' ];

		   } else {
		      
		      // Fetch sort order from db.
		      $user = new User( Login::userid() );
		      
		      $albumsorting = $user->albumsorttype;
		      
		      $sorting[ 'sort' ] = $user->albumsorttype;
		      
		      // Set sort order in session.
		      UserSessionArray::setItem( 'albumsort', $user->albumsorttype, 'sort' );

		   }

		   if ( isset( $sorting[ 'order' ] ) ) {

		      // Template value if sort order is set in session.
		      $albumsortingorder = $sorting[ 'order' ];

		   } else {

		      // Fetch sort order from db.
		      $user = new User( Login::userid() );
		      
		      $albumsortingorder = $user->albumsortorder;
		      
		      // Set sort order in session.
		      UserSessionArray::setItem( 'albumsort', $user->albumsortorder, 'order' );

		   }

		   // Sort order choices should be removed if sort type is set to 'manual'.
         if ( $sorting[ 'sort' ] == 'manual' ) {

            $albumsortingorder = false;

         }
         
         if ( !isset( $sorting['sort'] ) ) {
            
            $sorting['sort'] = 'year';
            $sorting['view'] = 'year';
            $sorting['order'] = 'asc';
            
         } else if ( $sorting[ 'sort'] == 'year' ) {
            
            $sorting['view'] = 'year';
            
         }

         // Fetch sorted list of albums.
		   $allalbums = Album::enum( 0, 0, true, true, false, false );
         
		   if( $mode == 'all' ) {
		      
            // Return complete album list.
		      $albums = $allalbums;

		   } else {

   	      // Return paginated set of albums.
   	      $page = max( 1, (int) $page );
   		   $pages = ceil( count( $allalbums ) / $paginationsize );
 
   		   $albums = array_splice( $allalbums, ( $page - 1 ) * $paginationsize, $paginationsize );
   
   		   //$this->albums = $albums;
   		   $this->pagination = array(
               'current'   => $page,
      	      'prev'      => $page <= 1 ? '' : $page - 1,
      	      'next'      => $page < $pages ? $page + 1 : '',
      	      'first'     => 1,
      	      'last'      => $pages,
      	      'items'     => array(
                                 'from'   => ( ( $page - 1 ) * $paginationsize ) + 1,
                                 'to'     => ( ( $page - 1 ) * $paginationsize ) + count( $albums ),
                                 'count'  => count( $albums ),     
      	      ),
      	      
   		   );
   		   
		   }
		   
		   // rendering albums indexed by created/preferences-year when sortingview is year.
		   if ( $sorting['view'] == 'year' ) {
		      
		      $indexedalbums = array();
		      
		      foreach ( $albums as $album ) {
		         
		         $year = $album['owner']['preferences']['year'] > 0 ? $album['owner']['preferences']['year'] : date( 'Y', $album['created'] );
		         
		         if ( !isset( $indexedalbums[$year]['year'] ) ) $indexedalbums[$year]['year'] = $year;
		         $indexedalbums[$year]['albums'][] = $album;
		         
		      }
		      
		      $this->years = $indexedalbums;
		      
		   } else {
		      
		      $this->albums = $albums;
		      
		   }

		   // assigning albumsorting to view
		   $this->albumsorting = sprintf( '%s/%s', $albumsorting, $albumsortingorder );
		   
		}
		
      /**
       * Sort albums
       *
       * @param String $sort
       * @param String $order
       */
      
		public function sort( $sort = 'created', $order = 'desc' ) {

         // Set session sort order.
         UserSessionArray::clearItems( 'albumsort' );
         UserSessionArray::setItem( 'albumsort', $sort, 'sort' );
         UserSessionArray::setItem( 'albumsort', $order, 'order' );
         
         if ( $sort == 'year') UserSessionArray::setItem( 'albumsort', $sort, 'view' );
    
         // Save sort order.
         $user = new User( Login::userid() );
         $user->albumsorttype = $sort;
         $user->albumsortorder = $order;
         $user->save();

		   relocate( '/account/albums' );

		}
         
      /**
       * Count albums
       * 
       * @return array
       */
      
      private function getAlbumCount() {
         
         return (int)DB::query( "SELECT count(aid) FROM bildealbum WHERE uid = ? AND deleted_at IS NULL", Login::userid() )->fetchSingle();
         
      }
      
      /**
       * Count images
       * 
       * @return array
       */
      
      private function getImageCount() {
         
         return (int)DB::query( "SELECT count(bid) FROM bildeinfo WHERE owner_uid = ? AND deleted_at IS NULL", Login::userid() )->fetchSingle();

      }
      
      /**
       * Count images in inbox
       * 
       * @return array
       */
      
      private function getInboxCount() {
         
         //die ("SELECT count(bid) FROM bildeinfo WHERE aid IS NULL and owner_uid = " . Login::userid() . " AND deleted_at IS NULL");
         return (int)DB::query( "SELECT count(bid) FROM bildeinfo WHERE aid IS NULL and owner_uid = ? AND deleted_at IS NULL", Login::userid() )->fetchSingle();

      }
      
      /**
       * Get subscription for user
       *
       * @return array
       */
      
      private function getSubscription() {

         return Subscription::staticAsArray( Login::userid() );
         
      }
      
      /**
       * Get last subscriptiondate for user
       *
       * @return array
       */
      
      private function getSubscriptionLastDate() {
      
         return Subscription::latestOrderValidToByUserId( login::userid() );
            
      }
      
   }  

?>