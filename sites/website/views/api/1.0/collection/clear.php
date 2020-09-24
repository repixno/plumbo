<?php

   /**
    * clear collection
    * 
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'session.usersessionarray' );

   class APICollectionClear extends JSONPage implements IValidatedView {
      
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
       * clear collection
       * 
       * @api-name collection.clear
       * @api-param-optional collection collection name
       * @api-post-optional collection collection name
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute( $collection = 'default' ) {

         $collection = $_POST[ 'collection' ] ? $_POST[ 'collection' ] : $collection;
         
         $imagecollectionname = sprintf( 'collection_%s_image' , $collection );
         $albumcollectionname = sprintf( 'collection_%s_album' , $collection );
         
         try {
         
            $usersession = new UserSessionArray( $imagecollectionname );
            
            $usersession->Clear();

            $usersession = new UserSessionArray( $albumcollectionname );
            
            $usersession->Clear();
            
            $this->message = 'OK';
            $this->result = true;
            
         } catch ( Exception $e ) {
            
            $this->message = 'Failed';
            $this->result = false;
            
         }
      }
      
   }


?>