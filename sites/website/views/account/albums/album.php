<?php
   import( 'website.album' );
   import( 'website.uploadhelper' );
   import( 'website.image' );
   import( 'pages.protected' );
   
   class AccountAlbumsAlbum extends ProtectedPage implements IValidatedView {
      
      protected $template = 'account.albums.album';
      
      /**
       * Validator
       *
       * @return Array
       */   
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      /**
       * Execute
       * 
       * adds imagesorting, imagesortingorder, albumid and pageoffset to the view
       *
       * @param Integer $albumid
       * @param String $name
       * @param Integer $page
       * @return Array
       */
      
      public function Execute( $albumid = 0, $name = '', $page = 1 ) {
         
         if ( $albumid ) {
         
            try {
               $album = new Album( $albumid );
               $this->selectedalbumid = $aid;
               $this->selectedalbum = $album->asArray();
            } catch( Exception $e ) { $this->selectedalbumid = 0; }
            
            $this->batchid = UploadHelper::getBatchId();
            $this->albums = Album::enum( 0, 0, false, false, true );

            // Setup imagesorting tags
            
            $album = new Album( $albumid );
            
            // Template value if sort type is set in session.
            
            $this->imagesorting = $album->filesorttype;
            
            // Template value if sort order is set in session.
            
            $this->imagesortingorder = $album->filesortorder;
            
            // Remove sort order indicator if manual, so order navigation disappears in user interface.
            
            if ( $album->filesorttype == 'manual' ) {
   
               $this->imagesortingorder = false;
   
            }
   
            // Add albumid to view
            
            $this->albumid = $albumid;
   
            // Define start of pagination index for current page.
            
            $pagebase = is_numeric( $page ) ? $page : 1;
            
            // set pagination size
            
            $paginationsize = 20;
            
            $this->pageoffset = $paginationsize * ( $pagebase - 1 );
   
            // fetch albums
            
            return $this->fetchPaginatedAlbums( $albumid, $page, $paginationsize );
            
         } else {
            
            $this->batchid = UploadHelper::getBatchId();
            $this->album = null;
            
         }

	   }

	   /**
	    * Fetch list of images by albumid
	    * 
	    * adds album to the view
	    *
	    * @param Integer $albumid
	    * @return Array
	    */
	   
	   private function fetchImageList( $albumid = 0 ) {

	      $imagelist = array();
	      
	      // fetch album by albumid, do a custom query if albumid is 0
	      
	      if( $albumid == 0 ) {

            $imagelist = array();
            
            $images = new Image();
            
            foreach( $images->collection( array( 'bid' ), array( 'owner_uid' => Login::userid(), 'aid' => NULL, 'deleted_at' => NULL ) )->fetchAll() as $row ) {
               
               try {
                  
                  $image = new Image( array_shift( $row ) );
                  $imagelist []= $image->asArray();
                  
               } catch( Exception $e ) {}
               
            }

            $inbox = new Album();
            $this->album = $inbox->asArray();

         } else {
            
            $album = new Album( $albumid );
   
            // fetch images by album
   
            $imagelist = $album->getImages();
            
            // add album to the view
   
            $this->album = $album->asArray();
            
         }

         return $imagelist;

	   }
	   
	   /**
	    * Fetch albums by page
	    * 
	    * adds images and pagination to the view
	    *
	    * @param Integer $albumid
	    * @param Integer $page
	    * @param Integer $paginationsize
	    * @return unknown
	    */

      private function fetchPaginatedAlbums( $albumid = 0, $page = 1, $paginationsize = 20 ) {

         $imagelist = $this->fetchImageList( $albumid );

         if( $page == 'all' ) {
            
            // fetch all albums

            $this->images = $imagelist;

         } else {
            
            // fetch albums by page

            $page = max( 1, (int) $page );

            $pages = ceil( count( $imagelist ) / $paginationsize );
            
   		   $images = array_splice( $imagelist, ( $page - 1 ) * $paginationsize, $paginationsize );
   		   
   		   // add images to view
   
   		   $this->images = $images;

   		   // add pagination to view
   		   
   		   $this->pagination = array(
   		   
               'current'    => $page,
   		      'prev'       => $page <= 1 ? '' : $page - 1,
   		      'next'       => $page < $pages ? $page + 1 : '',
   		      'first'      => 1,
   		      'last'       => $pages,
   		      'items'      => array(
                  'from'    => ( ( $page - 1 ) * $paginationsize ) + 1,
                  'to'      => ( ( $page - 1 ) * $paginationsize ) + count( $images ),
                  'count'   => count( $images ),
   		      ),
   		      
   		   );
   		   
         }

      }

   }

?>