<?php

   model( 'order.ukeplan' );

   class UserUkeplanOrder extends DBUkeplanOrder {
      
      static function fromIdAndUserId( $userid, $projectid ) {
         
         
         $id = (int) DB::query( "
            SELECT 
               id
            FROM 
               ukeplan_orders 
            WHERE 
               id = ?
         ", $projectid )->fetchSingle();

         
         if( $id > 0 ) {
            
            $ukeplanoder = new DBUkeplanOrder( $id );
            
            if( empty( $ukeplanoder->userid ) ){
               
               $ukeplanoder->userid = $userid;
               $ukeplanoder->save();
            }
            
            return $ukeplanoder;
            
         }
         
         return null;
         
      }
      
      
      static function toProduction( $orderid = 0) {
         
         if( $orderid == 0 ){
            $id = (int) DB::query( "
               SELECT 
                  id
               FROM 
                  ukeplan_orders 
               WHERE 
                  processed IS NULL AND
                  orderid is not null order by orderid desc limit 1
            ")->fetchSingle();
         }else{
            
            $id = (int) DB::query( "
               SELECT 
                  id
               FROM 
                  ukeplan_orders 
               WHERE 
                  orderid = ?
            ", $orderid )->fetchSingle();
            
         }
         
         if( $id > 0 ) {
            
            return new DBUkeplanOrder( $id );
            
         }
         
         return null;
         
      }
      
   }


?>