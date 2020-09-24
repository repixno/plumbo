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

         $this->setTemplate( 'fetchalbum.album' );

         $imagelist = array();
		 
		 
		 foreach( $this->fetchImageList( $aid ) as $image ){
			if(   substr( $image['identifier'], 0, 6 ) === "gruppe" ) {
			   $imagelist['gruppe'][] = $image; 
			}else{
			   $imagelist['portrett'][] = $image;
			}
		 }
		 $this->images = $imagelist;
		 return true;

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

         }

         return $imagelist;

	   }

    

   }

?>