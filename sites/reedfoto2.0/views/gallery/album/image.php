<?php

   /**
    * Single Image view
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.image' );
   import( 'website.album' );

   class GalleryAlbumImage extends WebPage implements IValidatedView {
      
      protected $template = 'gallery.album.showimage';
      
      public function Validate() {
         
         return array(
            "execute"   => array(
               "request"   => array(
                  "bid"       => VALIDATE_INTEGER,
               ),
               "fields" => array(
                  "bid"    => VALIDATE_INTEGER, 
               )
            )
         
         );
         
      }
      
      
      public function Execute( $bid = 0 ) {
         
         $image = new Image( $bid );
         
         if( empty( $image ) || !$image instanceof Image ) return false;
         
         $this->image = $image->asArray();
         
         $album = new Album( $image->aid );
         $images = $album->getImages();
         
         $imageids = array();
         foreach( $images as $image ) {
            $imageids[] = $image['id'];
         }
         
         $position = array_search( $bid, $imageids );
         
         $this->imagenumber = $position + 1;
         $this->imagecount = count( $imageids );
         
         if( $position > 0 ) {
            try {
               $previmage = new Image( $imageids[$position - 1] );
               $this->previmage = $previmage->asArray();
            } catch( Exception $e ) {}
         }
         
         if( $position < count( $imageids ) - 1 ) {
            try {
               $nextimage = new Image( $imageids[$position + 1] );
               $this->nextimage = $nextimage->asArray();
            } catch( Exception $e ) {}
         }
         
      }
      
      
   }



?>