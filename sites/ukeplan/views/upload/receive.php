<?PHP
   
   function errorhandler( $errno, $errstr, $errfile, $errline, $errcontext ) {
      
      #logTimer( sprintf( 'ERRORHANDLER %s %s | %d %s', date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $errno, $errstr ) );
      return true;
      
   }
   
   set_error_handler( 'errorhandler', E_ALL ^ E_NOTICE );
   
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.uploadedimagesarray' );
   
   class UploadReceive extends WebPage /*implements IValidatedView*/ implements IView {
      
      protected $template = false;
      
      public function Execute( $param = 'image', $albumid = null, $batchid = null ) {
         
         $allowed = array( 'jpg', 'jpeg', 'tif', 'tiff' );
         
         try {
            
            $ext = pathinfo($_FILES[$param]['name'], PATHINFO_EXTENSION );
         
            if( !in_array(   strtolower( $ext ) , $allowed ) ){
               echo  $ext . " not allowed";
               exit;
            }
         
            // check to see if the albumid should be set from POST data
            if( !$albumid && isset( $_POST['albumid'] ) ) {
               $albumid = $_POST['albumid'];
            }
            
            // compatibility with the old Flash-uploader
            if( !$albumid && isset( $_POST['uploadaid'] ) ) {
               $albumid = $_POST['uploadaid'];
            }
            
            // compatibility with upload_aid from EF 2.x
            if( !$albumid && isset( $_SESSION['upload_aid'] ) ) {
               $albumid = $_SESSION['upload_aid'];
            }
            
            // more flash-uploader compatibility checks
            if( !$batchid && isset( $_POST['batchid'] ) ) {
               $batchid = $_POST['batchid'];
            }
            
            // did we receive a file correctly?
            if( count( $_FILES ) && isset( $_FILES[$param] ) ) {
               
               try {
                  
                  if( $albumid > 0 ) {
                     
                     $album = new Album( $albumid );
                     if( $album->uid != Login::userid() ) {
                        throw new SecurityException( 'Permission Denied', 403 );
                     }
                     
                  }
                  
                  if( is_uploaded_file( $_FILES[$param]['tmp_name'] ) && !$_FILES[$param]['error'] ) {
                     
                     $userid = Login::isLoggedIn() ? Login::userid() : 61224;
                     
                     $imageid = StorageUtil::uploadImage(
                        $userid,
                        $albumid ? $albumid : null,
                        $_FILES[$param]['tmp_name'],
                        $_FILES[$param]['type'],
                        $_FILES[$param]['name']
                     );
                     
                     if( $imageid ) {
                        
                        // render the imageid
                        header("HTTP/1.0 200 OK");
                        
                        // draw the imageid
                        echo $imageid;
                        
                        // add the image to the array of images
                        UploadedImagesArray::add( $imageid );
                        
                        // do we have a batchid?
                        if( $batchid > 0 && $imageid > 0 ) {
                           
                           // hack for flash uploader
                           DB::query( 'INSERT INTO flash_uploader_images( uid, batch_id, bid ) VALUES( ?, ?, ? );', $userid, $batchid, $imageid );
                           
                        }
                        
                     } else {
                        
                        // something went terribly wrong :(
                        throw new Exception( 'Upload failed' );
                        
                     }
                     
                  }
                  
               } catch( Exception $e ) {
                  
                  header("HTTP/1.0 500 Internal Server error");
                  exit( 0 );
                  
               }
               
            }
            
         } catch( Exception $e ) {
            
            # logTimer( sprintf( 'EXCEPTION %s %s | %d %s', date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $e->getCode(), $e->getMessage() ) );
            throw new Exception( $e->getMessage(), $e->getCode() );
            
         }
         
      }
      
   }
   
?>