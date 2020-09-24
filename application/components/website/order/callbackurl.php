<?php


   model( 'order.callbackurl' );
   
   class OrderCallbackUrl extends DBOrderCallbackUrl {
      
      static function fromPortalCode( $portalcode = 'EF-997' ) {
         
         $res = DB::query( "
            SELECT
               ordercallbackid 
            FROM
               order_callback_urls 
            WHERE 
               portalcode = ?
         ", $portalcode );
         
         if( $res->count() > 0 ) {
            
            list( $id ) = $res->fetchRow();
            return new DBOrderCallbackUrl( $id );
            
         } 
         
         return null;
         
      }
      
   }

?>