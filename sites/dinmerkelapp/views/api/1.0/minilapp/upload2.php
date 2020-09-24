<?php
   
    import( 'pages.json' );
   
   class JQAPIUpload extends JSONPage implements NoAuthRequired, IView {
        private $tmpfolder = '/home/www/tmpbilder/';
        private $width = 154;
        private $height = 154;
        private $secret = 'Ku6uwuRi';
   
      
      public function Execute() {
        
        if( $_FILES ){
            
            $param = 'files';
            
            try {
         
            // check to see if the albumid should be set from POST data
            
            
            // did we receive a file correctly?
            if( count( $_FILES ) && isset( $_FILES[$param] ) ) {
               
               try {

                  
                    $upload = $_FILES[$param];
                    
                    if ( is_array($upload['tmp_name'])) {
                    // param_name is an array identifier like "files[]",
                    // $_FILES is a multi-dimensional array:
                    
                    
                        foreach ($upload['tmp_name'] as $index => $value) {
                            
                            
                            
                            
                            $filename = md5( uniqid() . $this->secret ) . '.jpg';
                            
                            if (move_uploaded_file($upload['tmp_name'][$index],  $this->tmpfolder . $filename )) {
                               
                               //instantiate the image magick class
                               $image = new Imagick( $this->tmpfolder . $filename );
                               
                               //crop and resize the image
                               $image->thumbnailImage ( 500, 500, true );
                               $image->writeImage( $this->tmpfolder . $filename);
                               
                               $data = array('filename' => $filename );
                                
                            } else {
                               
                                $data = array('error' => 'Failed to save' . serialize( $_FILES ) );
                                
                            }
                               
                        }
                    }
                  
               } catch( Exception $e ) {
                  
                  header("HTTP/1.0 500 Internal Server error");
                  exit( 0 );
                  
                }
               
                }
                $this->result = true;
                $this->files = $data;
                $this->message = $imageid;
                return true;
         } catch( Exception $e ) {
            
            # logTimer( sprintf( 'EXCEPTION %s %s | %d %s', date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $e->getCode(), $e->getMessage() ) );
            throw new Exception( $e->getMessage(), $e->getCode() );
            
         }
            
            
        }
        
         $this->message = "";
         $this->files = '';
         
      }
      
   }
   
?>