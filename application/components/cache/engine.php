<?PHP
   
   // import supported types
   import( 'cache.memcache' );
   import( 'cache.diskcache' );
   
   /**
    * Handles dynamic creation of the correct cache engine
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class CacheEngineFactory {
      
      static $defaultInstance;
      
      static function create( $name = null ) {
         
         if( isset( $name ) ) {
            
            switch( $name ) {
               
               case 'memcache':
                  // fall back to diskcache if extension not available
                  if( MemCacheEngine::available() ) {
                     return new MemCacheEngine();
                     break;
                  }
                  
               default:
                  return new DiskCacheEngine();
                  break;
                  
            }
            
         } else {
            
            if( MemCacheEngine::available() ) {
               
               return new MemCacheEngine();
               
            } else {
               
               return new DiskCacheEngine();
               
            }
            
         }
         
      }
      
      static function current() {
         
         if( !CacheEngineFactory::$defaultInstance instanceof ICacheEngine ) {
            
            CacheEngineFactory::$defaultInstance = CacheEngineFactory::create();
            
         }
         
         return CacheEngineFactory::$defaultInstance;
         
      }
      
   }
   
   class CacheEngine extends CacheEngineFactory {
      
      static function write( $key, $value, $expire = 3600 ) {
         
         $engine = CacheEngineFactory::current();
         return $engine->write( $key, $value, $expire );
         
      }
      
      static function read( $key, $default = null ) {
         
         $engine = CacheEngineFactory::current();
         return $engine->read( $key, $default );
         
      }
      
      static function erase( $key ) {
         
         $engine = CacheEngineFactory::current();
         return $engine->erase( $key );
         
      }
      
      static function increment( $key ) {
         
         $engine = CacheEngineFactory::current();
         return $engine->increment( $key );
         
      }
      
      static function decrement( $key ) {
         
         $engine = CacheEngineFactory::current();
         return $engine->decrement( $key );
         
      }
      
      static function clear() {
         
         $engine = CacheEngineFactory::current();
         return $engine->clear();
         
      }
      
   }
   
?>