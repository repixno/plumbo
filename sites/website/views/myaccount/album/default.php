<?php

   /**
    * Show single image in album
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.album' );
   import( 'website.image' );

   class MyAccountAlbumDefault extends UserPage implements IValidatedView {

      static $paginationsize = 20;
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
               ),
            ),
            'rss' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
            ),
            'slideshow' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
            ),
            'slideshowparameters' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
            ),
            'popup' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      public function Execute( $aid = 0, $name = '', $page = 1 ) {

         // Setup imagesorting tags
         $album = new Album( $aid );
         // Template value if sort type is set in session.
         $this->imagesorting = $album->filesorttype;
         // Template value if sort order is set in session.
         $this->imagesortingorder = $album->filesortorder;
         // Remove sort order indicator if manual, so order navigation disappears in user interface.
         if ( $album->filesorttype == 'manual' ) {

            $this->imagesortingorder = false;

         }

         $this->albumid = $aid;

         // Define start of pagination index for current page.
         $pageBase = is_numeric( $page ) ? $page : 1;
         $this->pageoffset = MyAccountAlbumDefault::$paginationsize * ($pageBase-1);

         $this->mediarss = sprintf( '%s/myaccount/album/rss/%d', WebsiteHelper::rootBaseUrl(), $aid );

         $this->setTemplate( 'myaccount.album.showalbum' );

         return $this->fetchPaginatedAlbums( $aid, $page );

	   }

	   private function fetchImageList( $aid = 0 ) {

	      $imagelist = array();

         if( $aid > 0 ) {

            $album = new Album( $aid );

            // try setting the default image to be shown
            // if no image is given as param.
            if( empty( $bid ) ) $bid = $album->default_bid;
            $imagelist = $album->getImages();

            $this->album = $album->asArray();

         } else if( $aid == 0 ) {

            $imagelist = array();
            $images = new Image();
            foreach( $images->collection( array( 'bid' ), array( 'owner_uid' => Login::userid(), 'aid' => NULL, 'deleted_at' => NULL ) )->fetchAll() as $row ) {
               try {
                  $image = new Image( array_shift( $row ) );
                  $imagelist []= $image->asArray();
               } catch( Exception $e ) {

               }
            }

            $inbox = new Album();
            $this->album = $inbox->asArray();

         }

         return $imagelist;

	   }

      private function fetchPaginatedAlbums( $aid = 0, $page = 1 ) {

         $aid = (int) $aid;

         $imagelist = $this->fetchImageList( $aid );

         if( $page == 'all' ) {

            $this->images = $imagelist;
            return true;

         } else {

            $page = max( 1, (int) $page );

         }

         $pages = ceil( count( $imagelist ) / MyAccountAlbumDefault::$paginationsize );
		   $images = array_splice( $imagelist, ( $page - 1 ) * MyAccountAlbumDefault::$paginationsize, MyAccountAlbumDefault::$paginationsize );

		   $this->images = $images;

		   $this->pagination = array(
            'current' => $page,
		      'prev' => $page <= 1 ? '' : $page - 1,
		      'next' => $page < $pages ? $page + 1 : '',
		      'first' => 1,
		      'last' => $pages,
		      'items' => array(
               'from' => ( ( $page - 1 ) * MyAccountAlbumDefault::$paginationsize ) + 1,
               'to' => ( ( $page - 1 ) * MyAccountAlbumDefault::$paginationsize ) + count( $images ),
               'count' => count( $images ),
		      ),
		   );

      }

      public function rss( $aid = 0 ) {

         header( 'Content-type: text/xml; charset=UTF-8' );
         $this->setTemplate( 'myaccount.album.showalbum-rss' );
         $this->images = $this->fetchImageList( $aid );

      }

      public function slideshow( $aid = 0  ) {

         $this->setTemplate( 'myaccount.album.slideshowpro' );
         $this->images = $this->fetchImageList( $aid );

      }

      public function slideshowparameters( $aid = 0 ) {

         $this->setTemplate( 'myaccount.album.slideshowpro-parameters' );
         $this->images = $this->fetchImageList( $aid );

      }

      public function popup( $aid = 0 ) {

         $this->setTemplate( 'myaccount.album.show_album_popup' );
         $this->images = $this->fetchImageList( $aid );

      }

   }

?>