<?php

   /**
    * 
    * Get publicly shared albums
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.galleryalbum' );
   config( 'website.gallery' );
   
   class GalleryDefault extends WebPage implements IView {
      
      protected $template = 'gallery.index';
      
      static $paginationsize = 20;
		
      public function Execute( $page = 1 ) {
         
         $albums = new GalleryAlbum();
         $albumlist = array();

         $portal = Dispatcher::getPortal();
         $portalkeymap = Settings::get( 'gallery', 'portalkeymap', array() );
         $portalkey = isset( $portalkeymap[$portal] ) ? $portalkeymap[$portal] : $portalkeymap['default'];
         
         $numalbums = $albums->collection( array( 'aid' ), array( 'uid' => array( '!=', 0 ), 'deleted_at' => null, 'key' => $portalkey ) )->count();
         foreach( $albums->collection( array( 'aid' ), array( 'uid' => array( '!=', 0 ), 'deleted_at' => null, 'key' => $portalkey ), 'publicshare_time DESC', GalleryDefault::$paginationsize, ( $page - 1 ) * GalleryDefault::$paginationsize )->fetchAll() as $row ) {
            
            list( $albumid ) = $row;
            
            $album = new GalleryAlbum( $albumid );
            $albumlist[] = $album->asArray();
            
         }
         
         $pages = ceil( $numalbums / GalleryDefault::$paginationsize );
		   
         $this->pagination = array(
            'current' => $page,
		      'prev' => $page <= 1 ? '' : $page - 1,
		      'next' => $page < $pages ? $page + 1 : '',
		      'first' => 1,
		      'last' => $pages,
		      'items' => array(
               'from' => ( ( $page - 1 ) * GalleryDefault::$paginationsize ) + 1,
               'to' => ( ( $page - 1 ) * GalleryDefault::$paginationsize ) + count( $albumlist ),
               'count' => count( $albumlist ),
		      ),
		   );
		   
         $this->albums = $albumlist;
         
      }
      
      
   }


?>