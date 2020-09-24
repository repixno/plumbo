<?php

   /**
    * Show images in a shared album
    * 
    * 
    */

   import( 'website.album' );
   import( 'website.userfriend' );
   import( 'website.permissions' );
   import( 'math.zbase32' );

   class SharedAlbumFriend extends WebPage implements IValidatedView {
      
      static $paginationsize = 20;
      
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
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'get' => array(
                  'subscribe' => VALIDATE_INTEGER
               )
            )
         ); 
         
      }
      
      /**
       * Execute
       * 
       * adds friendhash, signature, albumid, mediarss, images, pagination, userloggedin, albumsubscriber to view
       *
       * @param Integer $albumid
       * @param String $friendhash
       * @param String $signature
       * @return Boolean
       */
      
      public function Execute( $albumid = 0, $friendhash = '', $signature = '') {
         
         $this->albumsubscriber = false;
         
         $userloggedin = Login::userid() > 0 ? true : false;
         
         $this->userloggedin = $userloggedin;
         
         try {
         
            $friendid = $this->validateFriendHash( $albumid, $friendhash, $signature );
            
            $friend = new UserFriend( $friendid );
            
            if ( $friend->isLoaded() ) {
            
               $status = Album::getSharingStatus( $albumid, $friendid, 1 );

               if ( $status > 0 ) {

                  if ( $status <= 2 ) {
                     
                     Album::setSharingStatus( $albumid, $friendid, 2 );
                     
                     if ( $userloggedin && ( isset( $_GET['subscribe'] ) ) ) {
                        
                        if ( Album::subscribeToSharedAlbum( $albumid, $friendid ) ) $this->albumsubscriber = true;
                        
                     }
                  
                  } else if ( ( $status > 2 ) && ( $userloggedin ) && ( Album::getSharingUserId( $albumid, $friendid ) == Login::userid() ) ) {
   
                     $this->albumsubscriber = true;
                     
                  }
                  
                  if ( Login::userid() <= 0 )Session::pipe( 'loginredirecturl', sprintf( '/shared/album/%s/%s/%s', $albumid, $friendhash, $signature ) );
                  
                  $this->friendhash = $friendhash;
                  $this->signature = $signature;
                  $this->albumid = $albumid;
                  
                  $this->mediarss = sprintf( '%s/shared/album/rss/%d/%d/%s/%s', WebsiteHelper::rootBaseUrl(), $albumid, $page, $friendhash, $signature );
                  
                  $this->setTemplate( 'shared.album.showalbum' );
                  
                  return $this->fetchPaginatedAlbums( $albumid, $page, 20 );
                  
               } else {
                  
                  relocate( '/' );
                  
               }
            } else {
               
               // is user owner?
               
               $album = new Album( $albumid );
               
               $this->mediarss = sprintf( '%s/shared/album/rss/%d/%d/%s/%s', WebsiteHelper::rootBaseUrl(), $albumid, $page, $friendhash, $signature );
               
               $this->setTemplate( 'shared.album.showalbum' );
               
               return $this->fetchPaginatedAlbums( $albumid, $page, 20 );
               
            }
         
         } catch ( Exception $e ) {
            
            relocate( '/' );
            
         }
            
      }
      
      /**
       * Fetch paginated albums
       *
       * @param Integer $albumid
       * @param Integer $page
       * @param Integer $paginationsize
       */

      private function fetchPaginatedAlbums( $albumid = 0, $page = 1, $paginationsize = 20 ) {

         $albumid = (int) $albumid;
         
         if( $albumid > 0 ) {

            try {

               $imagelist = $this->fetchImageList( $albumid );

               $pages = ceil( count( $imagelist ) / $paginationsize );
               $images = array_splice( $imagelist, ( $page - 1 ) * $paginationsize, $paginationsize );

               $this->images = $images;

               $this->pagination = array(
                  'current'   => $page,
                  'prev'      => $page <= 1 ? '' : $page - 1,
                  'next'      => $page < $pages ? $page + 1 : '',
                  'first'     => 1,
                  'last'      => $pages,
                  'items'     => array(
                     'from'   => ( ( $page - 1 ) * $paginationsize ) + 1,
                     'to'     => ( ( $page - 1 ) * $paginationsize ) + count( $images ),
                     'count'  => count( $images ),
                  ),
               );

            } catch( NoPasswordException $e ) {
       
               $this->setTemplate( 'shared.album.password' );

            }

         } else if( $albumid == 0 ) {

            relocate( '/myaccount/album/0/%s', util::urlize( __( 'Inbox' ) ) );
                  
         }
                  
      }
      
      /**
       * Fetch all images in album
       *
       * @param Integer $albumid
       */
      
      private function fetchAllImagesInAlbum( $albumid = 0  ) {

         $albumid = (int) $albumid;

         if( $albumid > 0 ) {

            try {
               
               $images = $this->fetchImageList( $albumid );
               
               $this->images = images;
               
            } catch( NoPasswordException $e ) {

               $this->setTemplate( 'shared.album.password' );

            }

         } else if( $albumid == 0 ) {

            relocate( '/myaccount/album/0/%s', util::urlize( __( 'Inbox' ) ) );
            
         }
         
      }
      
      /**
       * Fetch image list
       *
       * @param Integer $albumid
       * @return Array
       */
      
      private function fetchImageList( $albumid ) {

         $album = new Album( $albumid );

         $this->album = $album->asArray();
         
         return $album->getImages();
      }
      
      /**
       * Validate friend hash and return friendid
       *
       * @param Integer $albumid
       * @param String $friendhash
       * @param String $signature
       * @return Integer
       */
      
      private function validateFriendHash( $albumid, $friendhash, $signature ) {
         
         $friendid = zBase32::decode( $friendhash );
      
         if ( zBase32::encode( crc32( $friendid ) + 0x100000000 ) == $signature ) {
            
            PermissionManager::current()->grantAccessTo( $albumid, 'album', PERMISSION_SHARED );

            $album = new Album( $albumid );
                
            foreach( $album->listImageIDs() as $imageid ) {
               
               PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_SHARED );
               
            }
            
            return $friendid;
            
         } else {

            return 0;
            
         }
         
      }
      
      /**
       * RSS
       *
       * @param Integer $albumid
       * @param String $publickey
       * @return Boolean
       */
      
      public function rss( $albumid = 0, $friendhash = '', $signature = '' ) {

         header( 'Content-type: text/xml; charset=UTF-8' );
         $this->setTemplate( 'shared.album.showalbum-rss' );
         
         $friendid = $this->validateFriendHash( $albumid, $friendhash, $signature );
         
         if ( $friendid > 0 ) {

            return $this->fetchAllImagesInAlbum( $albumid );
            
         } else {
            
            return null;
            
         }

      }
      
      /**
       * Slideshow
       *
       * @param Integer $albumid
       * @param String $publickey
       * @return Boolean
       */
      
      public function slideshow( $albumid = 0, $friendhash = '', $signature = '' ) {

         $this->setTemplate( 'shared.album.slideshowpro' );
         
         $friendid = $this->validateFriendHash( $albumid, $friendhash, $signature );
         
         if ( $friendid > 0 ) {

            return $this->fetchAllImagesInAlbum( $albumid );
            
         } else {
            
            return null;
            
         }

      }
      
      /**
       * Slideshow parameters
       *
       * @param Integer $albumid
       * @param String $publickey
       * @return Boolean
       */

      public function slideshowparameters( $albumid = 0, $friendhash = '', $signature = '' ) {

         $this->setTemplate( 'shared.album.slideshowpro-parameters' );
   
         $friendid = $this->validateFriendHash( $albumid, $friendhash, $signature );
         
         if ( $friendid > 0 ) {

            return $this->fetchAllImagesInAlbum( $albumid );
            
         } else {
            
            return null;
            
         }
         
      }
      
      /**
       * Popup
       *
       * @param Integer $albumid
       * @param String $publickey
       * @return Boolean
       */

      public function popup( $albumid = 0, $friendhash = '', $signature = '' ) {

         $this->setTemplate( 'shared.album.show_album_popup' );
         
         $friendid = $this->validateFriendHash( $albumid, $friendhash, $signature );
         
         if ( $friendid > 0 ) {

            return $this->fetchAllImagesInAlbum( $albumid );
            
         } else {
            
            return null;
            
         }

      }

      
   }
   
?>
