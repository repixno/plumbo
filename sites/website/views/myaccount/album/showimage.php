<?php

   /**
    * Show single image in album
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.album' );
   import( 'website.image' );

   class MyAccountAlbumDefault extends UserPage implements IView {

      public function Execute( $aid = 0, $bid = 0 ) {
         $this->mediarss = "http://kristoffer.eurofoto.no/myaccount/album/rss/".$aid;
         $this->dfgalleryxml = "http://kristoffer.eurofoto.no/myaccount/album/dfgallery/".$aid;
      	 $this->setTemplate( 'myaccount.album.showimage' );
	     $this->fetchAlbums( $aid, $bid );
	  }
      
      private function fetchAlbums( $aid = 0, $bid = 0 ) {
         
         $aid = (int) $aid;
         $bid = (int) $bid;
         $imagelist = array();
         
         if( $aid > 0 ) {
            
            $album = new Album( $aid );
            
            // try setting the default image to be shown
            // if no image is given as param.
            if( empty( $bid ) ) $bid = $album->default_bid;
            $images = new Image();

            foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $aid ) )->fetchAllAs('Image') as $image ) {
               $imagelist[] = $image->asArray();
            }

            // Try setting the bid to be shown
            $currImage = new Image( $bid );
            if( $currImage->getOwnerId() == Login::userid() ) {
               $this->image = $currImage->asArray();
            }
            
            $this->images = $imagelist;
            $this->album = $album->asArray();
            $this->fetchAlbums();
            
         }
         
      }
      
      public function rss( $aid = 0, $bid = 0 ) {
        $this->setTemplate( 'myaccount.album.showalbum-rss' );
        $this->fetchAlbums( $aid, $bid );	
      }
      
      public function slideshow( $aid = 0, $bid = 0 ) {
        $this->setTemplate( 'myaccount.album.slideshowpro' );
        $this->fetchAlbums( $aid, $bid );	
      }
      
      public function slideshowparameters( $aid = 0, $bid = 0 ) {
        $this->setTemplate( 'myaccount.album.slideshowpro-parameters' );
        $this->fetchAlbums( $aid, $bid );	
      }
   }
?>