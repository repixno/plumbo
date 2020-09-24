<?php

   model('reedfoto.page');
   model( 'reedfoto.pagecomments' );
   import( 'reedfoto.pagecomments' );
   
   class RFPage extends DBRFPage implements ModelCaching {

      static function enum( $corrid ) {

         $pages = new RFPage();

         $ret = array();
         foreach ( $pages->collection( array( 'id' ), array( 'correctionid' => $corrid ), 'orderkey asc'  )->fetchAllAs( 'RFPage' ) as $page ) {

            $ret[] = $page;

         }

         return $ret;

      }

      public function asArray() {

         $commentList = array();
         foreach ( RFPageComment::enum( $this->id ) as $comment ) {

            $commentList[] = $comment->asArray();

         }

         $ret = array(
            'id' => $this->id,
            'correctionid' => $this->correctionid,
            'title' => $this->title,
            'imageguid' => $this->imageguid,
            'comments' => $commentList
            );

         return $ret;

      }
      
      public function delete() {
         
         $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
         $archivefolder = Settings::Get( 'reedfoto', 'archivefolder', 'archive' );
         $archivepath = sprintf( '%s/%s', $storagepath, $archivefolder );
         
         $guid = $this->imageguid;
         
         $firstfolder = substr($guid, 0, 2);
         $secondfolder = substr($guid, 2, 2);

         $largefile = sprintf ( '%s/%s/%s/%s.large.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
         $mediumfile = sprintf ( '%s/%s/%s/%s.medium.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
         $smallfile = sprintf ( '%s/%s/%s/%s.small.jpg', $archivepath, $firstfolder, $secondfolder, $guid );
         
         $comments = new RFPageComment();

         foreach ( $comments->collection( array( 'id' ), array( 'pageid' => $this->id ) )->fetchAllAs( 'RFPageComment' ) as $comment ) {
            
            $comment->delete();

         }
         
         unlink( $largefile );
         unlink( $mediumfile );
         unlink( $smallfile );
         
         parent::delete();
         
         return true;
      }

   }

?>