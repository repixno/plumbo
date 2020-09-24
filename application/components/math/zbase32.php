<?php
   
   import( 'math.base' );
   
   class zBase32 {
      
      static $characters = 'ybndrfg8ejkmcpqxt1uwisa2345h769';
      
      static function encode( $integer ) {
         
         return MathBase::encode( $integer, zBase32::$characters );
         
      }
      
      static function decode( $string ) {
         
         return MathBase::decode( $string, zBase32::$characters );
         
      }
      
   }
   
?>