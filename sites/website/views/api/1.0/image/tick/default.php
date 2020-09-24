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
   
   class APITickImage extends JSONPage implements IValidatedView {
      
      
      /**
       * Add a image ID to the chosen images-array
       * 
       * @api-name image.tick
       * @api-param imageid Integer ID of the image to add
       * @api-result result Boolean true/false
       * @api-result images Array List of chosen images
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute( $id = 0 ) {
         
         if( isset( $_POST['imageid'] ) ) {
            $id = (int) $_POST['imageid'];
         }

         $this->result = false;
         $this->message = 'Not a valid image id';
         if( empty( $id ) ) return false;
         
         // Try loading image
         try {
            $image = new Image( $id );
         } catch( Exception $e ) {
            $this->result = false;
            $this->message = 'Failed to add image';
            return false;
         }
         
         
         $this->result = false;
         $this->message = "Not a valid image id";
         // Check to see if this is a valid image
         if( !$image instanceof Image || !$image->isLoaded() ) return false;

         // Get the user session array containing ticked images
         $checkedimages = UserSessionArray::getItems( 'tickedimages' );
         $checkedimages = $checkedimages[0];

         // Check if previously exists
         if( count( $checkedimages ) > 0 ) {
         
            // Check so this image isn't already in array
            $exists = false;
            foreach( $checkedimages as $key => $checkedimage ) {
               if( $id == $checkedimage['id'] ) {
                  $exists = true;
               }
               
            }
         
            if( !$exists ) {
               UserSessionArray::clearItems( 'tickedimages' );
               $checkedimages []= $image->asArray();
               UserSessionArray::addItem( 'tickedimages', $checkedimages );
            }
         
         } else {
            $checkedimages []= $image->asArray();
            UserSessionArray::addItem( 'tickedimages', $checkedimages );
         }
         
         // Everything is fine and dandy
         $this->result = true;
         $this->message = 'Image ticked';
         $this->images = $checkedimages;
         return true;
         
      }
      
      /**
       * Remove a image ID from the chosen images-array
       * 
       * @api-name image.tick.delete
       * @api-param imageid Integer ID of the image to remove
       * @api-result result Boolean true/false
       * @api-result images Array List of chosen images
       * @api-result message String Describes the result of the operation in US English
       */       
      public function delete( $id = 0 ) {
         
         if( isset( $_POST['imageid'] ) ) {
            $id = (int) $_POST['imageid'];
         }
         
         $this->result = false;
         $this->message = 'Not a valid image id';
         if( empty( $id ) ) return false;
         
         // Get the user session array containing ticked images
         $checkedimages = UserSessionArray::getItems( 'tickedimages' );
         $checkedimages = $checkedimages[0];
         
         // Check if previously exists
         if( count( $checkedimages ) > 0 ) {
         
            // Check so this image isn't already in array
            foreach( $checkedimages as $key => $checkedimage ) {
               if( $id == $checkedimage['id'] ) {
                  
                  UserSessionArray::clearItems( 'tickedimages' );
                  unset( $checkedimages[$key] );
                  UserSessionArray::addItem( 'tickedimages', $checkedimages );
                  
                  // Everything is fine and dandy
                  $this->result = true;
                  $this->message = 'Image removed';
                  $this->images = $checkedimages;
                  return true;
                  
               }
               
            }
         
         }
         
         $this->result = false;
         $this->message = 'No such image';
         $this->images = $checkedimages;
         return false;
         
      }
      
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
               )
            ),
            'delete' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
   }


?>