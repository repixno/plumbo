<?php

   /**
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

	import( 'website.album' );
	import( 'website.image' );
	import( 'website.user' );
	import( 'core.util' );
	import( 'session.usersessionarray' );

	class MyAccountAlbums extends UserPage implements IView {

		protected $template = 'myaccount.album.showalbums';
		static $paginationsize = 24;

		public function Execute( $page = 1 ) {

		   $id = (int)$id;

		   $album = new Album();
		   
		   $this->years = $album->getYears();
		   
		   $this->fillAlbums( $page );

		}
		public function Year( $year = 0, $page = 1){
		   $album = new Album();
		   $this->years = $album->getYears();
		   $this->fillAlbums( $page, $year );
		   $this->selectedyear = $year;
		}
		

		private function fillAlbums( $page = 1, $year  = 0 ) {

		   // Get album sort values from session.
		   $sorting = UserSessionArray::getItems( 'albumsort' );
		   if ( isset( $sorting[ 'sort' ] ) ) {

		      // Template value if sort type is set in session.
		      $this->albumsorting = $sorting['sort'];

		   } else {

		      // Fetch sort order from db.
		      $user = new User( Login::userid() );
		      $this->albumsorting = $user->albumsorttype;
		      $sorting[ 'sort' ] = $user->albumsorttype;
		      // Set sort order in session.
		      UserSessionArray::setItem( 'albumsort', $user->albumsorttype, 'sort' );

		   }

		   if ( isset( $sorting[ 'order' ] ) ) {

		      // Template value if sort order is set in session.
		      $this->albumsortingorder = $sorting[ 'order' ];

		   } else {

		      // Fetch sort order from db.
		      $user = new User( Login::userid() );
		      $this->albumsortingorder = $user->albumsortorder;
		      // Set sort order in session.
		      UserSessionArray::setItem( 'albumsort', $user->albumsortorder, 'order' );

		   }

		   // Sort order choices should be removed if sort type is set to 'manual';
         if ( $sorting[ 'sort' ] == 'manual' ) {

            $this->albumsortingorder = false;

         }
         
         // Return complete album list.
		   if( $page == 'all' ) {

            $this->albums = Album::enum();
            return true;
            
		   } else if( $year > 0) {
		      $this->albums = Album::enum( 0, 0, true, true, true, false, $year );
            return true;
		   }else{
		      // Fetch sorted list of albums.
		    	$albums = Album::enum( ( $page - 1 ) * MyAccountAlbums::$paginationsize, MyAccountAlbums::$paginationsize );
		    	// Return paginated set of albums.
            $page = max( 1, (int) $page );         
		   }
         
		   $numalbums = Album::numAlbums();
		   
		   $pages = ceil( $numalbums / MyAccountAlbums::$paginationsize );
		   #$albums = array_splice( $allalbums, ( $page - 1 ) * MyAccountAlbums::$paginationsize, MyAccountAlbums::$paginationsize );

		   $this->albums = $albums;

		   $this->pagination = array(
            'current' => $page,
		      'prev' => $page <= 1 ? '' : $page - 1,
		      'next' => $page < $pages ? $page + 1 : '',
		      'first' => 1,
		      'last' => $pages,
		      'items' => array(
               'from' => ( ( $page - 1 ) * MyAccountAlbums::$paginationsize ) + 1,
               'to' => ( ( $page - 1 ) * MyAccountAlbums::$paginationsize ) + $numalbums,
               'count' => count( $albums ),
		      ),
		   );

		}

		public function sort( $sort = 'created', $order = 'desc' ) {

         // Set session sort order.
         UserSessionArray::clearItems( 'albumsort' );
         UserSessionArray::setItem( 'albumsort', $sort, 'sort' );
         UserSessionArray::setItem( 'albumsort', $order, 'order' );

         // Save sort order.
         $user = new User( Login::userid() );
         $user->albumsorttype = $sort;
         $user->albumsortorder = $order;
         $user->save();

		   relocate( '/myaccount/albums' );

		}


		/**
		 * Listview of albums
		 *
		 */
		public function showlist( $page = 1 ) {

		   $this->setTemplate( 'myaccount.album.showalbums-list' );

		   $this->fillAlbums( $page );

		}

	}

?>