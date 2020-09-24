<?php


   /**
    * 
    * Set the title of an album
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumSetTitle extends JSONPage implements IValidatedView {
      
      /**
       * Validate the input
       *
       * @return unknown
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'title' => VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      
      /**
       * Sets the title for a given album
       * 
       * @api-name album.set.title
       * @api-auth required
       * @api-post-optional albumid Integer ID of the album to update title on
       * @api-post-optional title Integer The title to set
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {
         
         $id = (int)$_POST["albumid"];
         $title = $_POST["title"];

         $this->result = false;
         $this->message = "Title missing";
         if( !strlen( $title ) > 0 ) return false;
         
         // Try creating image object
         $album = new Album( $id );
         
         // Check if object loaded
         $this->result = false;
         $this->message = "Failed to load album";
         if( !$album->isLoaded() || !$album instanceof Album ) return false;
         
         // Check image ownership
         $this->result = false;
         $this->message = "Not album owner";
         if( $album->getOwnerId() != Login::userid() ) return false;
         
         // Everythings fine and dandy. Save new title
         $album->title = $title;
         $album->save();

         $this->result = $album->title;
         $this->message = "OK";
         
      }
      
   }


?>