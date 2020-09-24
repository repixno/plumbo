<?php
   
   /**
    * Get all user's uploaded images this session
    * Unfortunately needs to parse both old and
    * new uploader style.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    */

   import( 'pages.json' );
   
   class APIUploadEnum extends JSONPage implements NoAuthRequired, IView {
      
      /**
       * Fetch uploaded images
       * 
       * @api-name upload.enum
       * @api-result images Array array of images
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      
      public function Execute() {
         
         $result = array();
         $images = array();
         $oldstyle = array(); // Old uploader style
         $newstyle = array(); // New uploader style
         
         if( is_array( $_SESSION["client_info"]["uploaded_images"] ) ) {
            $oldstyle = array_keys( $_SESSION["client_info"]["uploaded_images"] );
            foreach( $oldstyle as $imageid ) {
               if( !in_array( $imageid, $result ) ) {
                  $result []= $imageid;
               }
            }
         }
         
         if( is_array( $_SESSION["client_info"]["upload"]["this_session"] ) ) {
            $newstyle = array_keys ( $_SESSION["client_info"]["upload"]["this_session"] );
            foreach( $newstyle as $imageid ) {
               if( !in_array( $imageid, $result ) ) {
                  $result []= $imageid;
               }
            }
         }
         
         
         $this->result = false;
         $this->message = 'Failed to load an image';
         foreach( $result as $imageid ) {
            $image = new Image( $imageid );
            if( !$image instanceof Image || !$image->isLoaded() ) return false;
            
            $images []= $image->asArray();
            
         }
         
         
         $this->result = true;
         $this->message = 'OK';
         $this->images = $images;
         
      }
      
   }
   
?>