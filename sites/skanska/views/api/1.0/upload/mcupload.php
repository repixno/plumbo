<?php
   
    import( 'pages.json' );
    import( 'storage.util' );
    import( 'website.image' );
    import( 'website.uploadedimagesarray' );
   
   //class JQAPIUpload extends JSONPage implements NoAuthRequired,IView {
   class JQAPIUpload extends WebPage implements NoAuthRequired,IView {
         
      
      public function Execute() {
        
        //header('Access-Control-Allow-Origin: *');
        
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT');
        
        
        $orderId = $_GET['orderId'];
        
        
        $userid = DB::query( "SELECT userid FROM mediaclip_session WHERE sessionid = ?", $orderId )->fetchSingle();
        
        $param = 'file';
    
        if( $_FILES ){
            
            $param = 'file';
            
            try {
         
            // check to see if the albumid should be set from POST data
            if( !$albumid && isset( $_POST['albumid'] ) ) {
               $albumid = (int)$_POST['albumid'];
            }
            
            // compatibility with the old Flash-uploader
            if( !$albumid && isset( $_POST['uploadaid'] ) ) {
               $albumid = (int)$_POST['uploadaid'];
            }
            
            // compatibility with upload_aid from EF 2.x
            if( !$albumid && isset( $_SESSION['upload_aid'] ) ) {
               $albumid = $_SESSION['upload_aid'];
            }
            
            // more flash-uploader compatibility checks
            if( !$batchid && isset( $_POST['batchid'] ) ) {
               $batchid = $_POST['batchid'];
            }
            
            //$userid = Login::isLoggedIn() ? Login::userid() : 61224;
            
            //$userid = 346812;
            
            // did we receive a file correctly?
            if( count( $_FILES ) && isset(  $_FILES[$param] ) ) {
                
               
                try {
                    if( $albumid > 0 ) {
                        $album = new Album( $albumid );
                        if( $album->uid != Login::userid() ) {
                            throw new SecurityException( 'Permission Denied', 403 );
                        }   
                    }
                  
                    $upload = $_FILES[$param];
                    
                    //if ( is_array($upload['tmp_name']) ) {
                    // param_name is an array identifier like "files[]",
                    // $_FILES is a multi-dimensional array:
                    
                    
                        //foreach ($upload['tmp_name'] as $index => $value) {
                            
                            $imageid = StorageUtil::uploadImage(
                                $userid,
                                $albumid ? $albumid : null,
                                $upload['tmp_name'],
                                $upload['type'],
                                $file_name ? $file_name : $upload['name']
                            );
                            
                        if( $imageid > 0){
                            $image = new Image( $imageid );
                            $imageinfo = $image->asArray();
                            
                            $file[] = array(
                                            'id' => $imageid,
                                            'thumbnailUrl' => $imageinfo['thumbnail'],
                                            'url'   => $imageinfo['urls']['private'],
                                            'name' => $upload['name'],
                                            'size' => $upload['size']
                                        );
                            UploadedImagesArray::add( $imageid );
                        }else{
                            throw new Exception( 'Upload failed' );
                        }
                               
                        //}
                    //}
                  
               } catch( Exception $e ) {
                  
                 Util::Debug( $e->getMessage() );
                 exit;
                  
                  //header("HTTP/1.0 500 Internal Server error");
                  //exit( 0 );
                  
                }
                
                //$this->result = true;
                //$this->message = "OK";
                //$this->imageid = $imageid;
                
                
                echo "{userFiles}$userid/$imageid.jpg";
                exit;
                /*$this->result = true;
                $this->files = $file;
                $this->message = $imageid;*/
                return true;
               
            }
            else{
                
                $this->result = true;
                $this->files = "null filer";
                $this->message = 0;
                return true;
                
            }
         } catch( Exception $e ) {
            
            Util::Debug( $e->getMessage() );
            exit;
            # logTimer( sprintf( 'EXCEPTION %s %s | %d %s', date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $e->getCode(), $e->getMessage() ) );
            //throw new Exception( $e->getMessage(), $e->getCode() );
            
         }
            
            
        }
        
         $this->message = "";
         $this->files = '';
         
      }
      
   }
   
?>