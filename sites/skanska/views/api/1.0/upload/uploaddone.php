<?php
   
    import( 'pages.json' );
    import( 'storage.utilnew' );
    import( 'website.image' );
    import( 'website.uploadedimagesarray' );
   
   class JQAPIUpload extends JSONPage implements NoAuthRequired, IView {
         
      
      public function Execute() {
        
        try{
            
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
            
                $albumid = 3868321;
            
                $imageid = $_POST['imageid' ];
                $imageData = $_POST['imageData' ];
                $metaData = $_POST['metaData' ];
                $image = StorageUtil::upateImagedataAfterUpload( $imageid, $albumid, $imageData , $metaData );
                $this->result = true;
                $this->message = $imageid;
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