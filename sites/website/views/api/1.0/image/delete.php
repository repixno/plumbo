<?php

   /**
    * API for deleting a single image
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );

   class APIImageDelete extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "post"   => array(
                  "imageid" => VALIDATE_INTEGER,
               ),
               "fields" => array(
                  "imageid"   => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      

      /**
       * Deletes a given image belonging to the logged in user.
       * 
       * @api-name image.delete
       * @api-post-optional imageid Integer ID of the image to delete
       * @api-param-optional imageid Integer ID of the image to delete
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute( $imageid = 0 ) {
         
         if( isset( $_POST['imageid'] ) ) {
            $imageid = $_POST['imageid'];
         }
         
         $this->result = false;
         $this->message = "Failed integer verification";
         if( empty( $imageid ) ) return false;

         // Try loading image etc
         try{
            
            $image = new Image( $imageid );
            if( !$image->isLoaded() || !$image instanceof Image ) return false;
            
            $this->result = false;
            $this->message = "No access to this image";
            
            // User has no access to this image
            // Needs to be user's own image
            if( $image->getOwnerId() != Login::userid() ) return false;

            // Try getting a time for deletion
            $deleted = $image->deleted();
            
            // Not previously deleted. Do it.
            if( empty( $deleted ) ) {
               
               $this->result = true;
               $this->message = "OK";
               
               $image->delete();
               
            } else { // Already deleted
               
               $this->result = false;
               $this->message = "Image already deleted";
               
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = "Failed to load image";
            
         }
         
      }
      
   }


?>