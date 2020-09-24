<?PHP
   
   import( 'pages.json' );
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   import( 'website.uploadedimagesarray' );
   
   class UploadImage extends JSONPage implements NoAuthRequired, IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'files' => array(
                  'image' => array(
                     'tmp_name' => VALIDATE_STRING,
                     'type' => VALIDATE_STRING,
                     'name' => VALIDATE_STRING,
                  ),
               ),
            ),
         );
      
      }
      
      /**
       * Uploads a file to Eurofoto
       * 
       * @api-name upload.image
       * @api-post-optional albumid Integer The ID of the album to upload the file into
       * @api-file-optional image File The image object to upload, in a JPEG format file.
       * @api-result image Array array containing information about the uploaded image
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         try {
            
            // import POST variables.
            $albumid = $_POST['albumid'];

            if( $albumid ) {
               // load the album from disk
               $album = new Album( $albumid );
               // make sure its YOUR album
               if( $album->uid != Login::userid() ) {
                  throw new SecurityException( 'Permission Denied', 401 );
               }
               
            }
            
            // make sure we have a title
            if( empty( $title ) && $_FILES['image']['name'] ) {
               $title = $_FILES['image']['name'];
            }
            
            // make sure we have a title
            if( empty( $title ) ) {
               throw new Exception( 'Required field missing: title' );
            }
            
            // make sure we have a valid uploaded file.
            if( !$_FILES['image']['tmp_name'] ) {
               throw new Exception( 'Required field missing: image File Object or externalurl String' );
            }
            
            if( Login::userid() ){
               $user = Login::userid();
            }else{
               $user = 61224;
            }
            
            // store the uploaded image
            $imageid = StorageUtil::uploadImage(
               $user,
               $albumid ? $albumid : null,
               $_FILES['image']['tmp_name'], 
               $_FILES['image']['type'], 
               $title,
               $description
            );
            
            // remove the uploaded file (if created manually)
            unlink( $_FILES['image']['tmp_name'] );
            
            // make sure the imageid is valid
            if( !$imageid ) {
               throw new Exception( 'Upload failed' );
            }
            
            // attempt to load the image
            $image = new Image( $imageid );
            $image->identifier = $identifier ? $identifier : null;
            
            //add to uploaded iamges array
            UploadedImagesArray::add( $imageid );
            
            // save the image
            $image->save();
            
            // return the image object
            $this->image = $image->asArray();
            
            $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
            
            if(!$xhr ){
               
               $imageid = $this->image['id'];
               $thumbnail = $this->image['thumbnail'];
               $name = $this->image['title'];
               
               
               echo "<textarea>";
               echo '{
                  "id": "'.$imageid.'", 
                  "thumbnail":  "'.$imageid.'",
                  "name":   "'.$imageid.'"
               }';
               echo '</textarea>';
               die();
            }
            
            // return successful!
            $this->result = true;
            $this->message = 'OK';
            
            
            
         } catch( SecurityException $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
            if( $e->getCode() == 401 ) {
               header( 'HTTP/1.0 401 Access Denied' );
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Upload failed: '.$e->getMessage();
            
         }
         
      }
      
   }
   
?>