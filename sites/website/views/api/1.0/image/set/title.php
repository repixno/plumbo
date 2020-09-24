<?php

   /**
    * Set new title of an image
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );

   class APIImagesSetTitle extends JSONPage implements IValidatedView {
      
      
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
                  'title' => VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      
      /**
       * Sets title on a image
       * 
       * @api-name image.set.title
       * @api-auth required
       * @api-post imageid Integer ID of the image to set description on
       * @api-post title String The title to set
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */     
      public function Execute() {
         
         $id = (int)$_POST["imageid"];
         $title = $_POST["title"];

         $this->result = false;
         $this->message = "Title missing";
         if( !strlen( $title ) > 0 ) return false;
         
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
         
         // Everythings fine and dandy. Save new title
         $image->title = $title;
         $image->save();
         
         $this->result = true;
         $this->message = "Updated image title";
         $this->title = $image->title;
         
      }
      
   }


?>