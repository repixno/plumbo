<?php

   /**
    * Get info about an image
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );
   import( 'website.image' );

   class APIimageInfo extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
               )
            )
         );
           
      }
      
      /**
       * Get info about an image.
       * 
       * @api-name image.info
       * @api-post-optional imageid Integer ID of the image
       * @api-param-optional imageid Integer ID of the image
       * @api-result image Array Image info
       * @api-result previousimage Array Previous image
       * @api-result nextimage Array Next image
       * @api-result imagenumber Integer Position of image in album
       * @api-result imagecount Integer Amount of images in album 
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */

      public function Execute( $imageid = 0 ) {
         
         $imageid = $_POST['imageid'] ? $_POST['imageid'] : $imageid;
         
          try {
            
            $image = new image( $imageid );

            $this->image = $image->asArray();

            // fetch album

            $album = new Album( $image->aid );

            // get images from album and loop them into a array of image id's

            $images = $album->getImages();

            $imageids = array();

            foreach( $images as $image ) {
               $imageids[] = $image[ 'id' ];
            }

            // search the array for our imageid

            $position = array_search( $imageid, $imageids );

            $this->imagenumber = $position + 1;
            $this->imagecount = count( $imageids );

            // check for previous image, add to view if found
           
            if( $position > 0 ) {

               try {

                  $previousimage = new Image( $imageids[ $position - 1 ] );

                  $this->previousimage = $previousimage->asArray();

               } catch( Exception $e ) { }

            }

            // check for next image, add to view if found

            if( $position < count( $imageids ) - 1 ) {

               try {

                  $nextimage = new Image( $imageids[ $position + 1 ] );

                  $this->nextimage = $nextimage->asArray();

               } catch( Exception $e ) { }

            }

            $this->result = true;
            $this->message = 'OK';

          } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such image or no access to this image.';
          
            return false;
            
         }
         
      }

   }


?>
