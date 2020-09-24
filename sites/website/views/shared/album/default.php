<?php

   /**
    * Show images in a shared album
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    * 
    */

   import( 'website.album' );
   import( 'website.image' );

   class SharedAlbumDefault extends WebPage implements IValidatedView {

      static $paginationsize = 20;
      
      protected $template = 'shared.album.showalbum';
      
      public function Execute( $aid = 0, $albumname = '', $page = 1, $publickey = '' ) {
         
         $this->mediarss = sprintf( '%s/shared/album/rss/%d/%s/%d/%s', WebsiteHelper::rootBaseUrl(), $aid, $albumname, $page, $publickey );
         
         return $this->fetchPaginatedAlbums( $aid, $page, $publickey );
         
      }
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'albumpassword' => VALIDATE_STRING,
               ),
            ),
            'rss' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'albumpassword' => VALIDATE_STRING,
               ),
            ),
            'slideshow' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'albumpassword' => VALIDATE_STRING,
               ),
            ),
            'slideshowparameters' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'albumpassword' => VALIDATE_STRING,
               ),
            ),
            'popup' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'albumpassword' => VALIDATE_STRING,
               ),
            )
         );
         
      }
      
      public function rss( $aid = 0, $albumname = '', $publickey = '' ) {
         
         header( 'Content-type: text/xml; charset=UTF-8' );
         $this->setTemplate( 'shared.album.showalbum-rss' );
         
         return $this->fetchAllImagesInAlbum( $aid, $publickey );
         
      }
      
      public function slideshow( $aid = 0, $albumname = '', $publickey = '' ) {
         
         $this->setTemplate( 'shared.album.slideshowpro' );
         
         return $this->fetchAllImagesInAlbum( $aid, $publickey );
         
      }
      
      public function slideshowparameters( $aid = 0, $albumname = '', $publickey = '' ) {
         
         $this->setTemplate( 'shared.album.slideshowpro-parameters' );
         
         return $this->fetchAllImagesInAlbum( $aid, $publickey );
         
      }
      
      public function popup( $aid = 0, $albumname = '', $publickey = '' ) {
         
         $this->setTemplate( 'shared.album.show_album_popup' );
         
         return $this->fetchAllImagesInAlbum( $aid, $publickey );
         
      }
      
      private function fetchImageList( $aid ) {
         
         $album = new Album( $aid );
         
         $this->album = $album->asArray();
         return $album->getImages();
         
      }
      
      function signWithOldKey( $data, $twofactorkey = '' ) {
         
         $signkey = 'f25c884e-0cf1-469b-b071-ae38ad025c34';
         
         return md5( $data . $signkey . $twofactorkey );
         
      }
      
      private function validatePublicKey( $aid, $publickey ) {
         
         if( isset( $_POST['albumpassword'] ) ) {
            
            PermissionManager::current()->addTokenFor( $aid, 'album', $_POST['albumpassword'] );
            
         }
         
         if( !empty( $publickey ) ) {
            
   		   import( 'math.signer' );
            
            $signature = Signer::sign( $aid, 'share' );
            $oldsignature = $this->signWithOldKey( $aid, 'share' );
            
            $access = DB::query( 'SELECT access FROM bildealbum WHERE aid = ? AND deleted_at IS NULL', $aid )->fetchSingle();
            if( $access == 2 && ( $signature == $publickey || $oldsignature == $publickey ) ) {
               
               PermissionManager::current()->grantAccessTo( $aid, 'album', PERMISSION_SHARED );
               
               $album = new Album( $aid );
               foreach( $album->listImageIDs() as $imageid ) {
                  
                  PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_SHARED );
                  
               }
               
            }
            
         }
         
      }
      
      private function fetchAllImagesInAlbum( $aid = 0 , $publickey = '' ) {
         
         $aid = (int) $aid;
         
         if( $aid > 0 ) {
            
            try {
               
               $this->validatePublicKey( $aid, $publickey );
      		   $this->images = $this->fetchImageList( $aid );
               
            } catch( NoPasswordException $e ) {
               
               $this->setTemplate( 'shared.album.password' );
               
            }
            
         } else if( $aid == 0 ) {
            
            relocate( '/myaccount/album/0/%s', util::urlize( __( 'Inbox' ) ) );
            
         }
         
      }
      
      private function fetchPaginatedAlbums( $aid = 0, $page = 1, $publickey = '' ) {
         
         $aid = (int) $aid;
         
         if( $aid > 0 ) {
            
            try {
               
               $this->validatePublicKey( $aid, $publickey );
               
               $imagelist = $this->fetchImageList( $aid );
               
               $pages = ceil( count( $imagelist ) / SharedAlbumDefault::$paginationsize );
      		   $images = array_splice( $imagelist, ( $page - 1 ) * SharedAlbumDefault::$paginationsize, SharedAlbumDefault::$paginationsize );
      		   
      		   $this->images = $images;
      		   
      		   $this->pagination = array(
                  'current' => $page,
      		      'prev' => $page <= 1 ? '' : $page - 1,
      		      'next' => $page < $pages ? $page + 1 : '',
      		      'first' => 1,
      		      'last' => $pages,
      		      'items' => array(
                     'from' => ( ( $page - 1 ) * SharedAlbumDefault::$paginationsize ) + 1,
                     'to' => ( ( $page - 1 ) * SharedAlbumDefault::$paginationsize ) + count( $images ),
                     'count' => count( $images ),
      		      ),
      		   );
               
            } catch( NoPasswordException $e ) {
               
               $this->setTemplate( 'shared.album.password' );
               
            }
            
         } else if( $aid == 0 ) {
            
            relocate( '/myaccount/album/0/%s', util::urlize( __( 'Inbox' ) ) );
            
         }
         
      }
      
   }
   
?>