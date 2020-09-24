<?PHP
   
   import( 'cache.interface' );
   
   class DiskCacheEngine implements ICacheEngine {
      
      private $disabled = false;
      private $cachedir = '/tmp/';
      
      public function __construct() {
         
         $this->disabled = Settings::Get( 'cache', 'disabled', false );
         $this->cachedir = Settings::Get( 'cache', 'diskcache.cachedir', $this->cachedir );
         $this->cachedir = realpath( $this->cachedir );
         
         if( $this->cachedir == '' ) {
            
            throw new CriticalException( 'DiskCache: CacheDir not found! Check system configuration!' );
            
         }
         
      }
      
      static function available() {
         
         return is_writeable( $this->cachedir ) ? true : false;
         
      }
      
      private function getDiskFile( $key ) {
         
         return sprintf( '%s/diskcache_%s', $this->cachedir, md5( $key ) );
         
      }
      
      public function write( $key, $value, $expire = 3600 ) {
         
         if( $this->disabled ) return $value;
         
         file_put_contents( $this->getDiskFile( $key ), serialize( $value ) );
         
         return $value;
         
      }
      
      public function read( $key, $default = null ) {
         
         if( $this->disabled ) return $default;
         
         $diskfile = $this->getDiskFile( $key );
         
         if( !file_exists( $diskfile ) ) return $default;
         
         return unserialize( file_get_contents( $diskfile ) );
         
      }
      
      public function erase( $key ) {
         
         if( $this->disabled ) return true;
         
         $diskfile = $this->getDiskFile( $key );
         if( !file_exists( $diskfile ) ) return false;
         
         unlink( $diskfile );
         
         return true;
         
      }
      
      public function increment( $key ) {
         
         if( $this->disabled ) return 1;
         
         $diskfile = $this->getDiskFile( $key );
         if( !file_exists( $diskfile ) ) {
            
            $value = (int) 0;
            
         } else {
            
            $value = (int) file_get_contents( $diskfile );
            
         }
         
         $value++;
         
         return $this->write( $key, $value );
         
      }
      
      public function decrement( $key ) {
         
         if( $this->disabled ) return 0;
         
         $diskfile = $this->getDiskFile( $key );
         if( !file_exists( $diskfile ) ) {
            
            $value = (int) 0;
            
         } else {
            
            $value = (int) file_get_contents( $diskfile );
            
         }
         
         $value--;
         
         return $this->write( $key, $value );
         
      }
      
      public function clear() {
         
         if( $this->disabled ) return true;
         
         foreach( new DirectoryIterator( $this->cachedir ) as $entry ) {
            $filename = $entry->getFilename();
            if( substr( $filename, 0, 1 ) == '.' ) continue;
            if( substr( $filename, 0, 9 ) != 'diskcache' ) continue;
            unlink( sprintf( '%s/%s', $this->cachedir, $filename ) );
         }
         
         return true;
               
      }

   }
   
?>