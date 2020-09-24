<?PHP
   import( 'reedfoto.correction' );
   import( 'reedfoto.page' );
   import( 'reedfoto.user' );
   
   class ReedFotoCorrectionEdit extends WebPage implements IView {
      
      protected $template = 'edit';
      
      public function Execute( $correctionid = 0, $iframe = 0, $currentpageid = 0, $popup = 0 ) {
         
         $correction = new RFCorrection( $correctionid );

         if( !Login::isAdmin() && $correction->userid != Login::userid() ) {
            throw new Exception( 'Access denied', 403 );
         }
         
         if ( $iframe > 0) { 
            
            $this->template = 'edit_iframe';

            $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
            $filesfolder = Settings::Get( 'reedfoto', 'filesfolder', 'files' );
            
            $guid = UUID::create();
         
            if ( $_POST['pageid'] ) { 
               
               if ( $_FILES['uploadfile'] ) {
                  
                  $comment = $_POST[ 'comment' ];
                  
                  if ( file_exists( $_FILES[ 'uploadfile' ][ 'tmp_name' ] ) ) {
                     
                     $firstfolder = substr($guid, 0, 2);
                     $secondfolder = substr($guid, 2, 2);
                     
                     $filespath = sprintf( '%s/%s', $storagepath, $filesfolder );
                     
                     if ( !is_dir( sprintf( '%s/%s', $filespath, $firstfolder ) ) ) mkdir( sprintf( '%s/%s', $filespath, $firstfolder ) );
                     if ( !is_dir( sprintf( '%s/%s/%s', $filespath, $firstfolder, $secondfolder ) ) ) mkdir( sprintf( '%s/%s/%s', $filespath, $firstfolder, $secondfolder ) );                
                     
                     if ( move_uploaded_file( $_FILES[ 'uploadfile' ][ 'tmp_name' ],  sprintf( '%s/%s/%s/%s.ext', $filespath, $firstfolder, $secondfolder, $guid ) ) ) {
                     
                        $filehash = $guid;
                        $filetype = $_FILES['uploadfile']['type'];
                        $filesize = filesize( sprintf( '%s/%s/%s/%s.ext', $filespath, $firstfolder, $secondfolder, $guid ) );
                        $filename = util::urlize( $_FILES[ 'uploadfile' ][ 'name' ] );
                        
                        $type = 'file';
                        
                     }
                     
                  } else {
                     
                     $filehash = null;
                     $filetype = null;
                     $filesize = null;
                     $filename = null;
                     
                  }
                  
               } else {

                     $filehash = null;
                     $filetype = null;
                     $filesize = null;
                     $filename = null;
                     
               }
               
               $pagecomment = new RFPageComment();
               $pagecomment->pageid = (int)$_POST['pageid'];
               $pagecomment->comment = $_POST['comment'];
               $pagecomment->type = $type ? $type : 'comment';
               $pagecomment->filehash = $filehash;
               $pagecomment->filetype = $filetype;
               $pagecomment->filesize = $filesize;
               $pagecomment->filename = $filename;
               $pagecomment->x = $_POST['x'];
               $pagecomment->y = $_POST['y'];
               $pagecomment->created = date( 'Y-m-d' );
               $pagecomment->createdby = Login::userid();
               $pagecomment->save();
               
               $this->pagecommentid = $pagecomment->id;
               
            }   
            
         } else {
            
            $this->correction = $correction;
            
            $pages = array();
            
            foreach ( RFPage::enum( $correction->id ) as $page ) {
   
               $page = array(
               
                  'id' => $page->id,
                  'imageguid' => $page->imageguid,
                  'width' => $page->width,
                  'height' => $page->height,
                  'title' => $page->title,
                  'pagetext' => $page->pagetext,
                  'orderkey' => $page->orderkey,
                  
               );
               
               if ($currentpageid <= 0) $currentpageid = $page['id'];
                
               $pages[] = $page;
   
            }
            
            $this->currentpageid = $currentpageid;
            $this->pages = $pages;
           
         }
         
         if ( $correction->approved ) $this->approved = $correction->approved;
         
         if ( !$correction->opened ) {
            $correction->opened = date( 'Y-m-d' );
            $correction->openedby = Login::userid();
            $correction->save();         
         }
         
         $this->correctionid = $correctionid;
          
      }
      
   }
   
?>
