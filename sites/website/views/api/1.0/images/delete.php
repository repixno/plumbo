<?php

   /**
    * 
    * Set a collection of images as deleted
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );

   class APIImagesDelete extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "post" => array(
                  "images" => VALIDATE_STRING,
               ),
            )
         );
         
      }

      /**
       * Deletes a list of images belonging to the logged in user.
       * 
       * @api-name images.delete
       * @api-post images Array List of image ID's to delete
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */           
      public function Execute() {
         
         $this->result = false;
         $this->message = "No images given";
         
         if( empty( $_POST["images"] ) ) return false;
         
         // Check if any images in string
         $imagearray = explode( ',', $_POST["images"] );
         if( count( $imagearray ) > 0 ) {
            
            foreach( $imagearray as $imageid ) {
               
               $imageid = (int)$imageid;
               if( !empty( $imageid ) ) {
                  $imagesToDelete []= $imageid;
               }
               
            }
            
         }
         
         
         $this->result = false;
         $this->message = "No valid images";

         if( !count( $imagesToDelete ) > 0 ) return false;
         
         $imagesToDeleteString = implode( ',', $imagesToDelete );
         $deletedImages = array();
   	   $images = new Image();
   	   
   		foreach( $images->collection( 'bid', array( 'uid' => Login::userid(), 'bid' => array( 'IN', $imagesToDeleteString ) ) )->fetchAllAs('Image') as $image ) {
   		   
   		   if( $image->getOwnerId() == Login::userid() ) {
   		      
   		      if( !$image->deleted() ) {
                  
   		         $image->delete();
   		         
               }
               
   		   }
               
         }
         
         $this->result = true;
         $this->message = "OK";
         
      }
      
   }


?>