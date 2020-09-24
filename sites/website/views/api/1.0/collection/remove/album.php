<?php

   /**
    * Remove album(s) from collection
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionRemoveAlbum extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_STRING,
                  'collection' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               )
            )
         );
           
      }
      
      /**
       * Remove album(s) from collection
       * 
       * @api-name collection.remove.album
       * @api-post-optional albumid String ID of the album or a commaseparated list of ID's
       * @api-param-optional albumid String ID of the album or a commaseparated list of ID's
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $albumid = 0, $collection = 'default' ) {

         $albumid = $_POST[ 'albumid' ] ? $_POST[ 'albumid' ] : $imageid;
         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         $collectionname = sprintf( 'collection_%s_album' , $collection );
         
         $albums = explode ( ',', $albumid );
         
         if ( count( $albums ) <= 0 ) {
            
            $albums[ 0 ] = (int)$albumid;
            
         } 
         
         try {
         
            foreach( $albums as $albumid ) {@
            
               $album = new Album( $albumid );
               
               if ( $album->isLoaded() ) {
                  
                  $usersession = new UserSessionArray( $collectionname );
         	   
                  /*$images = new Image();
         	   
                  foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $albumid, 'deleted_at' => null ) )->fetchAll() as $row ) {
                
                     $usersession->removeItem( $collectionname, $row[ 0 ] );
                     
                  }*/
                  
                  $usersession->removeItem( $collectionname, $albumid );
               
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