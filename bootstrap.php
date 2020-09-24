<?PHP
   
   /**
    * Some usefull syntax improving defines
    */
   define( 'NULL', null );
   
   /**
    * Returns the rootpath of the installation,
    * aka the path in which this file resides.
    *
    * @return string The path to the folder that holds this file.
    */
   function getRootPath() {
      
      // return the path
      return dirname( __FILE__ );
      
   }
   
   /**
    * Define the base exception types, critical and security
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class CriticalException extends Exception {}
   class SecurityException extends Exception {}
   class ImportException extends CriticalException {}
   
   /**
    * Define the storage class for imported modules
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Import {
      
      static $resourcetype = 'components';
      static $allowmissing = false;
      static $imports = array( 'configuration' => array(),
                               'application/components' => array(),
                               'application/elements' => array(),
                               'application/library' => array(),
                               'application/models' => array(),
                             );
      
      /**
       * Loads a class resource from disk
       *
       * @return boolean true on success, casts an ImportException and returns false on failure.
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function loadResource( $args, $useRootPath = false ) {
         
         $args[0] = str_replace( '.', '/', $args[0] );
         $file = sprintf( '%s/%s/%s.php', getRootPath(), Import::$resourcetype, call_user_func_array( 'sprintf', $args ) );
         
         if( !file_exists( $file ) ) {
            throw new ImportException( sprintf( 'Error: Unable to import missing %s: %s', Import::$resourcetype, $file ), 1000 );
         }

         $path = getcwd();
         
         if( $useRootPath) {
            chdir( sprintf( '%s/%s/', getRootPath(), Import::$resourcetype ) );
         } else {
            chdir( dirname( $file ) );
         }
         
         include_once( $file );
         
         chdir( $path );
         
         return true;
         
      }
      
   }
   
   /**
    * Generic module import function
    *
    * @return boolean true on success, casts an ImportException and returns false on failure.
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   function import() {
      
      Import::$resourcetype = 'application/components';
      
      $args = func_get_args();
      
      return Import::loadResource( $args );
      
   }
      
   /**
    * Generic model import function
    *
    * @return boolean true on success, casts an ImportException and returns false on failure.
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   function model() {
      
      Import::$resourcetype = 'application/models';
      
      $args = func_get_args();
      
      return Import::loadResource( $args );
      
   }
   
   /**
    * Generic library import function
    *
    * @return boolean true on success, casts an ImportException and returns false on failure.
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   function library() {
      
      Import::$resourcetype = 'application/library';
      
      $args = func_get_args();
      
      return Import::loadResource( $args, true );
      
   }
   
   /**
    * Generic configuration loader function
    *
    * @return boolean true on success, casts an ImportException and returns false on failure.
    */
   function config() {
      
      Import::$resourcetype = 'configuration';
      
      $args = func_get_args();
      
      return Import::loadResource( $args );
      
   }
   
   /**
    * Override configuration loader function
    *
    * @return boolean true on success, returns false and does not throw an exception on failure.
    */
   function override( $config ) {
      
      @include( getRootPath()."/configuration/".str_replace( '.', '/', $config ).'.php' );
      return true;
      
   }
   
   /**
    * Generic element loader function
    *
    * @return boolean true on success, casts an ImportException and returns false on failure.
    */
   function element() {
      
      Import::$resourcetype = 'application/elements';
      
      $args = func_get_args();
      
      return Import::loadResource( $args );
      
   }
   
   /**
    * Relocates to a spesific URL location, supporting params
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   function relocate() {
      
      ob_end_clean();
      $args = func_get_args();
      $location = call_user_func_array( 'sprintf', $args );
      header( sprintf( 'location: %s', $location ) );
      
   }
   
   function printTimer( $event = 'log' ) {
      
      global $lastTimer;
      $timenow = microtime(true);
      $elapsed = $timenow - $lastTimer;
      $lastTimer = $timenow;
      
      echo @date('Y-m-d H:i:s  +').$elapsed."ms (".print_r( $event, true ).")\n";
      
   }
   
   function logTimer( $event = 'log' ) {
      
      global $lastTimer;
      $timenow = microtime(true);
      $elapsed = $timenow - $lastTimer;
      $lastTimer = $timenow;
      
      $file = fopen( '/tmp/bootstrap.timer.log', 'a' );
      fwrite( $file, @date('Y-m-d H:i:s '.$_SERVER['REMOTE_ADDR'].' +').$elapsed."ms (".print_r( $event, true ).")\n" );
      fclose( $file );
      
   }
   
   function resetTimer() {
      
      return (float) $GLOBALS['timer'] = microtime(true);
      
   }
   
   function getTimer() {
      
      return (float) $GLOBALS['timer'];
      
   }
   
?>
