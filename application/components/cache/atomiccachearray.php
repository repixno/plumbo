<?php
   
	// we require memcache support
	//include_once "memcache.php";
	import( 'cache.engine' );
	
	
	/**
	 * Secure Memory Cached Atomic Array Cache
	 * 
	 * @author Oyvind Selbek <oyvind@selbek.com>
	 */
	class AtomicCacheArray implements ArrayAccess, Countable {
	   
	   const EXPIRYTIME = 30;
	   const WAITUTIME = 101;
	   const MAXTRIES = 100000;
	   
	   protected $key;
	   protected $lock;
	   protected $expire = 3600;
	   
	   public $memcache;
	   
	   /**
	    * Constructor - sets the array key in the object
	    *
	    * @param string $key
	    */
	   public function __construct( $key, $expire = 3600 ) {
	      
	      $this->key = $key;
	      $this->lock = 'lock:'.$key;
	      $this->expire = $expire;
	      $this->memcache = MemCacheSessionConnection::current();
	      
	   }
	   
	   /**
	    * Aquires an atomic lock, ensuring no two operations goes on at the same time.
	    *
	    * @return boolean false on failure.
	    */
	   private function aquireLock() {
	      
   	   // 
         for( $tries = 0; $tries < AtomicCacheArray::MAXTRIES; ++$tries ) {
            
            // attempt to add the lock to the cache
            //if( memCacheAdd( $this->lock, 1, false, AtomicCacheArray::EXPIRYTIME ) ) { break; }
            if( $this->memcache->write( $this->lock, 1, false, AtomicCacheArray::EXPIRYTIME ) ) { break; }
            
            // sleep and try again
            usleep( AtomicCacheArray::WAITUTIME );
            
         }
         
         if( $tries == AtomicCacheArray::MAXTRIES ) {
            
            return false;
            
         }
	      
	   }
	   
	   /**
	    * Releases an already aquired lock. Failure to release a lock, will cause it to never be reaquireable.
	    *
	    * @return boolean true on success.
	    */
	   private function releaseLock() {
	      
	      // clear the lock
         //memCacheErase( $this->lock );
         $this->memcache->erase( $this->lock );          
         // return success
   		return true;
	      
	   }
	   
	   /**
	    * Internal function for reading the actual array using the platforms MemCache API
	    *
	    * @return array that was read from the key
	    */
	   private function readArray() {
	      
	      //$array = memCacheRead( 'AtomicCacheArray['.$this->key.']' );
	      $array = $this->memcache->read( 'AtomicCacheArray['.$this->key.']' );
	      if( !is_array( $array ) ) $array = array();
	      
	      return $array;
	      
	   }
	   
	   /**
	    * Internal function for writing the actual array using the platforms MemCache API
	    *
	    * @param array $array The array to write.
	    */
	   private function writeArray( $array ) {
	      
	      //memCacheReplaceOrAdd( 'AtomicCacheArray['.$this->key.']', $array, false, 3600 );
	      $this->memcache->write( 'AtomicCacheArray['.$this->key.']', $array, false, $this->expire );
	      
	   }
	   
	   /**
	    * Adds an item to this array
	    *
	    * @param mixed $item Some kind of string or numerical value
	    */
	   public function Set( $item, $id = null ) {
	      
	      // aquire an atomic lock
	      $this->aquireLock();
	      
	      // read the array
	      $array = $this->readArray();
	      
	      // append the item
	      if( empty( $id ) ) {
	         $array[] = $item;
	      } else {
	         $array[$id] = $item;
	      }
	      
	      // write the array back
	      $this->writeArray( $array );
		   
	      // release the lock
		   $this->releaseLock();
		   
	   }
	   
	   
	   static function addItem( $key, $item ) {
	      $array = new AtomicCacheArray( $key );
	      return $array->set( $item );
      }
      
      static function setItem( $key, $item, $id ) {
	      $array = new AtomicCacheArray( $key );
	      return $array->set( $item, $id );
      }
      
	   static function getItem( $key, $id ) {
	      $array = new AtomicCacheArray( $key );
         return $array->get( $id );
	   }
	   
	   /**
	    * Removes an item from this array
	    *
	    * @param mixed $item Some kind of string or numerical value
	    */
	   public function Remove( $id ) {
	      
	      $this->offsetUnset( $id );
	      
	   }
	   
	   
	   /**
	    * Removes an item from this array
	    *
	    * @param string $key
	    * @param mixed $item Some kind of string or numerical value
	    * @return unknown
	    */
	   static function removeItem( $key, $item ) {
	      
	      $array = new AtomicCacheArray( $key );
	      return $array->Remove( $item );
	      
	   }
	   
	   
	   /**
	    * Clears the array entirely
	    */
	   public function Clear() {
	      
	      // aquire an atomic lock
	      $this->aquireLock();
	      
	      // write the empty array
	      $this->writeArray( array() );
		   
	      // release the lock
		   $this->releaseLock();
	      
	   }
	   
	   
	   /**
	    * Clears the array entirely
	    */
	   static function clearItems( $key ) {
	      
	      $array = new AtomicCacheArray( $key );
	      return $array->Clear();
	      
	   }
	   
	   
      /**
       * Retreives the array and returns it
       *
       * @param string $id Optional sub ID to fetch
       * @return Array the stored array.
       */
	   public function Get( $id = null ) {
	      
	      // aquire an atomic lock
	      $this->aquireLock();
	      
	      // read the array
	      $array = $this->readArray();
	      
	      // release the lock
		   $this->releaseLock();
	      
		   // return the array values
		   return isset( $id ) ? ( isset( $array[$id] ) ? $array[$id] : null ) : $array;
		   
	   }
	   
	   static function getItems( $key ) {
	      
	      $array = new AtomicCacheArray( $key );
         return $array->get();
         
	   }
	   
	   /**
       * Implements ArrayAccess::offsetGet()
       *
       * @param string $offset The offset to retreive 
       * @return mixed The value stored at the offset
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetGet( $offset ) {
         
         // aquire an atomic lock
	      $this->aquireLock();
	      
	      // read the array
	      $array = $this->readArray();
	      
	      // remove the offset
	      $result = $array[$offset];
	      
	      // release the lock
		   $this->releaseLock();
         
		   // return it
		   return $result;
		   
      }
      
      /**
       * Implements ArrayAccess::offsetSet()
       *
       * @param string $offset The offset to set 
       * @param mixed $value The value to store
       * @return mixed The value set if successful, null otherwise
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetSet( $offset, $value ) {
         
         // aquire an atomic lock
	      $this->aquireLock();
	      
	      // read the array
	      $array = $this->readArray();
	      
	      // set the offset
	      $array[$offset] = $value;
	      
	      // write the array
	      $this->writeArray( $array );
	      
	      // release the lock
		   $this->releaseLock();
         
		   // return set value
		   return $value;
		   
      }
      
      /**
       * Implements ArrayAccess::offsetExists()
       * 
       * @param string $offset The offset to validate
       * @return boolean True if the offset is valid
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetExists( $offset ) {
         
         // aquire an atomic lock
	      $this->aquireLock();
	      
	      // read the array
	      $array = $this->readArray();
	      
	      // is there a field of this name defined?
         $result = isset( $array[$offset] );
	      
	      // release the lock
		   $this->releaseLock();
         
		   // return it
         return $result;
         
      }
      
      /**
       * Implements ArrayAccess::offsetUnset()
       * 
       * @param string $offset The offset to unset
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetUnset( $offset ) {
         
         // aquire an atomic lock
	      $this->aquireLock();
	      
	      // read the array
	      $array = $this->readArray();
	      
	      // remove the offset
	      unset( $array[$offset] );
	      
	      // write the array
	      $this->writeArray( $array );
	      
	      // release the lock
		   $this->releaseLock();
         
      }
      
      /**
       * Implements Countable::count()
       *
       * @return integer The number of fields in this class
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function count() {
         
         // proxy to the count method
         return count( $this->get() );
         
      }
      
      public function __toString() {
         
         $result = '';
         foreach( $this->get() as $key => $val ) {
            $result .= "$key:\t".print_r( $val, true )."\n";
         }
         return $result;
         
      }
	   
	}
	
?>
