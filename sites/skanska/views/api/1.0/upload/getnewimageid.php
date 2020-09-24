<?php
   
    import( 'pages.json' );
    import( 'storage.utilnew' );
    import( 'website.image' );
    import( 'website.uploadedimagesarray' );
   
   class JQAPIUpload extends JSONPage implements NoAuthRequired, IView {
         
      
        public function Execute() {
              
              try {
              
                 try {
                      
                      $imageData = array(
                          'ownerid' => 0,
                          'hashcode' => '',
                          'albumid' => 0,
                          'contenttype' => '',
                          'title' => '',
                          'filnamn' => '',
                          'sessionid' => $_POST['sessionid']
                      );
                      
                      $imageid = StorageUtil::saveImageDataBeforeUpload( $imageData );
                    
                 } catch( Exception $e ) {
                    
                    header("HTTP/1.0 500 Internal Server error");
                    exit( 0 );
                    
                  }
                 
                 echo $imageid;
                 die();
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