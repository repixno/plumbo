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
   
   class APITickSingleImage extends JSONPage implements IView {

      /**
       * Add a image ID to the chosen images-array
       * 
       * @api-name image.tick.single
       * @api-param imageid Integer ID of the image to remove
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
         if( empty( $image ) || !$image instanceof Image || $image->getOwnerId() == 0 ) return false;

         $checkedimages = UserSessionArray::getItems( "tickedimages" );

         if( count( $checkedimages ) > 0 ) {
         
            // Check so this image isn't already in array
            $exists = false;
            foreach( $checkedimages as $checkedimage ) {
               if( $imageid == $checkedimage["id"] ) {
                  $exists = true;
               }
            }
         
            if( !$exists ) {

               $checkedimages []= $image->asArray();
               UserSessionArray::clearItems( "tickedimages" );
               UserSessionArray::addItem( "tickedimages", $checkedimages );
            
            }
            
            // Everything is fine and dandy
            $this->result = true;
            $this->message = "OK";
            $this->images = $checkedimages;
         
         }
         
      }
      
   }


?>