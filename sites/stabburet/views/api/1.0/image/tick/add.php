<?php

   /**
    * 
    * Put an imageid in checked images array
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );
   import( 'session.usersessionarray' );
   
   class APITickSingleImage extends JSONPage implements IValidatedView {

      /**
       * 
       * Add a image ID to the chosen images-array
       * 
       * @api-name image.tick.add
       * @api-param imageid Integer ID of the image to add
       * @api-result result Boolean true/false
       * @api-result images Array List of chosen images
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute( $imageid ) {
         
         $imageid = (int)$imageid;
         $image = new Image( $imageid );
         
         $this->result = false;
         $this->message = "Not a valid image id";
         
         // Check to see if this is a valid image
         if( !$image->isLoaded() || !$image instanceof Image || $image->getOwnerId() == 0 ) return false;

         $choosenImages = UserSessionArray::getItems( "choosenimages" );
         $choosenImages = reset( $choosenImages );
         $choosenImages[$imageid] = 1;
         
         UserSessionArray::clearItems( "choosenimages" );
         UserSessionArray::addItem( "choosenimages", $choosenImages );
         
         $this->result = true;
         $this->images = $chosenImages;
         $this->message = "OK";
         
      }
      
      
      /**
       * Validate the image id as integer
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