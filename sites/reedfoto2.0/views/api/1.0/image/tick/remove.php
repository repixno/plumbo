<?php

   /**
    * 
    * Remove an imageid in checked images array
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'pages.json' );
   import( 'session.usersessionarray' );

   class APIImagesTickRemove extends JSONPage implements IValidatedView {

      /**
       * Remove a image ID from the chosen images-array
       * 
       * @api-name image.tick.remove
       * @api-param-optional imageid Integer ID of the image to remove
       * @api-post-optional imageid Integer ID of the image to remove
       * @api-result result Boolean true/false
       * @api-result images Array List of chosen images
       * @api-result message String Describes the result of the operation in US English
       */   
      public function Execute( $imageId ) {
         
         $images = UserSessionArray::getItems( "choosenimages" );
         $images = reset( $images );
         
         if( isset( $images[$imageId] ) ) {
            
            unset( $images[$imageId] );
            $this->images = $images;
            $this->result = true;
            $this->message = "OK";
            
         } else {
            
            $this->images = $images;
            $this->result = false;
            $this->message = "Image not previously ticked";
            
         }
         
         UserSessionArray::clearItems( "choosenimages" );
         UserSessionArray::addItem( "choosenimages", $images );
         
      }
      
      
      /**
       * Validate image id as integer
       *
       * @return unknown
       */
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
           
         
   }


?>