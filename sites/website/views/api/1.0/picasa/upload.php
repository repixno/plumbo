<?PHP
   
   import( 'pages.json' );
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   
   class UploadPicasaImage extends Webpage implements IValidatedView {
      
      protected $template = false;
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'albumname' => VALIDATE_STRING,
                  'title' => VALIDATE_STRING,
                  'sessionid' => VALIDATE_STRING,
                  'photocount' => VALIDATE_STRING
               ),
               'files' => array(
                  'ImageData' => array(
                     'tmp_name' => VALIDATE_STRING,
                     'type' => VALIDATE_STRING,
                     'name' => VALIDATE_STRING,
                  ),
               ),
            ),
         );
      
      }
      
      /**
       * Uploads a file to Eurofoto from picasa
       * 
       */
      public function Execute() {
         
         try {
            
            // import POST variables.
            $albumname = trim( $_POST['albumname'] ) ;

            // make sure we have a title
            if( empty( $title ) && $_FILES["ImageData"]['name'] ) {
               $title = $_FILES["ImageData"]['name'];
            }
            
            
            Login::bySecureToken( $_POST['sessionid'] );
            
            
            $albumid = DB::query( "SELECT aid FROM bildealbum WHERE namn ILIKE ? AND uid = ? ORDER BY aid DESC LIMIT 1", $albumname, Login::userid() )->fetchSingle();
            
            if( !empty( $albumname ) && $albumid < 1 ){
               $album = new Album();
               $album->uid = Login::userid();
               $album->namn = $albumname;
               $album->save();
               
               $albumid = $album->aid;
               
            }
            
            //mail( 'tor.inge@eurofoto.no' , "picasa debug" , "ok" . $title . "albumname: " .  $albumname . " albumid: $albumid ==" . $album->aid );
            
            // make sure we have a title
            if( empty( $title ) ) {
               throw new Exception( 'Required field missing: title' );
            }

            // make sure we have a valid uploaded file.
            if( !$_FILES["ImageData"]['tmp_name'] ) {
               throw new Exception( 'Required field missing: image File Object or externalurl String' );
            }
            
            // store the uploaded image
            $imageid = StorageUtil::uploadImage(
               Login::userid(),
               $albumid ? $albumid : null,
               $_FILES["ImageData"]['tmp_name'], 
               $_FILES["ImageData"]['type'], 
               $title,
               $description
            );

            
            $resp = 	"error=0\n";
            $resp .=	"clickurl=http://www.eurofoto.no/myaccount/album/" . $albumid;
            echo $resp;
            
         } catch( SecurityException $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
            if( $e->getCode() == 401 ) {
               header( 'HTTP/1.0 401 Access Denied' );
            }
            
         } catch( Exception $e ) {
            
            echo "error=105";
            
            mail( 'tor.inge@eurofoto.no' , "picasa debug error" , "error" . $this->message  );
            
         }
         
      }
      
   }
   
?>