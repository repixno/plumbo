<?php

   /**
    * enumerate collection
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionEnum extends JSONPage implements IValidatedView {
      
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
       * enumerate collection
       * 
       * @api-name collection.enum
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result images Array array of images
       * @api-result albums Array array of albums
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $collection = 'default' ) {

         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         $imagecollectionname = sprintf( 'collection_%s_image' , $collection );
         $albumcollectionname = sprintf( 'collection_%s_album' , $collection );
         
         try {
         
            $imageusersession = new UserSessionArray( $imagecollectionname );
            
            $this->images = $imageusersession->getItems( $imagecollectionname );
            
            $albumusersession = new UserSessionArray( $albumcollectionname );
            
            $this->albums = $albumusersession->getItems( $albumcollectionname );
            
            $this->message = 'OK';
            $this->result = true;
            
         } catch ( Exception $e ) {
            
            $this->message = 'Failed';
            $this->result = false;
            
         }
      }
      
   }


?>