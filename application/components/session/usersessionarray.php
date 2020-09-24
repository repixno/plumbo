<?php

   /**
    * UserSessionArray - used to store "session" info in
    * memcache so not to burden session.
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * @author Oyvind Selbek <oyvind@interweb.no>
    */
   
   
   import( 'cache.atomiccachearray' );
   
   class UserSessionArray extends AtomicCacheArray {
      
      /**
	    * Constructor - sets the array key in the object
	    *
	    * @param string $key
	    */
	   public function __construct( $key ) {
	      
	      $result = parent::__construct( $key );
	      
	      $this->key = Session::id()."_".$key;
	      $this->lock = 'lock:'.Session::id()."_".$key;
	      $this->expire = 86400;
	      
	      return $result;
	      
	   }
      
	   static function addItem( $key, $item ) {
	      $array = new UserSessionArray( $key );
	      return $array->set( $item );
      }
      
      static function setItem( $key, $item, $id ) {
	      $array = new UserSessionArray( $key );
	      return $array->set( $item, $id );
      }
      
      static function removeItem( $key, $item ) {
	      
	      $array = new UserSessionArray( $key );
	      return $array->Remove( $item );
	      
	   }
	   
	   static function clearItems( $key ) {
	      
	      $array = new UserSessionArray( $key );
	      return $array->Clear();
	      
	   }
	   
	   static function getItems( $key ) {
	      
	      $array = new UserSessionArray( $key );
         return $array->get();
         
	   }
      
	   static function getItem( $key, $id ) {
	      $array = new UserSessionArray( $key );
         return $array->get( $id );
	   }
      
   }

?>