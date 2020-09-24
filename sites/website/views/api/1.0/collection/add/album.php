<?php

   /**
    * Add album(s) to collection
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionAddAlbum extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_STRING,
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
       * Add album(s) to collection
       * 
       * @api-name collection.add.album
       * @api-post-optional albumid String ID of the album or a comma separated list of ID's
       * @api-param-optional albumid String ID of the album or a comma separated list of ID's
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result count Integer items in collection
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $albumid = 0, $collection = 'default' ) {
          
         $albumid = $_POST[ 'albumid' ] ? $_POST[ 'albumid' ] : $albumid;
         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         $collectionname = sprintf( 'collection_%s_album' , $collection );
         
         $albums = explode ( ',', $albumid );
         
         if ( count( $albums ) <= 0 ) {
            
            $albums[ 0 ] = (int)$albumid;
            
         } 
         
         try {
            
            foreach ( $albums as $albumid ) {
               
               $album = new Album( $albumid );
               
               $usersession = new UserSessionArray( $collectionname );
                  
               if ( $album->isLoaded() ) {
                  
                  $albumimages = array();
         	   
                  $images = new Image();
         	   
                  foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $albumid, 'deleted_at' => null ) )->fetchAll() as $row ) {
                  
                     //$usersession->setitem( $collectionname, true, $row[ 0 ] );
                     
                     $albumimages[ $row[ 0 ] ] = true;
                     
                  }
                  
                  $usersession->setitem( $collectionname, $albumimages, $albumid );
                
               }
               
            }
            
            $this->count = $usersession->count();
            
            $this->message = 'OK';
            $this->result = true;
            
         } catch ( Exception $e ) {
            
            $this->message = 'Failed';
            $this->result = false;
            
         }
      }
      
   }


?>