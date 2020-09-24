<?php

   /**
    * Delete one or more of user's albums
    * 2009-08-29
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumsDelete extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "post" => array(
                  "albums" => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      /**
       * Delete one or more of user's albums
       * 
       * @api-name albums.delete
       * @api-auth required
       * @api-example
       * @api-post-optional albums String Comma-separated list of album ID's
       * @api-param-optional albums String Comma-separated list of album ID's
       * @api-result deleted Integer Number of deleted albums
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */       
      public function Execute() {
         
         $albums = $_POST["albums"];
         
         
         // Check for albums
         $this->result = false;
         $this->message = "Required input parameter missing or invalid (albums)";
         
         if( empty(  $albums ) ) return false;
         $albumArray = explode( ',', $albums );
         $albumsToDelete = array();
         
         if( count( $albumArray ) == 0 ) return false;
         
         foreach( $albumArray as $albumId ) {
            
            $tmpAid = (int)$albumId;
            if( !empty( $tmpAid ) ) {
               $albumsToDelete []= $tmpAid;
               unset( $tmpAid );
            }
            
         }
         
         if( count( $albumsToDelete ) == 0 ) return false;
         $albumsToDeleteString = implode( ',', $albumsToDelete );
         $numAlbumsDeleted = 0;
         
         
         // OK - we've got us some albums. Try deleting them.
         try {
            
            $albums = new Album();
            
            // Loop through all albums and set them as deleted, 
            // go through all images in album and set them as deleted.
            foreach( $albums->collection( 'aid', array( 'uid' => Login::userid(), 'aid' => array( 'IN', $albumsToDelete ), 'deleted_at' => null ) )->fetchAllAs('Album') as $album ) {

			      if( $album->uid == Login::userid()  ) {
			         
			         $album->delete();
			         
			         $numAlbumsDeleted++;
			         
			         // Delete all images in this album
			         $images = new Image();
			         foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $album->aid, 'deleted_at' => null ) )->fetchAllAs('Image') as $image ) {
                     
			            if( $image->ownerid == Login::userid() ) {
			               $image->delete();
			            }
			            
			         }
			         
			      }
			      
            }
			   
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = "Unable to delete albums";
            return false;
            
         }
         
         $this->result = true;
         $this->deleted = $numAlbumsDeleted;
         $this->message = "OK";
         
      }
      
   }
   
?>