<?php

   /**
    * Remove image(s) from collection
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionRemoveImage extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_STRING,
                  'collection' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               )
            )
         );
           
      }
      
      /**
       * Remove image(s) from collection
       * 
       * @api-name collection.remove.image
       * @api-post-optional imageid String ID of the image or a commaseparated list of ID's
       * @api-param-optional imageid String ID of the image or a commaseparated list of ID's
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $imageid = 0, $collection = 'default' ) {

         $imageid = $_POST[ 'imageid' ] ? $_POST[ 'imageid' ] : $imageid;
         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         $collectionname = sprintf( 'collection_%s_image' , $collection );
         
         $images = explode ( ',', $imageid );
         
         if ( count( $images ) <= 0 ) {
            
            $images[ 0 ] = (int)$imageid;
            
         } 
                 
         try {
            
            foreach ( $images as $imageid ) {
         
               $image = new Image( $imageid );
               
               if ( $image->isLoaded() ) {
                  
                  $usersession = new UserSessionArray( $collectionname );
                  
                  $usersession->removeItem( $collectionname, $imageid );
                  
               }
               
            }
            
            $this->message = 'OK';
            $this->result = true;
            
         } catch ( Exception $e ) {
            
            $this->message = 'Failed';
            $this->result = false;
            
         }
      }
      
   }


?>