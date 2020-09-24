<?php

   /**
    * Wrapper class for EF < 2.8 and
    * images uploaded without being 
    * logged in
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */


   import( 'website.image' );
   import( 'website.album' );
   
   class UploadedImagesArray {
      
      /**
       * Get all uploaded images
       *
       * @return array
       */
      static function get() {
         
         $uploadedImages = array();
         
         if( count( $_SESSION["client_info"]["uploaded_images"] ) > 0 ) {
            
            $uploadedImages = array_keys( $_SESSION["client_info"]["uploaded_images"] );
            
         }
         
         return $uploadedImages;
         
      }
      
      
      /**
       * return the original format of 
       * the array
       *
       * @return array
       * 
       */
      static function getOriginal() {
         
         return $_SESSION["client_info"]["uploaded_images"];
         
      }
      
      
      
      /**
       * Add an image
       *
       * @param integer $imageId
       */
      static function add( $imageid ) {
      
         $_SESSION["client_info"]["uploaded_images"][$imageid] = 1;
         
      }
      
      
      /**
       * Clear all uploaded images
       *
       */
      static function clear() {
         
         unset( $_SESSION["client_info"]["uploaded_images"] );
         unset( $_SESSION["client_info"]["upload_order"]["this_session"] );
         unset( $_SESSION["client_info"]["upload"]["this_session"] );
         
      }
      
      
      /**
       * Get the number of images in array
       *
       * @return integer
       * 
       */
      static function count() {
         
         return count( $_SESSION["client_info"]["uploaded_images"] );
         
      }
      

      /**
       * Get comma separated string
       *
       * @return string
       */
      static function getAsString() {
         
         $uploadedImages = UploadedImagesArray::get();
         return implode( ',', $uploadedImages );
         
      }
      
      
      
      /**
       * Move images to user's own account and album
       *
       * @return boolean
       */
      static function move() {
         
         if( UploadedImagesArray::count() > 0 ) {

            $album = new Album();
            
            $album->ownerid = Login::userid();
            $album->title = date( 'Y-m-d H:i' );
            $album->save();
            
            $res = DB::query( '
               UPDATE
                  bildeinfo 
               SET 
                  aid = ?,
                  owner_uid = ?
               WHERE 
                  aid IS NULL AND 
                  owner_uid = ? AND 
                  bid IN( '.UploadedImagesArray::getAsString().' )
               ',
               $album->aid, 
               Login::userid(), 
               61224 
            );
            
            foreach( UploadedImagesArray::get() as $imageid ) {
               Image::deleteFromObjectCacheByClassAndId( 'image', $imageid );
            }
            
            UploadedImagesArray::clear();
            
            return true;
            
         }
         
         return false;
         
      }
      
   }


?>