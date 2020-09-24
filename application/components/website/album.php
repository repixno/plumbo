<?php

   /**
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'core.util' );
   import( 'website.image' );
   model( 'album.index' );

   class NoPasswordException extends SecurityException {}

   class Album extends DBAlbum {

      private $defaultimages = array();
      private $permissions = array();

      static $validAlbumSortTypes = array(
         'created',
         'title',
         'manual'
      );
      static $validAlbumSortOrders = array(
         'asc',
         'desc'
      );

      static function getUserAlbumByTitle( $userid, $title ) { 
         return Album::fromFieldValue( array( 'uid' => $userid, 'namn' => $title, 'deleted_at' => null ), 'Album', false ); 
      } 

      public function isLoaded() {

         if( $this->deleted_at ) {

            throw new SecurityException( 'This album no longer exists!' );

         }

         if( !$this->hasAccess() ) {

            throw new SecurityException( 'You have no access to this album' );

         }

         return parent::isLoaded();

      }

      /**
       * @todo Make it return false if the user has no access to this object
       */
      public function hasAccess() {
         
         if(Login::userid()){
            $yours = $this->uid == Login::userid();
            $access = DB::query( 'SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ? AND aid = ?', Login::userid(), $this->aid )->fetchSingle();
         }
         
         $permissions = PermissionManager::current();
         if( $this->password && !$yours && !$access) {
            
            if( !$permissions->compareTokenFor( $this->aid, 'album', $this->password ) ) {

               throw new NoPasswordException( 'You have no access to this album' );

            }
            
         }
         
         if( $permissions->hasAccessTo( $this->aid, 'album' ) ) {
            
            return true;
            
         }
         
         if( !$yours && !$this->access ) {
            
            return false;
            
         }
         
         return false;
         
      }
      
      public function getYears(){
         
         
			$queryString = "
			   SELECT
			      DISTINCT( year )
			   FROM
			      bildealbum
			   WHERE
			      uid=?
			   ";

         $years = array();            
         foreach( DB::query( $queryString, Login::userid() )->fetchAll() as $year ) {
            list( $years[] ) = $year;
         }
         
         return $years;
         
      }

      public function getDefaultImages() {

         return array();

      }

      public function getAlbumURL() {

         return sprintf( '%s/myaccount/album/%d/%s', WebsiteHelper::rootBaseUrl(), $this->aid, util::urlize( $this->title ) );

      }


      public function getPublicKey() {

         import( 'math.signer' );
         return Signer::sign( $this->aid, 'share' );

      }

      /**
       * Get sharing url
       *
       * @param Integer $friendid
       * @return String
       */

      public function getSharingURL() {

         return sprintf( '%s/shared/album/%d/%s/1/%s', WebsiteHelper::rootBaseUrl(), $this->aid, util::urlize( $this->title ), $this->getPublicKey() );

      }

      /**
       * Get gallery url
       *
       * @return String
       */

      public function getGalleryURL() {

         return sprintf( '%s/gallery/album/%d/%s', WebsiteHelper::rootBaseUrl(), $this->aid, util::urlize( $this->title ) );

      }

      /**
       * Get image ids
       *
       * @return Array
       */
      public function listImageIDs() {

         $validsort = array(
            'created'   => 'time',
            'title'     => "( substring(tittel, '^[0-9]{1,9}') )::int , substring( lower(tittel), '[^0-9_].*$' )",
            'manual'    => 'sorting',
         );

         $validorder = array(
            'asc',
            'desc'
         );

         $orderby = $validsort[ $this->filesorttype ];
         $order = $this->filesortorder;

         $imagelist = array();
         if( $this->aid > 0 ) {
            
            $count = 0;
            $images = new Image();
            
            
            try{
            foreach( $images->collection( array( 'bid' ), array( 'aid' => $this->aid, 'deleted_at' => null ), $orderby.' '.$order )->fetchAll() as $imageid ) {
               list( $imagelist[] ) = $imageid;
               $count++;
            }
            }catch ( Exception  $e ){
                 mail( 'tor.inge@eurofoto.no', "albumsortingbug" , $this->aid  . ' - '  . $e->getMessage() );
            }
            
            $identifier = sprintf( 'album-imgcount[%d]', $this->aid );
            CacheEngine::write( $identifier, $count, 3600 );
            
         }
         
         return $imagelist;

      }
      
      
      /**
      * Get the number of images for this album
      *
      * @return integer
      */
      public function getNumImages() {

         $identifier = sprintf( 'album-imgcount[%d]', $this->aid );
         if( !$count = (int) CacheEngine::read( $identifier, 0 ) ) {
            
            // TODO: Replace me with a more modelesqe way of doing it.
            $count = (int) DB::query( 'SELECT COUNT(bid) FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL', $this->aid )->fetchSingle();
            CacheEngine::write( $identifier, $count, 3600 );
            return $count;
            
         } else {
            
            return $count;
            
         }
         
      }
      
       /**
       * get images
       *
       * @return Array
       */
      
      public function getImages() {

         $imagelist = array();
         $images = new Image();

         foreach( $this->listImageIDs() as $imageid ) {

            try {
               $image = new Image( $imageid );
               $asarray = $image->asArray();
               $imagelist[] = $asarray;
            } catch( Exception $e ) {}

         }

         return $imagelist;

      }
      
      public function asSimpleArray() {

         return array(
               'albumurl' => sprintf( '/myaccount/album/%d/%s', $this->aid, Util::urlize( $title ) ),
               'title' => String::saferHTML( $this->getTitle() ),
               'thumbnailurl' => $this->getThumbnailUrl(),
            );

      }
      
      
      

      public function asArray( $includeDetails = true, $includeThumbnails = true ) {

         if( $this->aid == Model::CREATE ) {

            return $this->getInbox( $includeDetails, $includeThumbnails );

         }

         $title = String::saferHTML( $this->getTitle() );

         $permission = PermissionManager::current()->permissionType( $this->aid, 'album' );

         $yours = $this->uid == login::userid();

         $result = array(
				'id' => $this->aid,
				'title' => $title,
				'urlname' => Util::urlize( $title ),
				'identifier' => $this->identifier,
				'ownerid' => $this->uid,
				'numviewed' => (int)$this->views,
				'permission' => $permission,
				'publickey' => $this->access == PERMISSION_SHARED ? $this->getPublicKey() : '',
				'password' => $this->password,
				'created' => $this->created_time,
            'owner' => array(
               'uid' => $this->uid,
			      'name' => User::getNameFromUid( $this->uid ),
			      'yours' => $yours,
			      'preferences' => array(
			         'purchase' => $this->purchaseaccess,
                  'download' => $this->downloadaccess,
                  'year'     => $this->year,
			      ),
            ),
            'access' => array(
               'purchase' => $yours || $this->purchaseaccess,
               'download' => $yours || $this->downloadaccess,
            ),
			);

			if( $includeDetails ) {

			   $description = $this->getDescription();

				$result['descriptionraw'] = String::saferHTML( $description );
				$result['description'] = String::enhanceAsHTML( $description );
				$result['albumurl'] = sprintf( '/myaccount/album/%d/%s', $this->aid, $result['urlname'] );
				$result['shared'] = array(
				   'link' => $this->isLinked(),
				   'password' => $this->hasPassword(),
				   'public' => $this->isPublic(),
				   'groups' => $this->isSharedWithGroups(),
				   'friends' => $this->isSharedWithFriends(),
				   'friendsorgroups' => $this->isSharedWithGroups() || $this->isSharedWithFriends() ? true : false,
				);
				$result['galleryurl'] = $result['shared']['public'] ? $this->getGalleryURL() : '';
				$result['sharingurl'] = $result['shared']['link'] ? $this->getSharingURL() : '';
				$result['isshared'] = $result['shared']['link']
				                   || $result['shared']['password']
				                   || $result['shared']['public']
				                   || $result['shared']['groups']
				                   || $result['shared']['friends']
				                    ? true : false;
			}

			if( $includeThumbnails ) {
				$result['defaultimageid'] = $this->default_bid;
				$result['thumbnailurl'] = $this->getThumbnailUrl();
				$result['numimages'] = $this->numimages;
				$result['defaultthumbnails'] = $this->getDefaultImages();
			}

			return $result;

      }

      public function hasPassword() {

         return $this->password ? true : false;

      }

      public function isLinked() {

         return $this->access == 2 ? true : false;

      }

      public function save() {

         $result = parent::save();
         $this->reloadPermissions( true );
         return $result;

      }

      public function reloadPermissions( $force = false ) {

         if( $this->aid > 0 ) {

            $identifier = sprintf( 'album-permission-info[%d]', $this->aid );
            $this->permissions = CacheEngine::read( $identifier, array() );

            if( $force || !is_array( $this->permissions ) || !count( $this->permissions ) ) {

               $this->permissions = array(
      			   'public' => DB::query( 'SELECT COUNT(aid) FROM public_album WHERE aid = ?', $this->aid )->fetchSingle() > 0 ? true : false,
      			   'groups' => DB::query( 'SELECT COUNT(aid) FROM grupp_tilgangtilalbum_dedikert WHERE aid = ?', $this->aid )->fetchSingle() > 0 ? true : false,
      			   'friends' => DB::query( 'SELECT COUNT(aid) FROM tilgangtilalbum_dedikert WHERE aid = ? AND groupid=0', $this->aid )->fetchSingle() > 0 ? true : false,
               );

               CacheEngine::write( $identifier, $this->permissions );

            }

            return true;

         } else {

            return false;

         }

      }

      private function validatePermissions() {

         if( !is_array( $this->permissions ) || !count( $this->permissions ) ) {
            return $this->reloadPermissions();
         }

         return true;

      }

      public function isPublic() {

         $this->validatePermissions();
         return $this->permissions['public'] ? true : false;

      }

      public function isSharedWithFriends() {

         $this->validatePermissions();
         return $this->permissions['friends'] ? true : false;

      }

      public function isSharedWithGroups() {

         $this->validatePermissions();
         return $this->permissions['groups'] ? true : false;

      }

      
      /**
       * Enumerate albums shared to user
       *
       * @param boolean $onlyForPurchase Show only albums that have purchase access
       * @return array
       */
      static function enumSharedToMe( $onlyForPurchase = false ) {
         
         $albumlist = array();
         $albumids = array();

         if( !Login::isLoggedIn() ) return $albumlist;

         // Get shared albums
			$queryString = "
			   SELECT
			      aid
			   FROM
			      tilgangtilalbum_dedikert
			   WHERE
			      uid=?
			   ";
			
			$res = DB::query( $queryString, Login::userid() );
			while( list( $ded ) = $res->fetchRow() ) {
			      
			   try {
			         
			      $sharedalbum = new Album( $ded );
			      if( $onlyForPurchase ) {
			         if( $sharedalbum->for_sale ) {
			            $albumids []= $sharedalbum->id;
			         }
			      } else {
			         $albumids []= $sharedalbum->id;
			      }
			         
			   } catch( Exception $e ) { /* Do nothing */ }
			      
			}
			   
			foreach( $albumids as $albumid ) {
			   try {
               $album = new Album( $albumid );
	       if( $album->uid != 943910 ){
		  $albumlist[] = $album->asArray( $includeDetails, $includeThumbnails );
	       }
            } catch( Exception $e ) {}
			}

			return $albumlist;
         
      }
	  
	  
	  static function enumReedfoto( $onlyForPurchase = false ) {
         
         $albumlist = array();
         $albumids = array();

         if( !Login::isLoggedIn() ) return $albumlist;
			
			$res = DB::query( "SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ?", Login::userid() );
			while( list( $ded ) = $res->fetchRow() ) {
			      
			   try {
			         
			      $sharedalbum = new Album( $ded );
			      if( $onlyForPurchase ) {
			         if( $sharedalbum->for_sale ) {
			            $albumids []= $sharedalbum->id;
			         }
			      } else {
			         $albumids []= $sharedalbum->id;
			      }
			         
			   } catch( Exception $e ) { /* Do nothing */ }
			      
			}
			   
			foreach( $albumids as $albumid ) {
				  try {
					 $album = new Album( $albumid );
						if( $album->uid == 943910 ){
							  $albumlist[] = $album->asArray( $includeDetails, $includeThumbnails );
						}
				  } catch( Exception $e ) {}
			}

			return $albumlist;
         
      }
	  
      
      static function numAlbums( $includeInbox = true ) {
         
         $collection = new Album();
			$albumids = array();
			foreach( $collection->collection( array( 'aid' ), array( 'uid' => Login::userid(), 'deleted_at' => NULL ) )->fetchAll() as $album ) {
            list( $albumids[] ) = $album;
			}
         
		   return $includeInbox ? count( $albumids ) + 1 : count( $albumids );
         
      }
      
      /**
       * Enumerate user's albums
       *
       * @param integer $offset
       * @param integer $limit
       * @param boolean $includeDetails
       * @param boolean $includeThumbnails
       * @param boolean $includeInbox
       * @return array
       */
      static function enum( $offset = 0, $limit = 0, $includeDetails = true, $includeThumbnails = true, $includeInbox = true, $includeShared = false, $year = 0 ) {

         $albumlist = array();

         if( !Login::isLoggedIn() ) return $albumlist;

         $sorttypes = array(
            'created' => 'created_time',
            'title' => 'namn',
            'manual' => 'sorting',
         );

         $sortorders = array(
            'asc',
            'desc',
         );

         // Fetch sort info from session.
         $albumsort = UserSessionArray::getItems( 'albumsort' );

         $sortorder = $albumsort['sort'];
         if( !isset( $sortorder ) ) $sortorder = 'created';

         $order = $albumsort['order'];
         if( !isset( $order ) ) $order = 'desc';

         // Set default sort type if none is found in session.
         if( !array_key_exists( $sortorder, $sorttypes ) ) {

            $sortorder = 'created';

         }

         // Set default sort order if none is found in session.
         if( !in_array( $order, $sortorders ) || !isset( $order ) ) {

            $order = 'desc';

         }

         $sortorder = $sorttypes[$sortorder];

			$collection = new Album();
			//$collection->debug();
			$albumids = array();
			if( $year > 0){
   			foreach( $collection->collection( array( 'aid' ), array( 'uid' => Login::userid(), 'deleted_at' => NULL, 'year' => $year ), $sortorder.' '.$order, $limit, $offset )->fetchAll() as $album ) {
               list( $albumids[] ) = $album;
   			}
			}else{
			  foreach( $collection->collection( array( 'aid' ), array( 'uid' => Login::userid(), 'deleted_at' => NULL ), $sortorder.' '.$order, $limit, $offset )->fetchAll() as $album ) {
               list( $albumids[] ) = $album;
   			} 
			   
			}

			if( $includeInbox ) {
			   $inbox = new Album();
			   $albumlist[] = $inbox->getInbox( $includeDetails, $includeThumbnails );
			}

			if ( $includeShared ) {

			   $queryString = "
			      SELECT
			         aid
			      FROM
			         tilgangtilalbum_dedikert
			      WHERE
			         uid=?
			      ";
			   foreach ( DB::query( $queryString, Login::userid() )->fetchAll() as $ded ) {

			      list( $albumids[] ) = $ded;

			   }

			}

			foreach( $albumids as $albumid ) {
			   try {
               $album = new Album( $albumid );
               $albumlist[] = $album->asArray( $includeDetails, $includeThumbnails );
            } catch( Exception $e ) {}
			}

			return $albumlist;

      }


      public function getInbox( $includeDetails = true, $includeThumbnails = true ) {

         $images = array();
         if( login::isLoggedIn() ) {
            $res = DB::query( "
   			      SELECT
   			         bid
   			      FROM
   			         bildeinfo
   			      WHERE
   			         owner_uid = ? AND
   			         aid IS NULL AND
   			         deleted_at IS NULL AND
   			         quarantined_at IS NULL
   			      ORDER BY sorting, bid
   			   ", Login::userid() );

            $quantity = $res->count();
            while( list( $imageid ) = $res->fetchRow() ) {

               $images[]= $imageid;

            }
         }

         $result["id"] = 0;
         $result["title"] = __( 'Inbox' );
         $result["urlname"] = Util::urlize( $result['title'] );
         $result["numviews"] = 'N/A';
			$result['permission'] = PERMISSION_OWNER;
			$result['identifier'] = null;
			$result['created'] = date( 'Y-m-d H:i:s' );
			$result['ownerid'] = Login::userid();
         $result['owner'] = array(
            'uid' => Login::userid(),
		      'name' => User::getNameFromUid( Login::userid() ),
		      'yours' => true,
		      'preferences' => array(
		         'purchase' => true,
               'download' => true,
               'year'     => date('Y'),
	         ),
         );
         $result['access'] = array(
            'purchase' => true,
            'download' => true,
         );

         if( $includeDetails ) {
				$result['descriptionraw'] = '';
				$result['description'] = '';
				$result['albumurl'] = '/myaccount/album/0/'.$result['urlname'];
				$result['shared'] = array(
				   'link' => false,
				   'password' => false,
				   'public' => false,
				   'groups' => false,
				   'friends' => false,
				   'friendsorgroups' => false,
				);
				$result['galleryurl'] = '';
				$result['sharingurl'] = '';
				$result['isshared'] = false;
		   }

			if( $includeThumbnails ) {

			   $imageList = array();

			   if( $quantity > 0 ) {

   				$result['thumbnail'] = $images[0];
   				$result['defaultimageid'] = $images[0];
   				$result['thumbnailurl'] = "/images/stream/thumbnail/".$images[0];
   				$result["numimages"] = $quantity;

   				if( $quantity < 5 ) {
   			     $max = $quantity;
   				} else {
   			     $max = 5;
   				}

   				if( $max > count( $images ) ) {
   				   $max = count( $images );
   				}

   				$imageList = array();

   				for( $i=0; $i<$max; $i++ ) {
   				   $image = new Image( $images[$i] );
   				   if( $image->isLoaded() && $image instanceof Image ) {
   				      $imageList []= $image->asArray();
   				   }
   				}

			   } else {

			      $result['thumbnail'] = '';
			      $result['defaultimageid'] = 0;
			      $result['thumbnailurl'] = '';
			      $result['numimages'] = 0;

			   }

				$result['defaultthumbnails'] = $imageList;

         }

         return $result;

      }


      /**
      * Get the default image for this album
      *
      * @return integer
      */
      public function getDefaultImageId() {

         $defaultImage = false;
         if ( $this->aid > 0 ) {
            
            try {
               
               if ( !$this->fieldGet( 'default_bid' ) ) {
                  
                  $images = new Image();
                  foreach( $images->collection( 'bid', array( 'aid' => $this->aid, 'deleted_at' => null ), 'dato ASC', 1 )->fetchAllAs('Image') as $image ) {
                     
                     $defaultImage = $image->bid;
                     
                  }
                  
                  $this->fieldSet( 'default_bid', $defaultImage );
                  $this->save();
                  
               } else {
                  
                  $defaultImage = $this->fieldGet( 'default_bid' );

                  try{
                     $image = new Image( $defaultImage );
                  }catch( Exception $e ){
                     $images = new Image();
                     foreach( $images->collection( 'bid', array( 'aid' => $this->aid, 'deleted_at' => null ), 'dato ASC', 1 )->fetchAllAs('Image') as $image ) {
                        $defaultImage = $image->bid;
                     }
                  }
                  
               }
               
            } catch( Exception $e ) {
               
            }

         }

         return $defaultImage;

      }



      public function getThumbnailUrl() {
         
         $expires = time() + 3600;
         $code = base64_encode( $expires
                                 . '|'
                                 . md5( 'yz9987gd'
                                      . $this->uid
                                      . $this->getDefaultImageId()
                                      . $expires
                                      . $_SERVER['REMOTE_ADDR']
                                   )
                                );

         if( $bid = $this->getDefaultImageId() ) {
            
            return sprintf('%s/images/stream/thumbnail/%d',
               WebsiteHelper::rootBaseUrl(),
               $bid
            );
         
         }else{
            return sprintf( 'https://static.repix.no/gfx/icons/empty_folder_icon.jpg', WebsiteHelper::staticBaseUrl());
         }

         return null;

      }



      /**
       * Set the default image of this album
       *
       * @param integer $imageid
       * @return boolean
       *
       */
      public function setDefaultImageId( $imageid = 0 ) {

         if( !empty( $imageid ) ) {

            $image = new Image( $imageid );
            if( !$image->isLoaded() || !$image instanceof Image ) return false;
            
            if( $image->owner_uid == Login::userid() && $image->aid == $this->aid ) {
               
               $this->default_bid = $imageid;
               
               return true;
            }
            
         }

         return false;

      }

      public function listSharedToMe() {

         $albums = new Album();
         $ret = array();
         foreach( DB::query( 'SELECT aid FROM tilgangtilalbum_dedikert WHERE uid=?', Login::userid() )->fetchAll() as $albumid ) {

            try {
               $album = new Album( $albumid );
	       
	       if( $album->uid != 943910 ){
		  $ret[] = $album->asArray();
	       }
   	       
            } catch( Exception $e ) {}

	 }
	    return $ret;

      }
      
      
      public function listReedfoto() {

         $albums = new Album();
         $ret = array();
         foreach( DB::query( 'SELECT aid FROM tilgangtilalbum_dedikert WHERE uid=?', Login::userid() )->fetchAll() as $albumid ) {

            try {
               $album = new Album( $albumid );
	       
	       if( $album->uid == 943910 ){
		  $ret[] = $album->asArray();
	       }
   	       
            } catch( Exception $e ) {}

	 }
	    return $ret;

      }
      
      
      public function enumSharedByMe() {
         
         $albums = new Album();
         $ret = array();
         foreach( DB::query( 'SELECT distinct( aid ) FROM tilgangtilalbum_dedikert WHERE aid IN ( SELECT aid FROM bildealbum WHERE uid = ? AND deleted_at IS NULL )', Login::userid() )->fetchAll() as $albumid ) {

            try {
               $album = new Album( $albumid );
   				$ret[] = $album->asArray();
            } catch( Exception $e ) {}

			}

			return $ret;

      }

      public function removeUserAccess( $userid, $albumid = null ) {

         if ( !isset( $userid ) || !is_numeric( $userid ) ) return false;
         if ( !isset( $albumid ) ) $albumid = $this->aid;
         if ( !isset( $albumid ) || !is_numeric( $albumid ) ) return false;

         $queryString = "
            DELETE FROM
               tilgangtilalbum_dedikert
            WHERE
               aid=? AND
               uid=?
            ";
         DB::query( $queryString, $albumid, $userid );

         return true;

      }
      
      static function fromIdentifier( $identifier ) {
         
         return Album::fromFieldValue( array( 'identifier' => $identifier, 'deleted_at' => null ), 'Album', false );
         
      }
      
      static function idFromIdentifier( $identifier ) {
         
         $album = new Album();
         return (int) $album->collection( array( 'aid' ), array( 'identifier' => $identifier ), null, 1 )->fetchSingle();
         
      }

   }

?>
