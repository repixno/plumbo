<?PHP
   
   interface ICacheEngine {
      
      static function available();
      
      public function write( $key, $value, $expire = 3600 );
      
      public function read( $key, $default = null );
      
      public function erase( $key );
      
      public function increment( $key );
      
      public function decrement( $key );
      
      public function clear();
      
   }
   
   interface Cacheable {
      
      public function getCacheIdentifier();
      
   }
   
   interface CacheableDataOnly extends Cacheable {
      
      public function getCacheData();
      
      public function setCacheData( $data );
      
   }
   
?>