<?php

   /**
    * Get info about an album
    * 
    */

   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );

   class APIAlbumInfo extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
               )
            )
         );
           
      }
      
      /**
       * Get info about an album.
       * 
       * @api-name album.info
       * @api-post-optional albumid Integer ID of the album
       * @api-param-optional albumid Integer ID of the album
       * @api-result album Array Album info
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */

      public function Execute( $albumid = 0 ) {
         
         $albumid = $_POST['albumid'] ? $_POST['albumid'] : $albumid;
         
          try {
            
            $album = new Album( $albumid );

            $this->album = $album->asArray();

            $this->result = true;
            $this->message = 'OK';

          } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such album or no access to this album.';
          
            return false;
            
         }
         
      }

      /**
       * Count images
       * 
       * @return array
       */

      private function getImageCount( $albumid ) {

         return (int)DB::query( "SELECT count(aid) FROM bildeinfo WHERE owner_uid = ? AND deleted_at IS NULL", $albumid )->fetchSingle();

      }

   }


?>
