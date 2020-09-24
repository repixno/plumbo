<?PHP
   
   /**
    * Checksum library
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Checksum {
      
      /**
       * Calculates Modulus-10 checksums
       *
       * @param string $number The number to checksum
       * @return integer The checksum digit
       */
      static function mod10( $number ) {
         
			$length = strlen( $number );
			$v = 2;
			for( $i = 1; $i <= $length; $i++ ) {
				$addition = $v * ($number[( $length - $i )]);
				$result = $result + substr( $addition, 0, 1 ) + substr( $addition, 1, 2 );
				if( $v == 2 ) $v = 1; else $v++;
			}
			$mod = 10 - ( $result % 10 );
			if( $mod == 10 ) $mod = 0;
			return $mod;
			
		}
		
      /**
       * Calculates Modulus-11 checksums
       *
       * @param string $number The number to checksum
       * @return integer The checksum digit
       */
		static function mod11( $number ) {
		   
			$length = strlen( $number );
			$v = 2;
			for ($i=1; $i <= $length; $i++) {
				$result = $result + $v * ($number[( $length - $i )]);
				if( $v==7 ) $v = 2; else $v++;
			}
			$mod = 11 - ( $result % 11 );
			if ($mod == 11) $mod = 0;
			if ($mod == 10) $mod = "";
			return $mod;
			
		}
      
      /**
       * Calculates EAN-128 checksums
       *
       * @param string $number The number to checksum
       * @return integer The checksum digit
       */
		static function ean128( $ean128 ) {
			
		   $factor = 3;  
			$result = 0;
			for ($i = strlen( $ean128 ); $i > 0; $i--) {
				$result = $result + substr( $ean128, $i - 1, 1) * $factor;
				$factor = 4 - $factor;
			}
			return ( ( 1000 - $result ) % 10 );
			
		}
      
   }
   
?>