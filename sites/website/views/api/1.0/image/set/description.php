<?php

   /**
    * Set new description of an image
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );

   class APIImagesSetDescription extends JSONPage implements IValidatedView {
      
      
      /**
       * Validate the input
       *
       * @return unknown
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
                  'description' => VALIDATE_STRING,
               ),
            ),
         );
         
      }
         

      /**
       * Sets description on a image
       * 
       * @api-name image.set.description
       * @api-auth required
       * @api-post imageid Integer ID of the image update description on
       * @api-post description String The description to set
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */     
      public function Execute() {
         
         $id = (int)$_POST["imageid"];
         $description = $_POST["description"];
         
         // Try creating image object
         $image = new Image( $id );
         
         // Check if object loaded
         $this->result = false;
         $this->message = "Failed to load image";
         if( !$image->isLoaded() || !$image instanceof Image ) return false;
         
         // Check image ownership
         $this->result = false;
         $this->message = "Not image owner";
         
         if( $image->getOwnerId() != Login::userid() ) return false;
         
         // Everythings fine and dandy. Save new description
         $image->setDescription( $description );
         $image->save();
         
         $this->description = $description;
         
         $this->result = true;
         $this->message = "OK";
         
      }
      
   }


?>