<?PHP
   
   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.image' );
   
   class APIUploadSetCurrentAlbum extends JSONPage implements IValidatedView {
      
      /**
       * Validate the incoming data
       *
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               
            ),
         );
         
      }

      /**
       * Set current upload album
       * 
       * @api-name upload.setcurrentalbum
       * @api-post-optional albumid Integer ID of the album to be selected
       * @api-param-optional albumid Integer ID of the album to be selected
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      public function Execute( $albumid = 0 ) {
         
         $albumid = $_POST['albumid'] ? $_POST['albumid'] : $albumid;
         
         if( $albumid > 0 ) {
            
            try {
               
               $album = new Album( $albumid );
   
               
               if( !$album->isLoaded() ) {
                  
                  $this->message = 'Unable to find album!';
                  $this->result = false;
                  
                  return false;
               }
               
               if( $album->uid == Login::userid() ) {
                  
                  $_SESSION['upload_aid'] = $albumid;
                  
                  $this->message = 'OK'; 
                  $this->result = true;
                  
                  return true;
                  
               } else {
                  
                  $this->message = 'No access to this album.';
                  $this->result = false;
                  
                  return false;
               }
               
            } catch (Exception $ex) {
               
               $this->message = 'No access to this album.';
               $this->result = false;
               
               return false;               
            }

         } else {
            
            $_SESSION['upload_aid'] = 0;
            
            $this->message = 'OK';
            $this->result = true;
            
            return false;
         }
         
      }
      
   }
   
?>