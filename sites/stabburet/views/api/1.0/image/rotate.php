<?php

   /**
    * Get info about an image
    * 
    */

   import( 'pages.json' );
   import( 'website.image' );
   
   class ImageRotateAPI extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
                  'degrees' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
               )
            )
         );
           
      }
      
      /**
       * Rotate an image.
       * 
       * @api-name image.rotate
       * @api-post-optional imageid Integer ID of the image
       * @api-post-optional degrees Integer The number of degrees to rotate in 90-degree iterations
       * @api-param-optional imageid Integer ID of the image
       * @api-param-optional degrees Integer The number of degrees to rotate in 90-degree iterations
       * @api-result image Array Image info after rotation
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */

      public function Execute( $imageid = 0, $degrees = 0 ) {
         
         $imageid = $_POST['imageid'] ? $_POST['imageid'] : $imageid;
         $degrees = $_POST['degrees'] ? $_POST['degrees'] : $degrees;
         
          try {
            
            $image = new image( $imageid );
            if( $image->uid != Login::userid() ) {
               throw new Exception( 'Access denied' );
            }
            
            if( $image->rotate( $degrees ) ) {
            
               $this->image = $image->asArray();
               $this->result = true;
               $this->message = 'OK';

            } else {
               
               $this->result = false;
               $this->message = 'Rotation failed';
               
            }
             
          } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such image or no access to this image.';
          
            return false;
            
         }
         
      }

   }
   
?>