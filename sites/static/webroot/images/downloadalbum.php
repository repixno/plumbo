<?php

   model( 'album.downloads');

   class downloadAlbum extends Userpage implements IView {
      
      protected $template = false;
   
      public function Execute( $aid = 0  ){
         
         try{
            $aid = (int)$aid;
            if( $aid > 0 ) {
               $user = Login::userid();
               $res = DBAlbumDownloads::fromFieldValue(
                  array(
                     'uid' => $user,
                     'aid' => $aid
                  ),
                  'DBAlbumDownloads',
                  false
               );
               
               if( !empty( $res ) ) {
                  $id = $res->id;
                  $jobname = $res->job_name;
                  $filepath = sprintf( '/data/global/album_downloads/%s.zip' , $jobname );
                  
                  
                  if( !empty( $jobname ) && file_exists( $filepath ) ) {
                     
                     // Set the file as downloaded
                     $res->downloaded = date('Y-m-d H:i:s');
                     $res->save();
                     
                     // We'll be outputting a zip
                     header('Content-type: application/zip');
                     header('Content-Disposition: attachment; filename="'.$jobname.'.zip"');
                     header("Content-Transfer-Encoding: binary");
                     header("Content-Length: ".filesize( $filepath ) ); 
                     readfile( $filepath );
                     die();
                  }
               }
               else{
                  header("HTTP/1.0 404 not found");
                  exit( 0 );
               }
            }
         }catch ( Exception $e ){
            header("HTTP/1.0 404 not found");
            exit( 0 );
         }
         
      }
   
   }














?>