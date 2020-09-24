<?php

   /**
    * Component for text to gift orders
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   model( 'gift.order.text' );
   
   class GiftOrderText extends DBGiftOrderText {
      
      static function enumTextsFromTemplateIdAndPageId( $templateid, $pageid ) {
         
         $texts = array();
         
         $res = DB::query( "
            SELECT 
               malid,
               textid,
               text,
               font,
               color,
               x,
               y,
               dx,
               dy,
               page,
               gravity,
               shadow
            FROM 
               mal_text 
            WHERE 
               malid = ? AND
               page = ? 
            ORDER BY 
               textid
         ", $templateid, $pageid );
         
         if( $res->count() > 0 ) {
            
            while( list( $id, $textid, $text, $font, $color, $x, $y, $dx, $dy, $page, $gravity, $shadow ) = $res->fetchRow() ) {
               
               $text = array(
                  'malid'     => $id,
                  'textid'    => $textid,
                  'text'      => $text,
                  'font'      => $font,
                  'color'     => $color,
                  'x'         => $x,
                  'y'         => $y,
                  'dx'        => $dx,
                  'dy'        => $dy,
                  'page'      => $page,
                  'gravity'   => $gravity,
                  'shadow'    => $shadow
               );
               
               $texts []= $text;
               
            }
            
         }
         
         return $texts;
         
      }
      
      
   }

?>