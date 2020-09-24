<?php

   /**
    * Move images to new album
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );

   class APIImagesMove extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "post" => array(
                  "albumid" => VALIDATE_INTEGER,
                  "images" => VALIDATE_STRING,
               ),
               "get" => array(
                  "albumid" => VALIDATE_INTEGER,
                  "images" => VALIDATE_STRING,
               ),
            )
         );
         
      }

      /**
       * Move a list of images to a new album
       * 
       * @api-name images.move
       * @api-post-optional images Array List of image ID's to move
       * @api-post-optional albumid Integer Album ID to move images to
       * @api-get-optional images Array List of image ID's to move
       * @api-get-optional albumid Integer Album ID to move images to
       * @api-result images Array List of moved images
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */          
      public function Execute() {

         
         $albumid = (int)$_POST["albumid"];
         $images = $_POST["images"];
         
         // Check album id
         $this->result = false;
         $this->message = "No albumid given";
         if( empty( $albumid ) ) return false;
         
         // Check images given
         $this->result = false;
         $this->message = "No images given";
         if( empty( $images ) ) return false;
         
         // Check if any images in string
         $imagearray = explode( ',', $images );
         $imagesToMove = array();
         
         if( count( $imagearray ) > 0 ) {
            
            foreach( $imagearray as $imageid ) {
               
               $imageid = (int)$imageid;
               if( !empty( $imageid ) ) {
                  $imagesToMove []= $imageid;
               }
               
            }
            
         }

         
         // Is this a valid album?
         $this->result = false;
         $this->message = "Not a valid album";
         $album = new Album( $albumid );
         if( !$album->isLoaded() || !$album instanceof Album ) return false;   
         
         // Is the album the users album?
         $this->result = false;
         $this->message = "Not users album.";
         if( $album->ownerid != Login::userid() ) return false;
         
         // Are there any images to be moved
         $this->result = false;
         $this->message = "No valid images";
         if( !count( $imagesToMove ) > 0 ) return false;
         
         $movedImages = array();
   	   $image = new Image();
   	   
   	   // Get all images in collection
   		foreach( $image->collection( 'bid', array( 'owner_uid' => Login::userid(), 'bid' => array( 'IN', $imagesToMove ) ) )->fetchAllAs('Image') as $image ) {
   		   
   		   if( $image->isLoaded() && $image instanceof Image ) {
   		      // Is this the users image?
      		   if( $image->ownerid == Login::userid() && $albumid != $image->aid ) {
   	        
      		      $movedImages []= $image->asArray();
   	     	      $image->aid = $albumid;
   	     	      $image->save();
                     
      		   }
               
            }
            
   		}
         
         $this->result = true;
         $this->images = $movedImages;
         $this->message = "OK";
         
      }
      
   }

?>