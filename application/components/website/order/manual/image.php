<?php

   model( 'order.image' );

   class OrderImage extends DBOrderImage {
      
      static function fromOrderIdAndImageId( $orderid, $imageid ) {
         
         $res = DB::query( "
            SELECT 
               id 
            FROM 
               historie_bilde 
            WHERE 
               ordrenr = ? AND 
               bid = ?
         ", $orderid, $imageid );
         
         if( $res->count() > 0 ) {
            
            list( $id ) = $res->fetchRow();
            return new DBOrderImage( $id );
            
         }
         
         return null;
         
      }
      
   }


?>