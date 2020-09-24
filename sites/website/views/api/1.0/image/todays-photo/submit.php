<?php

   /**
    * API for submitting a image as todays photo
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   
   import( 'pages.json' );
   import( 'website.image' );

   class APIImageTodaysPhotoSubmit extends JSONPage implements IValidatedView {
      
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
       * Submit image as todays photo
       * 
       * @api-name image.todays-photo.submit
       * @api-post-optional imageid Integer ID of the image to submit
       * @api-param-optional imageid Integer ID of the image to submit
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
            
            // ...
            DB::query( 'INSERT INTO forslag_dagens (bid, uid) VALUES (?, ?);', $imageid, $image->getOwnerId() );
            
            $this->result = false;
            $this->message = "OK";
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = "Failed to load image";
            
         }
         
      }
      
   }


?>