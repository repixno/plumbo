<?php

   /**
    * Delete a single album
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );

   class APIAlbumDelete extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            "execute" => array(
               "post" => array(
                  "albumid" => VALIDATE_INTEGER,
               ),
               "fields" => array(
                  VALIDATE_INTEGER,
               )
            )
         );
           
      }
      
      /**
       * Deletes a given album belonging to the logged in user.
       * 
       * @api-name album.delete
       * @api-post-optional albumid Integer ID of the album to delete
       * @api-param-optional albumid Integer ID of the album to delete
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $id = 0 ) {
         
         if( empty( $id ) ) {
            $id = (int) $_POST["albumid"];
         }
         
         
         $this->result = false;
         $this->message = "No album id given";
         if( empty( $id ) ) return false;
         
         try {
            
            $album = new Album( $id );
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = "No such album or no access to this album";
            return false;
            
         }
         
         $this->result = false;
         $this->message = "Failed to load album";
         if( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;
         
         $this->result = false;
         $this->message = "No access to this album";
         if( $album->ownerid != Login::userid() ) return false;
         
         $this->result = false;
         $this->message = "Album already deleted";
         $deletedTime = $album->deleted;
         if( !empty( $deletedTime ) ) return false;

         // Delete album and all images within it
         $album->delete();
         $images = new Image();
			foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $album->aid ) )->fetchAllAs('Image') as $image ) {
            
			   $imageDeletedTime = $image->deleted();
			   if( $image->ownerid == Login::userid() && empty( $imageDeletedTime ) ) {
			      $image->delete();
			   }
			   unset( $imageDeletedTime );
			      
			}
			
         $this->result = true;
         $this->message = "Album deleted";
         return true;
         
      }
      
   }


?>