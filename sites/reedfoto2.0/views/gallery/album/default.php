<?php

   /**
    * Show images in a public album
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.galleryalbum' );
   import( 'website.album' );
   import( 'website.image' );

   class PublicAlbumDefault extends WebPage implements IValidatedView {
      
      static $paginationsize = 20;
      
      protected $template = 'gallery.album.showalbum';
      
      public function Execute( $aid = 0, $albumname = '', $page = 1 ) {
         
         $this->mediarss = sprintf( '%s/gallery/album/rss/%d/%s/%d', WebsiteHelper::rootBaseUrl(), $aid, $albumname, $page );
         
         return $this->fetchPaginatedAlbums( $aid, $page, $publickey );
         
      }
      
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
                  VALIDATE_STRING,
               ),
            ),
            'slideshow' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               ),
            ),
            'slideshowparameters' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               ),
            ),
            'popup' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      public function rss( $aid = 0, $albumname = '' ) {
         
         header( 'Content-type: text/xml; charset=UTF-8' );
         $this->setTemplate( 'gallery.album.showalbum-rss' );

         return $this->fetchAllImagesInAlbums( $aid );
         
      }
      
      public function slideshow( $aid = 0, $albumname = '' ) {
         
         $this->setTemplate( 'gallery.album.slideshowpro' );

         return $this->fetchAllImagesInAlbums( $aid );
         
      }
      
      public function slideshowparameters( $aid = 0, $albumname = '' ) {
         
         $this->setTemplate( 'gallery.album.slideshowpro-parameters' );
         
         return $this->fetchAllImagesInAlbums( $aid );
         
      }
      
      public function popup( $aid = 0, $albumname = '' ) {
         
         $this->setTemplate( 'gallery.album.show_album_popup' );
         
         return $this->fetchAllImagesInAlbums( $aid );
         
      }
      
      private function fetchAllImagesInAlbums( $aid ) {
         
         $aid = (int) $aid;
         if( $aid > 0 ) {
            
            try {
               
               $album = new GalleryAlbum( $aid );
               
            } catch( Exception $e ) {
               
               $album = new Album( $aid );
               
               relocate( '/myaccount/album/%d/%s', $aid, util::urlize( $album->title ) );
               
            }
            
            $this->album = $album->asArray();
            $this->images = $album->getImages();
            
         } else if( $aid == 0 ) {
            
            relocate( '/myaccount/album/0/%s', util::urlize( __( 'Inbox' ) ) );
            
         }
         
         
      }
      
      private function fetchPaginatedAlbums( $aid = 0, $page = 1 ) {
         
         $aid = (int) $aid;
         if( $aid > 0 ) {
            
            try {
               
               $album = new GalleryAlbum( $aid );
               
            } catch( Exception $e ) {
               
               $album = new Album( $aid );
               
               relocate( '/myaccount/album/%d/%s', $aid, util::urlize( $album->title ) );
               
            }
            
            $this->album = $album->asArray();
            $imagelist = $album->getImages();
            
            $pages = ceil( count( $imagelist ) / PublicAlbumDefault::$paginationsize );
   		   $images = array_splice( $imagelist, ( $page - 1 ) * PublicAlbumDefault::$paginationsize, PublicAlbumDefault::$paginationsize );
   		   
   		   $this->images = $images;
   		   
   		   $this->pagination = array(
               'current' => $page,
   		      'prev' => $page <= 1 ? '' : $page - 1,
   		      'next' => $page < $pages ? $page + 1 : '',
   		      'first' => 1,
   		      'last' => $pages,
   		      'items' => array(
                  'from' => ( ( $page - 1 ) * PublicAlbumDefault::$paginationsize ) + 1,
                  'to' => ( ( $page - 1 ) * PublicAlbumDefault::$paginationsize ) + count( $images ),
                  'count' => count( $images ),
   		      ),
   		   );
            
         } else if( $aid == 0 ) {
            
            relocate( '/myaccount/album/0/%s', util::urlize( __( 'Inbox' ) ) );
            
         }
         
      }
      
   }
   
?>