<?php

   /**
    *
    */

import('cewe.default');

class MyAccountAlbumDefault extends UserPage implements IValidatedView {

    static $paginationsize = 20;
      
    public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
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

        $api = new ceweApi();
        
        $data = array(
                    'includeStyleData' => 'false'
                );
            
        $images = $api->getApi( sprintf( '/photoAlbums/%s/photos', $aid ) );
		$cewealbums = $api->getApi( '/photoAlbums/' . $aid );
		
		$imagelist = array();
		
		foreach( $images->photos as $image ){
			$imagelist[] = $api->ceweImageArray( $image, $aid );
		}
        
        
        // Template value if sort type is set in session.
        $this->imagesorting = $album->filesorttype;
        // Template value if sort order is set in session.
        $this->imagesortingorder = $album->filesortorder;

        $this->albumid = $aid;

         // Define start of pagination index for current page.
        $pageBase = is_numeric( $page ) ? $page : 1;
        $this->pageoffset = MyAccountAlbumDefault::$paginationsize * ($pageBase-1);

        $this->setTemplate( 'myaccount.album.showalbum' );

		$this->album = $api->ceweAlbumArray($cewealbums);
        $this->images = $imagelist;

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