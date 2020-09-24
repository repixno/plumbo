<?php

model( 'reedfoto.projectparticipantsnew' );


class ProjectParticipantsNew extends DBProjectParticipantsNew {
   
    private $securecode = '2f1f4567bad3ce1cfa';
      
    static function find( $string, $projectid  ){
         
        $string = trim( $string );
         
        $splitnamearray = array(
                  'split1' => '35 km',
                  'split2' => '67 km',
                  'split4' => 'Mål'
            );

         try{
         
            $image = array();
            $query = sprintf( "SELECT * from project_participants_new WHERE startno = %d AND project_id = %d order by splitname" , $string, $projectid );

            foreach ( DB::query( $query )->fetchAll(  DB::FETCH_ASSOC ) as $res  ){
                
                $date = date( "Y-m-d H:i:s", strtotime( $res['splittime'] ) );
                if(  $res['project_id'] == 1 ){
                    if ( $res['splitname'] == "split1" ) {
                        // cam 1 og 2
                        $splitlow = date("H:i:s", strtotime($date . "-12 seconds" )  );
                        $splithigh = date("H:i:s", strtotime($date . " +12 seconds" ) );
                    }
                    else { 
                        // cam 3 og 4
                        $splitlow = date("H:i:s", strtotime($date . "-5 seconds" )  );
                        $splithigh = date("H:i:s", strtotime($date . " +5 seconds" ) );   
                    }
                }else{
                    if ( $res['splitname'] == "split2" ) {
                        // cam 1 og 2
                        $splitlow = date("H:i:s", strtotime($date . "-12 seconds" )  );
                        $splithigh = date("H:i:s", strtotime($date . " +12 seconds" ) );
                    }
                    else {
                        // cam 3 og 4
                        $splitlow = date("H:i:s", strtotime($date . "-5 seconds" )  );
                        $splithigh = date("H:i:s", strtotime($date . " +5 seconds" ) );   
                    }
                    
                    
                }
                
                $imagequery = DB::query( "SELECT * FROM project_images WHERE project_id = ? AND splitname ilike ? AND splittime between ? AND ? order by splittime" , $projectid , $res['splitname'], $splitlow, $splithigh )->fetchAll( DB::FETCH_ASSOC );
                  
                foreach ( $imagequery as $images ){
                    
                    PermissionManager::current()->grantAccessTo( $images['bid'], 'image', PERMISSION_SHARED );
                    $tmpimage = new Image( $images['bid'] );
                    $securecode = base64_encode( md5(  $images['bid'] . '2f1f4567bad3ce1cfa' ) . '_' . $images['bid']  );
                    $key = $splitnamearray[$images['splitname']];
                    $image[$key][] = array(
                        'image' => $tmpimage->asArray(),
                        'securecode' => $securecode,
                        'splitname'      => $images['splitname']
                    );
                    
                }
               
                
            }
            
         }catch ( Exception  $e ){
            
            util::Debug( $e->getMessage() );
            
         }
         
         return $image;

      }
      
      static function findParticipants( $string, $projectid ){         
         $string = trim( $string );
         try{
            if( is_numeric( $string ) ){
               $string = (int)$string;  
               $query = sprintf( "SELECT startno, max( name ) as name  from project_participants_new WHERE startno = %d AND project_id = %d group by startno" , $string, $projectid );
            }
            else{
               if( strlen( $string ) > 2 ){
                  $query = sprintf( "SELECT startno, max( name ) as name  from project_participants_new WHERE  name ilike '%s' AND project_id = %d group by startno ORDER BY name LIMIT 50" ,  '%'  . $string  . '%', $projectid );
               }
            }
   
            $ret = array();
            foreach ( DB::query( $query )->fetchAll(  DB::FETCH_ASSOC ) as $res  ){
               $ret[] = $res;               
            }
            
         }catch ( Exception  $e ){
            
            util::Debug( $e->getMessage() );
            
         }

         return $ret;

      }
   
   
   

   
}














?>