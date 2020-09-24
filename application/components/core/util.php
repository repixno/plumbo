<?PHP
   
   class Util {
        
	  static function setSSL(){
		 $hostname = $_SERVER['HTTP_HOST'];
         // validate the default domain name
         if( !$hostname ) $hostname = Settings::get( 'default', 'hostname' );
         $sites = Settings::getSection( 'domainMap' );
         $site = $sites[$hostname];
         
         if( $site['https']  && $_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https' ) {
			if( !isset( $_SERVER['HTTPS'] ) ){
			   $actual_link = $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
			   //Util::Debug("askdaskd-");
			   relocate( "https://" . $actual_link = $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI] );
			   die();
			}
         }
	  }
	  
      static function Debug() {
         
         $args = func_get_args();
         
         foreach( $args as $argument ) {
            
            echo "<pre>".print_r( $argument, true )."</pre>\n";
            
         }
         
      }
      
      static function xmlEntities( $xml ) {
         
         return str_replace( array( '&', '<', '>' ), array( '&amp;', '&lt;', '&rt;' ), $xml );
         
      }
      
      static function urlize( $string, $splitchar = '-' ) {
         
         $string = function_exists('iconv') ? iconv( 'UTF-8', 'ISO-8859-1//IGNORE//TRANSLIT', $string ) : utf8_decode( $string );
         $string = strtr( $string, "\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF\xE0\xE1\xE2\xE3\xE4\xE5\xE6\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF6\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF",
                                   'aaaaaaaceeeeiiiidnoooooxouuuuybsaaaaaaaceeeeiiiidnoooooouuuuyby' );
         
         $string = str_replace( '&', 'and', $string );
         $string = str_replace( array( "'", '"', '´', '`' ), '', $string );
         $string = trim( preg_replace( '/[^a-z0-9.-]/', $splitchar, strtolower( $string ) ) );
         for( $i = 0; $i < 3; $i++ ) $string = str_replace( '..', '.', $string );
         
         $string = str_replace( '.'.$splitchar, $splitchar, $string );
         for( $i = 0; $i < 3; $i++ ) $string = str_replace( $splitchar.$splitchar, $splitchar, $string );
         
         $char1 = substr( $string, 0, 1 );
         $charN = substr( $string,-1, 1 );
         
         if( $char1 == $splitchar || $char1 == '.' ) $string = substr( $string, 1 );
         if( $charN == $splitchar || $charN == '.' ) $string = substr( $string, 0, strlen( $string ) - 1 );
         
         return $string;
         
      }
      
      static function formatDate( $datestr ) {
         
         return date( Settings::get( 'datetime', 'dateformat', 'Y-m-d' ), strtotime( $datestr ) );
         
      }
      
      static function formatDateTime( $datestr, $second = false ) {
         
         $setting = $second ? 'datetimeformat' : 'datetimeformatnosec';
         $default = $second ? 'Y-m-d H:i:s' : 'Y-m-d H:i';
         
         return date( Settings::get( 'datetime', $setting, $default ), strtotime( $datestr ) );
         
      }
      
      static function formatPrice( $price, $accuracy = 2 ) {
         
         $accuracy = is_null( $accuracy ) ? 2 : (int) $accuracy;
         return number_format( $price, $accuracy, ',', ' ' );
         
      }
      
      static function diffArrays( $array1, $array2 ) {
         
         if( !is_array( $array1 ) && is_array( $array2 ) ) return $array2;
         if( !is_array( $array2 ) && is_array( $array1 ) ) return $array1;
         if( !is_array( $array1 ) && !is_array( $array2 ) ) return array();
         
			foreach( $array2 as $key => $val ) {

				if( $val && isset( $array1[$key] ) ) {

					if( is_array( $array2[$key] ) ) {

						$array1[$key] = util::diffArrays( $array1[$key], $array2[$key] );

						if( is_array( $array1[$key] ) && !count( $array1[$key] ) ) {

							unset( $array1["$key"] );

						}

					} else {

						unset( $array1["$key"] );

					}

				}

			}

			return $array1;

		}

		static function mergeArrays( $array1, $array2 ) {

			if( !is_array( $array1 ) && is_array( $array2 ) ) return $array2;
         if( !is_array( $array2 ) && is_array( $array1 ) ) return $array1;
         if( !is_array( $array1 ) && !is_array( $array2 ) ) return array();
         
         foreach( $array2 as $key => $val ) {

				if( is_array( $array1[$key] ) ) {

					$array1[$key] = util::mergeArrays( $array1[$key], $array2[$key] );

				} else {

					$array1[$key] = $val;

				}

			}
         
			return $array1;

		}
      
   }
   
?>