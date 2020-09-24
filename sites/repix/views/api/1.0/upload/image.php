<?PHP
   
   import( 'pages.json' );
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   
   class UploadImage extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'unique' => VALIDATE_INTEGER,
                  'title' => VALIDATE_STRING,
                  'description' => VALIDATE_STRING,
                  'contenttype' => VALIDATE_STRING,
                  'identifier' => VALIDATE_STRING,
                  'externalurl' => VALIDATE_STRING,
                  'addtobasket' => VALIDATE_INTEGER,
                  'gps_altitude' => VALIDATE_FLOAT,
                  'gps_latitude' => VALIDATE_FLOAT,
                  'gps_longitude' => VALIDATE_FLOAT,
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
       * @api-post-optional title String The title of the uploaded file
       * @api-post-optional description String The description of the uploaded file
       * @api-post-optional identifier String A source identifier of this image. non-unique
       * @api-post-optional contenttype String image/jpeg if not set to something else. Overridden
       * @api-post-optional externalurl String Optional URL to pull source image from
       * @api-post-optional addtobasket Boolean Set to true to add image to the basket.
       * @api-post-optional gps_altitude Float the altitude as a float
       * @api-post-optional gps_latitude Float the altitude as a float
       * @api-post-optional gps_longitude Float the altitude as a float
       * @api-file-optional image File The image object to upload, in a JPEG format file.
       * @api-result image Array array containing information about the uploaded image
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         try {
            
            // import POST variables.
            $albumid = $_POST['albumid'];
            $title = trim( $_POST['title'] );
            $description = trim( $_POST['description'] );
            $contenttype = trim( $_POST['contenttype'] );
            $externalurl = trim( $_POST['externalurl'] );
            $identifier = trim( $_POST['identifier'] );
            $addtobasket = (int) $_POST['addtobasket'];
            $unique = (int) $_POST['unique'];
            
            $gps_altitude = (float) $_POST['gps_altitude'];
            $gps_latitude = (float) $_POST['gps_latitude'];
            $gps_longitude = (float) $_POST['gps_longitude'];
            
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
            
            // make sure we have a valid uploadable file
            if( !$_FILES['image']['tmp_name'] && $externalurl ) {
               
               try {
                  
                  $_FILES['image']['tmp_name'] = tempnam( '/tmp/', 'uploadxfer' );
                  unlink( $_FILES['image']['tmp_name'] );
                  $_FILES['image']['tmp_name'].= '.jpg';
                  file_put_contents( $_FILES['image']['tmp_name'], file_get_contents( $externalurl ) );
                  
               } catch( Exception $e ) {
                  
                  if( file_exists( $_FILES['image']['tmp_name'] ) ) {
                     // remove the uploaded file (if created manually)
                     unlink( $_FILES['image']['tmp_name'] );
                  }
                  
                  throw new Exception( 'Upload error: Unable to download remote resource' );
                  
               }
               
               if( !file_exists( $_FILES['image']['tmp_name'] ) || !filesize( $_FILES['image']['tmp_name'] ) ) {
                  
                  $_FILES['image']['tmp_name'] = false;
                  
               } else {
                  
                  $_FILES['image']['type'] = $contenttype ? $contenttype : 'image/jpeg';
                  
               }
               
            }
            
            // make sure we have a valid uploaded file.
            if( !$_FILES['image']['tmp_name'] ) {
               throw new Exception( 'Required field missing: image File Object or externalurl String' );
            }
            
            if( $unique && $identifier ) {
            	
            	$where = array( 'identifier' => $identifier, 'owner_uid' => Login::userid(), 'deleted_at' => null );
            	
            	try {
            		$image = Image::fromFieldValue( $where, 'Image', false );
            	} catch( Exception $e ) {
            		$image = false;
            	}
            	
            } else {
            	
            	$image = false;
            	
            }
            
            if( !$image ) {
            
	            // store the uploaded image
	            $imageid = StorageUtil::uploadImage(
	               Login::userid(),
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
	            
	            // store any custom supplied GPS coordinates
	            if( $gps_altitude > 0 ) {
	               $image->exif_gps_altitude = $gps_altitude;
	            }
	            if( $gps_latitude > 0 ) {
	               $image->exif_gps_latitude = $gps_latitude;
	            }
	            if( $gps_longitude > 0 ) {
	               $image->exif_gps_longitude = $gps_longitude;
	            }
	            
	            // save the image
	            $image->save();
	            
            }
            
            // add it to the basket?
            if( $addtobasket ) {
               
               try {
                  
                  $cart = new Cart();
                  $cart->addItem( '0483', 1, array( 'images' => array( $image->bid => 1 ) ) );
                  $cart->addPrintAttribute( '' );
                  $cart->save();
                  
                  #throw new Exception( print_r( $cart, true ) );
                  
               } catch ( Exception $e ) {
                  
                  throw new Exception( 'Unable to add to basket: '.$e->getMessage() );
                  
               }
               
            }
            
            // store in the user object
            $user = new User( Login::userid() );
            $user->setPreference( 'lastimageid', $image->bid );
            
            // return the image object
            $this->image = $image->asArray();
            
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