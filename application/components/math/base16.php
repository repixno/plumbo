<?php
   
   import( 'math.base' );
   
   class Base16 {
      
      static $characters = '0123456789abcdef';
      
      static function encode( $integer ) {
         
         return MathBase::encode( $integer, Base16::$characters );
         
      }
      
      static function decode( $string ) {
         
         return MathBase::decode( $string, Base16::$characters );
         
      }
      
   }
   
?>