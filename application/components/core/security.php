<?php
   
   define( 'SECURITY_KEY_ROOTKEY', 'rootkey' );
   define( 'SECURITY_KEY_SEEDKEY', 'seedkey' );
   define( 'SECURITY_KEY_SESSION', 'session' );
   
   /**
    * Security Manager / Key Store implementation
    * Holds session/encryption keys for runtime access/validation
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class SecurityKeys {
      
      static $securitykeys;
      
      public function __get( $key ) {
         
         return SecurityKeys::get( $key );
         
      }
      
      public function __set( $key, $value ) {
         
         return SecurityKeys::set( $key, $value );
         
      }
      
      static function get( $key, $default = null ) {
         
         return isset( SecurityKeys::$securitykeys[$key] ) ? SecurityKeys::$securitykeys[$key] : $default;
         
      }
      
      static function set( $key, $value ) {
         
         return SecurityKeys::$securitykeys[$key] = $value;
         
      }
      
      static function setKeys( $array ) {
         
         if( is_array( $array ) ) {
            
            SecurityKeys::$securitykeys = $array;
            
         }
         
      }
      
   }
   
   /**
    * Holds a registry of secret information that should not be echoed to stdout.
    * Certain features in the system will use the filterString() method to filter
    * their output prior to echoing memory dumps that might contain sensitive data.
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class SecretInformation {
      
      static $secretinfo = array();
      
      static function register( $string ) {
         
         if( !empty( $string ) ) {
            
            SecretInformation::$secretinfo[] = $string;
            
         }
         
      }
      
      static function filterString( $string ) {
         
         foreach( SecretInformation::$secretinfo as $value ) {
            
            $string = str_replace( $value, str_repeat( '*', strlen( $value ) ), $string );
            
         }
         
         return $string;
         
      }
      
      
   }
   
?>