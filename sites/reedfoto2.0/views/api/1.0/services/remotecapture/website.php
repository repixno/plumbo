<?PHP
   
   // import required code
   import( 'pages.json' );
   import( 'storage.util' );
   
   /**
    * Website Capture API
    * 
    * Captures a remote website and stores it 
    * in the users Inbox as a JPEG image.
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class WebsiteRemoteCaptureService extends JSONPage implements IValidatedView {
      
      /**
       * Captures a remote website and stores it 
       * in the users Inbox as a JPEG image.
       * 
       * @api-name services.remotecapture.website
       * @api-post url The URL to capture
       * @api-result image The new image object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {
         
         // import our validated URL
         $url = $_POST['url'];
         
         // make sure everything is good:
         try {
            
            // attempt to parse the url
            if( parse_url( $url ) === false ) {
               
               // url is not valid. throw an exception:
               throw new Exception( 'Invalid URL' );
               
            }
            
            // create a temporary file for out captured data
            $tmp = tempnam( '/tmp/', 'remotecapture' );
            
            // execute the capture operation
            $error = exec( sprintf( self::$cmdline, escapeshellarg( $url ), $tmp ) );
            
            // store the actual image data in EF3 datastore
            $imageid = StorageUtil::uploadImage( 
               Login::userid(),     // the userid of the logged in user
               null,                // no albumid required, using inbox
               $tmp,                // the path to the local file to upload
               'image/jpeg',        // the contenttype of this image (jpeg)
               $url                 // the title of the image, now the URL.
            );
            
            // load the image
            $image = new Image( $imageid );
            
            // store the image data in the JSON return value
            $this->image = $image->asArray();
            
            // remove the tempfile
            unlink( $tmp );
            
            // store our success story int the JSON return value
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
      static $cmdline = '/usr/bin/xvfb-run --server-args="-screen 0, 1024x768x24" /usr/bin/CutyCapt --url=%s --min-width=1024 --out-format=jpeg --out=%s';
      
      /**
       * Validation routine for WebsiteRemoteCaptureService service
       *
       * @return array
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'url' => VALIDATE_STRING, 
               ),
            ),
         );
         
      }
      
   }
   
?>