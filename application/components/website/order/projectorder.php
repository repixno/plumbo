<?php

   model( 'user.projectorder' );

   class UserProjectOrder extends DBUserProjectOrder {
      
      static function fromIdAndUserId( $userid, $projectid ) {
         
         $id = (int) DB::query( "
            SELECT 
               id
            FROM 
               mediaclip_orders 
            WHERE 
               user_id = ? AND 
               id = ?
         ", $userid, $projectid )->fetchSingle();
         
         if( $id > 0 ) {
            
            return new DBUserProjectOrder( $id );
            
         }
         
         return null;
         
      }
      
   }


?>