<?php
   
    import( 'pages.json' );
    import( 'storage.util' );
    import( 'website.image' );
    import( 'website.uploadedimagesarray' );
   
   class JQAPIUpload extends JSONPage implements NoAuthRequired, IView {
         
      
      public function Execute() {
            
            try {
         
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
            
            $userid = Login::isLoggedIn() ? Login::userid() : 61224;
            
               try {
                    if( $albumid > 0 ) {
                        $album = new Album( $albumid );
                        if( $album->uid != Login::userid() ) {
                            throw new SecurityException( 'Permission Denied', 403 );
                        }   
                    }
                    
                    $imageData = array(
                        'ownerid' => $userid,
                        'hashcode' => $hashcode,
                        'albumid' => $albumid,
                        'contenttype' => '',
                        'title' => '',
                        'filnamn' => ''
                    );
                    
                    
                   
                    $imageid = StorageUtil::saveImageDataBeforeUpload( $imageData );
                
                    //$imageid = 12312312;
                  
                    UploadedImagesArray::add( $imageid );
                  
               } catch( Exception $e ) {
                  
                  header("HTTP/1.0 500 Internal Server error");
                  exit( 0 );
                  
                }
               
               
                $this->result = true;
                $this->message = "OK";
                $this->imageid = $imageid;
                return true;
         } catch( Exception $e ) {
            
            # logTimer( sprintf( 'EXCEPTION %s %s | %d %s', date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $e->getCode(), $e->getMessage() ) );
            throw new Exception( $e->getMessage(), $e->getCode() );
            
         }
            
            
        
        
         $this->message = "";
         $this->files = '';
         
      }
      
   }
   
?>