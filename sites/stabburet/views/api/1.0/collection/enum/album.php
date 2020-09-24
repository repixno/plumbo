<?php

   /**
    * enumerate collection (album)
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionEnumAlbum extends JSONPage implements IValidatedView {
      
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
       * enumerate collection (album)
       * 
       * @api-name collection.enum.album
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result albums Array array of albums
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $collection = 'default' ) {

         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         $albumcollectionname = sprintf( 'collection_%s_album' , $collection );
         
         try {
         
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