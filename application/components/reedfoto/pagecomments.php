<?php

   model( 'reedfoto.pagecomments' );

   class RFPageComment extends DBRFPageComment implements ModelCaching {

      static function enum( $pageid ) {

         $comments = new RFPageComment();

         $ret = array();
         foreach ( $comments->collection( array( 'id' ), array( 'pageid' => $pageid, 'deleted' => null ) )->fetchAllAs( 'RFPageComment' ) as $comment ) {

            $ret[] = $comment;

         }

         return $ret;

      }

      public function asArray() {

         $ret = array(
            'id' => $this->id,
            'pageid' => $this->pageid,
            'type' => $this->type,
            'comment' => $this->comment,
            'filehash' => $this->filehash,
            'filetype' => $this->filetype,
            'filesize' => $this->filesize,
            'filename' => $this->filename,
            'x' => $this->x,
            'y' => $this->y,
            'created' => $this->created,
            'createdby' => $this->createdby,
            'deleted' => $this->deleted,
            'deletedby' => $this->deletedby
         );
         
         if ($ret['filesize'] > 0) $ret['filesizekb'] = round($ret['filesize'] / 1024);

         return $ret;

      }
      
      public function delete() {
         
         $storageroot = Settings::Get( 'reedfoto', 'storageroot' );
         $filesfolder = Settings::get( 'reedfoto', 'filesfolder', 'files' );
         $filespath = sprintf( '%s/%s/%s', getRootPath(), $storageroot, $filesfolder );
     
         if ( $this->type == 'file' ) {
            
            $fullpath = sprintf( '%s/%s/%s/%s.ext', $filespath, substr( $this->filehash, 0, 2 ), substr( $this->filehash, 2, 2 ), $this->filehash );
         
            unlink($fullpath);
            
         }
         
         parent::delete();
         
         return true;
         
      }

   }

?>
