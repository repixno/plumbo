<?php

   /**
    * Count items in collection
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionCount extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'collection' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_STRING,
               )
            )
         );
           
      }
      
      /**
       * Count items in collection
       * 
       * @api-name collection.count
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result images Count of images in collection
       * @api-result albums Count of albums in collection
       * @api-result albumimages Count of images in albums
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $collection = 'default' ) {

         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         // this is stupid, i know.
         if ( $collection == 'false' ) $collection = 'default'; 
         
         $imagecollectionname = sprintf( 'collection_%s_image' , $collection );
         $albumcollectionname = sprintf( 'collection_%s_album' , $collection );
         
         try {
         
            $usersession = new UserSessionArray( $imagecollectionname );
            
            $this->images = $usersession->count();

            $usersession = new UserSessionArray( $albumcollectionname );
            
            $this->albums = $usersession->count();

            try {
               
               $albums = $usersession->getItems( $albumcollectionname );
                           
               foreach ( $albums as $album ) {
                  foreach ( $album as $image ) {
                     $albumimages++;
                  }
               }
               
            } catch (Exception $x) { }
            
            $this->albumimages = (int)$albumimages;
            
            $this->message = 'OK';
            $this->result = true;
            
         } catch ( Exception $e ) {
            
            $this->message = 'Failed';
            $this->result = false;
            
         }
      }
      
   }


?>