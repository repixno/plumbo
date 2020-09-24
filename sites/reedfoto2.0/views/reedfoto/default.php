<?php
  /**
CREATE TABLE project_participants (
      id serial NOT NULL,
      project_id integer,
      startno integer,
      name text,
      club text,
      class character varying,
      groupname character varying,
      cameraid character varying,
      splitname character varying,
      splittime  interval,
      filename character varying,
      bid integer
  );
  
  CREATE TABLE project_images(
      id serial NOT NULL,
      project_id integer,
      filename character varying,
      bid integer,
      imported timestamp without time zone,
      cameraid character varying,
      splitname character varying,
      exifdate timestamp without time zone,
      timediff integer,
      splittime interval
  
  );
  
  CREATE TABLE project_info(
     id serial NOT NULL,
     userid integer,
     name text,
     date timestamp without time zone
  )
  **/
   import( 'pages.protected' );
   import( 'reedfoto.projectparticipants' );
   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   
   model( 'reedfoto.projectimages' );

   class ReedFotoDefault extends ProtectedPage implements IView {
      
      protected $template = false;
      private $rfuser = 842043;
      
      //protected $filepath = '/mnt/produksjon/div/rf_upload/';
      protected $filepath = '/mnt/storage/export/csv/';
      
      
      public function Execute(){
         
         $this->template = 'reedfoto.index';

      }

      public function addProject(){
         util::Debug( $_POST );
         $newProject = new ProjectInfo();         
         $newProject->name = $_POST['projectname'];
         $newProject->created = date( 'Y-m-d H:i:s');   
      }
      
      /*******************************
       * Used to create a temp table
       * for photoprojects
       *******************************/
      public function projectImages(){
         
         $projectuser = $this->rfuser;
         
         $fetchimages = DB::query( 'SELECT * FROM bildeinfo WHERE owner_uid = ? AND deleted_at IS NULL', $projectuser)->fetchAll( DB::FETCH_ASSOC  );

         $camdiff = array(
                  'cam1' => 1,
                  'cam2' => 1,
                  'cam3' => 1
         );

         foreach ( $fetchimages as $image ){       
            
            if( DB::query( "SELECT id FROM project_images WHERE filename ilike ?", $image['tittel'] )->fetchSingle() ){
               util::Debug( $image['tittel']  . ' finns');
            }else{
               try{
                  $projectimage = new DBProjectImages();
      
                  list( $splitname, $cameraid )  = explode( '_' , $image['tittel'] );
                  
                  $timediff = $camdiff[$cameraid];
                              
                  $projectimage->project_id = (int)1;
                  $projectimage->filename = $image['tittel'];
                  $projectimage->bid = $image['bid'];
                  $projectimage->imported =  date( 'Y-m-d H:i:s' );
                  $projectimage->cameraid =  $cameraid;
                  $projectimage->splitname =  $splitname;
                  $projectimage->exifdate =  $image['exif_date'];
                  //$projectimage->timediff =  (int)$timediff;
                  $projectimage->splittime = date( 'H:i:s', strtotime( $image['exif_date']  . "+$timediff seconds") );
                  
                  $projectimage->save();
                  util::Debug( $image['tittel']  . ' LAGT TIL' );
               }catch (Exception $e){
                  util::Debug( $e->getMessage() );
               }
            }
            
         }
         
      }
      
      public function Import( $id, $count=0 ) {
 
         //util::Debug( $id );
         //util::Debug( $count );
         
         //readfile( '/mnt/produksjon/test1/test1.txt');
         //readfile( '/var/www/repix/sites/website/views/reedfoto/test1.txt');
         
         
         $splitnamearray = array(
                  '36 km' => 'split1',
                  'Mål'   => 'split2'
         );
         
         
         foreach ( glob ( $this->filepath . 'ready/*.csv' ) as $ret ){
 
            util::Debug( $ret );
            if( file_exists( $ret  ) ){
               
               $lines = file( $ret );
               
               foreach($lines as $line){
                  
                  util::Debug( $line );

                  $row = explode( ';' , $line );
                  list( $startno, $place, $name, $club, $class, $starttime, $splitname, $splittime ) = $row;
   
                  //list( $hour, $minute, $seconds) = explode( ':' ,  $splittime );
                  //$splitseconds = ($hour * 60 * 60) + ($minute * 60) + $seconds;
                  //$date = date( "Y-m-d H:i:s", strtotime( $starttime . "+$splitseconds seconds" ) );
                  $date = date( "Y-m-d H:i:s", strtotime( $splittime ) );
                  
                  $splitlow = date("H:i:s", strtotime($date . "-5 seconds" )  );
                  $splithigh = date("H:i:s", strtotime($date . " +5 seconds" ) );
                  
                  $splitname = $splitnamearray[ trim( $splitname ) ];

                  $query = sprintf( "SELECT * FROM project_images WHERE splitname ilike '%s' AND splittime between '%s' AND '%s'" ,  $splitname, $splitlow, $splithigh );
                  //util::Debug( $query );
                  //die();

                  foreach ( DB::query( $query )->fetchAll( DB::FETCH_ASSOC ) as $images ){
                     
                     if( DB::query( "SELECT id FROM project_participants WHERE bid = ? AND startno = ? ", $images['bid'], $startno )->fetchSingle() ){
                        util::Debug( "Bilde " . $images['bid'] . " allerede koblet med løper");
                     }
                     else{
                        $participant = new ProjectParticipants();
                        $participant->project_id = 1;
                        $participant->startno = $startno;
                        $participant->name = utf8_encode( $name );
                        $participant->club = utf8_encode( $club );
                        $participant->class = utf8_encode( $class );
                        $participant->groupname = utf8_encode( $class );
                        $participant->cameraid = $cameraid;
                        $participant->splitname = $splitname;
                        $participant->splittime = date( 'H:i:s' , strtotime( $date ) );
                        $participant->filename = trim( $images['filename'] );
                        $participant->bid = $images['bid'];
                        
                        $participant->save();
                        
                        util::Debug( $participant );
                     }
                     
                  }

                 
               }
               rename( $ret ,  $this->filepath . 'done/' . basename( $ret ) );
               //file_put_contents(  $ret . "/done.txt", date( 'Y-m-d' )  );
            }
         
         
         }
         
         
      }
      
   } 
?>