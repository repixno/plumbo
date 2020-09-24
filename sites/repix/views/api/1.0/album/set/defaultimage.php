<?php

   /**
    * 
    * Set the default image of an album
    * 
    * @author Eivind Moland <eivind@intelesms.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumSetDefaultImage extends JSONPage implements IValidatedView {
      
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
                  'imageid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'imageid' => VALIDATE_INTEGER,
               ),
            ),
            
         );
         
      }      
      
      /**
       * Sets the default image/thumbnail for a given album
       * 
       * @api-name album.set.defaultimage
       * @api-auth required
       * @api-post-optional albumid Integer ID of the album to set default image for
       * @api-post-optional imageid Integer The image ID to set as default image for the selected album
       * @api-param-optional albumid Integer ID of the album to set default image for
       * @api-param-optional imageid Integer The image ID to set as default image for the selected album
       * @api-result imageid String The provided image ID
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute( $albumid = 0, $imageid = 0 ) {
         
         $albumid = isset( $_POST['albumid'] ) ? (int)$_POST["albumid"] : $albumid;
         $imageid = isset( $_POST['imageid'] ) ? (int)$_POST["imageid"] : $imageid;

         $this->result = false;
         $this->message = "Image id is missing";
         if( $imageid <= 0 ) return false;
         
         try {
            
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
            
            // Save the default image
            $this->result = false;
            $this->message = 'Image not in album';
            if ( !$album->setDefaultImageId( $imageid ) ) return false;
            
            $album->save();
            
            $this->imageid = $imageid;
            
            $this->result = true;
            $this->message = 'OK';

          
         } catch (Exception $ex) {
            
            $this->result = false;
            $this->message = sprintf( 'Error: %s',$ex );
            
         }
         
      }
      
   }

?>