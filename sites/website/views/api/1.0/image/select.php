<?php

   /**
    * API for selecting a single image
    * 
    * @author Andreas F�rnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );

   class APIImageSelect extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            "execute" => array(
               "fields" => array(
                  VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      /**
       * Selects a given image belonging to the logged in user.
       * 
       * @api-name image.select
       * @api-param-optional imageid Integer The id of the image to be selected
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */       
      public function Execute( $imageid = 0 ) {
         
         $this->result = false;
         $this->message = "Failed integer verification";
         if( empty( $imageid ) ) return false;

         // Try loading image etc
         try{
            
            $image = new Image( $imageid );
            if( !$image->isLoaded() || !$image instanceof Image ) return false;
            
            Image::select( $imageid );
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = "Failed to load image";
            
         }
         
      }
      
   }
   
?>