<?php

   import( 'website.image' );
   
   import( 'pages.protected' );
   
   class AccountImagesImage extends ProtectedPage implements IValidatedView {

      protected $template = 'account.images.image';
      
      /**
       * Validator
       * 
       * @return array array of fields
       *
       */

		public function Validate() {

         return array(
            'execute' => array( 
               'fields' => array(
                  'page' => VALIDATE_INTEGER,
                  'mode' => VALIDATE_STRING
               )
            )
         );
         
		}
      	
      /**
       * Execute
       *
       * adds image, imagenumber, imagecount, previousimage, nextimage to view
       * 
       */

      public function Execute( $imageid = 0 ) {
         
         $image = new Image( $imageid );

         if( !empty( $image ) || $image instanceof Image ) {
            
            // add image data to view
            
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
                  
               } catch( Exception $e ) {}
               
            }
            
            // check for next image, add to view if found
            
            if( $position < count( $imageids ) - 1 ) {
               
               try {
                  
                  $nextimage = new Image( $imageids[ $position + 1 ] );
                  
                  $this->nextimage = $nextimage->asArray();
                  
               } catch( Exception $e ) {}
               
            }
            
         }
         
      }
      
   }
   
?>