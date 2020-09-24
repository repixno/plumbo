<?php

   /**
    * Component for clipart to gift orders
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'gift.order.clipart' );
   
   class GiftOrderClipart extends DBGiftOrderClipart {
      static function enumClipartFromTemplateIdAndPageId( $templateid, $pageid ) {
         
         $cliparts = array();
         
         $res = DB::query( "
            SELECT 
               malid,
               clipid,
               x,
               y,
               dx,
               dy,
               clipnr,
               page
            FROM 
               mal_clipart 
            WHERE 
               malid = ? AND
               page = ? 
            ORDER BY 
               clipnr
         ", $templateid, $pageid );
         
         if( $res->count() > 0 ) {
            
            while( list( $id, $clipid, $x, $y, $dx, $dy, $clipnr, $page ) = $res->fetchRow() ) {
               
               $clipart = array(
                  'malid'     => $id,
                  'clipid'    => $clipid,
                  'x'         => $x,
                  'y'         => $y,
                  'dx'        => $dx,
                  'dy'        => $dy,
                  'clipnr'    => $clipnr,
                  'page'      => $page,
               );
               
               $cliparts []= $clipart;
               
            }
            
         }
         
         return $cliparts;
         
      }
      
   }


?>