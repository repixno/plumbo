<?php

model( 'reedfoto.projectparticipants' );


class ProjectParticipants extends DBProjectParticipants {
   
   die();
      
      static function find( $string, $projectid  ){
         
         $string = trim( $string );

         try{
         
            if( is_numeric( $string ) ){
               $string = (int)$string;  
               $query = sprintf( "SELECT * from project_participants WHERE startno = %d AND project_id = %d order by splitname, filename" , $string, $projectid );
            }
            else{
               if( strlen( $string ) > 0 ){
                  $query = sprintf( "SELECT * from project_participants WHERE  name ilike '%s' AND project_id = %d ORDER BY splitname, filename" ,  '%'  . $string  . '%', $projectid );
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
      
      static function findParticipants( $string, $projectid ){
         
         
         $string = trim( $string );

         try{
         
            if( is_numeric( $string ) ){
               $string = (int)$string;  
               $query = sprintf( "SELECT startno, max( name ) as name  from project_participants WHERE startno = %d AND project_id = %d group by startno" , $string, $projectid );
            }
            else{
               if( strlen( $string ) > 2 ){
                  $query = sprintf( "SELECT startno, max( name ) as name  from project_participants WHERE  name ilike '%s' AND project_id = %d group by startno ORDER BY name LIMIT 50" ,  '%'  . $string  . '%', $projectid );
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