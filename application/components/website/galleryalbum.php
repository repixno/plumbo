<?php
   
   /**
    * 
    * Gallery album component
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'album.gallery' );
   import( 'website.image' );

   class GalleryAlbum extends DBGalleryAlbum {
      
      public function isLoaded() {
         
         if( !$this->hasAccess() ) {
            
            throw new SecurityException( 'You have no access to this album' );
            
         }
         
         return parent::isLoaded();
         
      }
      
      /**
       * @todo Make it return false if the user has no access to this object
       */
      public function hasAccess() {
         
         return PermissionManager::current()->hasAccessTo( $this->aid, 'album' );
         
      }
      
      public function asArray() {
         
         $title = $this->getTitle();
         
         return array(
            'id'              => $this->albumid,
            'userid'          => $this->userid,
            'title'           => $title,
            'key'             => $this->key,
            'numviewed'       => (int)$this->views,
				'permission'      => PermissionManager::current()->permissionType( $this->albumid, 'album' ),
				'category'        => $this->category,
            'sharingtime'     => $this->sharingtime,
            'country'         => $this->country,
            'description'     => $this->getDescription(),
            'ownername'       => $this->getOwnerName(),
            'albumurl'        => sprintf( '/gallery/album/%d/%s', $this->aid, Util::urlize( $title ) ),
				'defaultthumbnails' => $this->getDefaultImages(),
            'defaultimageid'  => $this->default_bid,
				'thumbnailurl'    => $this->getThumbnailUrl(),
				'numimages'       => $this->numimages,
         );
         
      }
      
      /**
      * Get the default image for this album
      *
      * @return integer
      */
      public function getDefaultImageId() {

         $defaultImage = false;
         if ( $this->aid > 0 ) {

            if ( !$this->fieldGet( 'default_bid' ) ) {

               $images = new Image();
               foreach( $images->collection( 'bid', array( 'aid' => $this->aid, 'deleted_at' => null ), 'dato ASC', 1 )->fetchAllAs('Image') as $image ) {

                  $defaultImage = $image->bid;

               }

               $this->fieldSet( 'default_bid', $defaultImage );
               $this->save();

            } else {

               $defaultImage = $this->fieldGet( 'default_bid' );

            }

         }

         return $defaultImage;

      }



      public function getThumbnailUrl() {

         if( $bid = $this->getDefaultImageId() ) {
            return "/images/stream/thumbnail/".$this->getDefaultImageId();
         }

         return null;

      }
      
      public function listImageIDs() {
         
         $imagelist = array();
         if( $this->aid > 0 ) {
            $images = new Image();
            foreach( $images->collection( array( 'bid' ), array( 'aid' => $this->albumid, 'deleted_at' => null ) )->fetchAll() as $imageid ) {
               list( $imagelist[] ) = $imageid;
            }
         }
         return $imagelist;
         
      }
      
      public function getImages() {

         $imagelist = array();
         $images = new Image();
         foreach( $this->listImageIDs() as $imageid ) {
            try {
               $image = new Image( $imageid );
               $imagelist[] = $image->asArray();
            } catch( Exception $e ) {}
         }
         return $imagelist;
         
      }
      
      public function getDefaultImages() {
         
         $imagelist = array();
         if( $this->aid > 0 ) {
            $images = new Image();
   			foreach( $images->collection( 'bid', array( 'aid' => $this->albumid, 'deleted_at' => null ), 'dato ASC', 5 )->fetchAllAs('Image') as $image ) {
   			   $imagelist[] = $image->asArray();
   			}
         }
			return $imagelist;
			
      }
      
   }


?>