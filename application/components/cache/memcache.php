<?PHP
   
   import( 'cache.interface' );
   
   class MemCacheEngine implements ICacheEngine {
      
      private $memcache;
      private $disabled = false;
      
      public function __construct( $configsegment = 'memcache' ) {
         
         $hosts = Settings::Get( 'cache', $configsegment.'.hosts', array( 'localhost' => '11211' ) );
         
         $this->disabled = Settings::Get( 'cache', 'disabled', false );
         /*
         $this->memcache = new MemCache();
         
         if( count( $hosts ) > 0 ) {
            foreach( $hosts as $host => $port ) {
               
               try {
                  $this->memcache->addServer( $host, $port );
               } catch( Exception $e ) {}
               
            }
         }
         */
         $this->memcache = new MemCache(); 
         foreach( $hosts as $host=>$port ){
            try{
               if( $this->memcache->connect( $host, $port ) ){
                  break;
               }
              
            }catch( Exception $e ){}
            
         }
         
         while( count( $hosts ) > 0 ) {
             
            reset( $hosts ); 
            $host = key( $hosts ); 
            $port = current( $hosts ); 
            array_shift( $hosts ); 
            
            $this->memcache->addServer( $host, $port ); 
            
         }
         
      }
      
      static function available() {
         
         return extension_loaded( 'memcache' ) ? true : false;
         
      }
      
      public function add( $key, $value, $expire = 3600, $compress = false ) {
         
         if( $this->disabled ) return $value;
         
         return $this->memcache->add( $key, $value, $compress ? MEMCACHE_COMPRESSED : false, $expire );
         
      }
      
      public function replace( $key, $value, $expire = 3600, $compress = false ) {
         
         if( $this->disabled ) return $value;
         
         return $this->memcache->add( $key, $value, $compress ? MEMCACHE_COMPRESSED : false, $expire );
         
      }
      
      public function replaceOrAdd( $key, $value, $expire = 3600, $compress = false ) {
         
         if( $this->disabled ) return $value;
         
         if( !$this->memcache->replace( $key, $value, $compress ? MEMCACHE_COMPRESSED : false, $expire ) ) {
            
            return $this->memcache->add( $key, $value, $compress ? MEMCACHE_COMPRESSED : false, $expire );
            
         }
         
         return true;
         
      }
      
      public function write( $key, $value, $expire = 3600, $compress = false ) {
         
         if( $this->disabled ) return $value;
         
         return $this->memcache->set( $key, $value, $compress ? MEMCACHE_COMPRESSED : false, $expire );
         
      }
      
      public function read( $key, $default = null ) {
         
         if( $this->disabled ) return $default;
         
         $result = $this->memcache->get( $key );
         
         if( $result === false ) $result = $default;
         
         return $result;
         
      }
      
      public function erase( $key ) {
         
         if( $this->disabled ) return true;
         
         $this->memcache->delete( $key );
         
         return true;
         
      }
      
      public function increment( $key ) {
         
         if( $this->disabled ) return 1;
         
         return $this->memcache->increment( $key );
         
      }
      
      public function decrement( $key ) {
         
         if( $this->disabled ) return 0;
         
         return $this->memcache->decrement( $key );
         
      }
      
      public function clear() {
         
         if( $this->disabled ) return true;
         
         return $this->memcache->flush();
         
      }
      
   }
   
?>