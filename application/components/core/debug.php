<?PHP
   
   // depends on atomic cache-array
   import( 'cache.atomiccachearray' );
   
   /**
    * Debug framework, class component
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Debug {
      
      /**
       * Create a unique identifier for identifying debug data across sessions
       *
       * For now, just the IP-address is used, based on this reasoning:
       *  - browser-ident string was not chosen since one might wanna debug
       *    being logged out in one browser while being logged into the admin
       *    tool in another.
       *  - session ident was not chosen for the same reason.
       *  - a debug key for developers was not chosen, since you might not be
       *    logged in at all times.
       *  - login-uid etc was not chosen for the same reason.
       *  - developers seldomly share a public IP-address towards the system.
       * 
       * @return string The unique string, based on the users IP-address
       */
      static function uniqueIdentifier() {
         return $_SERVER['REMOTE_ADDR'];
      }
      
      /**
       * Returns an atomic (single-instance) cache-array of the identifier 
       * given, or using the uniqueIdentifier constructor if none is supplied.
       *
       * @param string $identifier Optional identifier to use
       * @return AtomicCacheArray Instance of an AtomicCacheArray object
       */
      static function getCacheArray( $identifier = null ) {
         return new AtomicCacheArray( sprintf( 'debug_%s', $identifier ? $identifier : Debug::uniqueIdentifier() ) );
      }
      
      /**
       * Write/Append an object, string or structure to the stream.
       *
       * @param mixed $mixed the object, string or structure to append to the stream.
       * @param string $identifier Optional unique identifier
       * @return boolean true
       */
      static function Write( $mixed, $identifier = null ) {
         $cache = Debug::getCacheArray( $identifier );
         $cache->Set( array( microtime(true), $mixed ) );
         return true;
      }
      
      /**
       * Reads the current bitstream of objects, structures and strings
       *
       * @param string $identifier Optional unique identifier
       * @return array Array of items (optionally empty).
       */
      static function Read( $identifier = null ) {
         $cache = Debug::getCacheArray( $identifier );
         $values = $cache->Get();
         $cache->Clear();
         return $values;
      }
      
   }
   
?>