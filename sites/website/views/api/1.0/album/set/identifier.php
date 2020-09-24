<?php


   /**
    * 
    * Set the identifier of an album
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumSetIdentifier extends JSONPage implements IValidatedView {
      
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
                  'identifier' => VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      /**
       * Sets the description for a given album
       * 
       * @api-name album.set.identifier
       * @api-auth required
       * @api-post-optional albumid Integer ID of the album to update identifier for
       * @api-post-optional identifier String The identifier to set
       * @api-result description String The description to set
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {
         
         $albumid = (int)$_POST['albumid'];
         $identifier = $_POST['identifier'];

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
         
         // Everythings fine and dandy. Save new identifier
         $album->identifier = $identifier;
         $album->save();
         
         $this->result = true;
         $this->identifier = $album->identifier;
         
         $this->message = 'OK';
         
      }
      
   }


?>