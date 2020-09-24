<?PHP
   
   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   
   class APIUploadSelectAlbum extends JSONPage implements IValidatedView {
      
      /**
       * Validate the incoming data
       *
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'batchid' => VALIDATE_INTEGER,
                  'albumid' => VALIDATE_INTEGER,
                  'albumtitle' => VALIDATE_STRING,
               ),
            ),
         );
         
      }

 
      /**
       * Select album for a image batch
       * 
       * @api-name upload.selectalbum
       * @api-post-optional batchid Integer ID of the image batch
       * @api-param-optional albumid Integer ID of the image batch
       * @api-post-optional albumid Integer ID of the album to put the images in
       * @api-param-optional albumid Integer ID of the album to put the images in
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute() {
         
         $batchid = $_POST['batchid'];
         $albumid = $_POST['albumid'];
         $albumtitle = $_POST['albumtitle'];

         if( $albumid > 0 ) {
            
            $album = new Album( $albumid );
            if( !$album->isLoaded() ) {
               $this->message = 'Unable to find album!';
               return false;
            }
            
            $albumid = $album->aid;
            
         } else {
            
            $albumid = null;
            
         }
         
         foreach( DB::query( 'SELECT bid FROM flash_uploader_images WHERE batch_id = ? AND uid = ?', $batchid, Login::userid() )->fetchAll() as $row ) {
            
            list( $bid ) = $row;
            
            $image = new Image( $bid );
            $image->aid = $albumid;
            $image->save();
            
         }
         
         $this->message = 'OK';
         $this->result = true;
         
      }
      
   }
   
?>