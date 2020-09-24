<?php

   model( 'order.merkelapp' );

   class UserMerkelappOrder extends DBMerkelappOrder  {
      
      static function toProduction() {
         
         $id =  DB::query( "
            SELECT 
               id
            FROM 
               merkelapp_orders 
            WHERE 
               orderid IS NOT NULL
               AND processed IS NULL
         ")->fetchAll( DB::FETCH_ASSOC );

         if( count( $id )  > 0 ) {
            return $id;            
         }
         
         return null;
         
      }
      
      static function userProjects( $userid ){
         $projects = array();
         foreach ( DB::query( "SELECT * FROM merkelapp_orders WHERE userid=? AND orderid IS NOT NULL", $userid )->fetchAll( DB::FETCH_ASSOC ) as $item ) {
            
            $product = ProductOption::fromRefId( $item[ 'articleid' ] );
            
            $productoptionid = $product->asArray();
            
            $projects[] = array(
               'id' => $item[ 'id' ],
               'title' => $productoptionid['title'],
               'userid' => $item[ 'userid' ],
               'orderid' => $item[ 'orderid' ],
               'articleid' => $item[ 'articleid' ],
               'quantity' => $item[ 'quantity' ],
               'productoptionid'  => $productoptionid['id']
               );
         }
         return $projects;
      }
      
   }


?>