<?PHP
   
   /**
    * Settings class.
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Settings {
      
      /**
       * @var array settings Holds the settings
       */
      static $settings = array();
      
      /**
       * Gets a value from a spesific section
       *
       * @param string $section
       * @param string $key
       * @return mixed
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function Get( $section, $key, $default = null ) {
         
         // return the value from the given section
         return isset( Settings::$settings[$section][$key] ) 
                     ? Settings::$settings[$section][$key] 
                     : $default;
         
      }
      
      /**
       * Sets a value in a spesific section
       *
       * @param string $section
       * @param string $key
       * @param mixed $value
       * @return mixed
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function Set( $section, $key, $value ) {
         
         // set and return the value being set
         return Settings::$settings[$section][$key] = $value;
         
      }
      
      /**
       * returns whether a given key has been set in a given section
       *
       * @param string $section
       * @param string $key
       * @return boolean True if it exists, otherwise false
       */
      static function Has( $section, $key ) {
         
         // set and return the value being set
         return isset( Settings::$settings[$section][$key] ) ? true : false;
         
      }
      
      /**
       * Sets an entire section from an array
       *
       * @param string $section The section that you want to set
       * @param array $values An array of key/value pairs to set
       * @return boolean True on success, false if values is invalid
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function SetSection( $section, $values ) {
         
         // is this an array of key/values?
         if( is_array( $values ) ) {
            
            // iterate the array and...
            foreach( $values as $key => $value ) {
               
               // ...store it in the settings value
               Settings::$settings[$section][$key] = $value;
               
            }
            
            // return successful
            return true;
            
         } else {
            
            // return failure
            return false;
            
         }
         
      }
      
      static function GetSection( $section, $default = null ) {
         
         return isset( Settings::$settings[$section] ) 
                     ? Settings::$settings[$section] 
                     : $default;
         
      }
      
      /**
       * Returns whether a given section has been set
       *
       * @param string $section
       * @return boolean True if it exists, otherwise false
       */
      static function HasSection( $section ) {
         
         // set and return the value being set
         return isset( Settings::$settings[$section] ) ? true : false;
         
      }
      
   }
   
?>