<?php


   /**
    * 
    * Set the description of an album
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumSetDescription extends JSONPage implements IValidatedView {
      
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
                  'description' => VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      /**
       * Sets the description for a given album
       * 
       * @api-name album.set.description
       * @api-auth required
       * @api-post-optional albumid Integer ID of the album to update description for
       * @api-post-optional description String The description to set
       * @api-result description String The description to set
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {
         
         $albumid = (int)$_POST['albumid'];
         $description = $_POST['description'];

         // Try creating image object
         $album = new Album( $albumid );
         
         // Check if object loaded
         $this->result = false;
         $this->message = 'Failed to load album';
         if( !$album->isLoaded() || !$album instanceof Album ) return false;
         
         // Check image ownership
         $this->result = false;
         $this->message = 'Not album owner';
         if( $album->getOwnerId() != Login::userid() ) return false;
         
         // Everythings fine and dandy. Save new description
         $album->description = $description;
         $album->save();
         
         $this->result = true;
         $this->description = $album->description;
         $this->message = 'OK';
         
      }
      
   }


?>