<?PHP

	import( 'website.album' );
	import( 'website.image' );
	import( 'core.util' );
	import( 'session.usersessionarray' );


   class OrderprintsChoosephotos extends UserPage implements IView {

      protected $template = 'order-prints.choose-photos';

      public function Execute() {
         $id = (int)$id;

         // Clear all previoulsy choosen images
         UserSessionArray::clearItems( 'choosenimages' );
         UserSessionArray::clearItems( 'printorder' );

         $sorting = UserSessionArray::getItem( 'albumsort', 0 );
		   if( isset( $sorting['sort'] ) ) {
		      $this->albumsorting = $sorting['sort'];
		   } else {
		      $this->albumsorting = 'created';
		   }

		   if( isset( $sorting['order'] ) ) {
		      $this->albumsortingorder = $sorting['order'];
		   } else {
		      $this->albumsortingorder = 'desc';
		   }

         if( empty( $id ) ) {

            //$this->albums = $this->enumAlbums();
            $allalbums = Album::enum();
            $this->albums = $allalbums;

         } else {

            $this->album = new Album( $id );
            $this->albums = $this->getAlbum( $id );
         }
      }

		public function sort( $sort = 'created', $order = 'desc' ) {

		   UserSessionArray::clearItems( 'albumsort' );
		   UserSessionArray::addItem( 'albumsort', array( 'sort' => $sort, 'order' => $order ) );
		   relocate( '/order-prints/choose-photos' );

		}

      /**
      * Get the given album
      *
      * @param integer $id
      * @return array
      */
      private function getAlbum( $id = 0 ) {

         $id = (int)$id;
         $imagelist = array();

         if( $id > 0 ) {

            $images = new Image();

            foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $id ) )->fetchAllAs('Image') as $image ) {

               $imagelist[] = $image->asArray();

            }
         }

         return $imagelist;

      }


      /**
      * Enumerate all user's albums
      *
      * @return array
      */
      private function enumAlbums() {

         $albumlist = array();
         $albums = new Album();

         if( Login::userid() > 0 ) {
            foreach( $albums->collection( 'aid', array( 'uid' => Login::userid(), 'deleted_at' => NULL, 'quarantined_at' => NULL ) )->fetchAllAs('Album') as $album ) {

               $albumlist[] = $album->asArray();

            }
         }

         return $albumlist;

      }

   }

?>
