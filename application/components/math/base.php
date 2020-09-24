<?PHP
   
   class MathBase {
      
      static function encode( $dec, $digits ) {
         
         $value = "";
         $base  = strlen( $digits );
         
         while( $dec > $base - 1 ) {
            $rest = bcmod( $dec, $base );
            $dec  = bcdiv( $dec, $base );
            $value = $digits[$rest].$value;
         }
         
         $value = $digits[intval($dec)].$value;
         return (string) $value;
         
      }
      
      static function decode( $value, $digits ) {
         
         $base  = strlen( $digits );
         $size  = strlen( $value );
         $dec   = 0;
         
         for( $loop = 0; $loop < $size; $loop++) {
            $element = strpos( $digits, $value[$loop] );
            $dec = bcadd( bcmul( $dec, $base ), $element );
         }
         
         return (string) $dec;
         
      }
      
   }
   
?>