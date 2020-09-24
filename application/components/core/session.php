<?PHP
   
   class Session {
      
      static $key;
      
      static function Initialize( $sessionName = null ) {
         
         // do we have an override session id?
         if( isset( $_REQUEST['sessionid'] ) ) {
            
            // allow overriding the sessionid
            session_id( $_REQUEST['sessionid'] );
            
         }
         
         // add support for no session handling
         if( !isset( $_REQUEST['nosession'] ) ) {
            
         	// strip any old-style cookies
         	# unset( $_COOKIE['PHPSESSID'] );
         	# setcookie( 'PHPSESSID', "", time() - 3600, '/' );
            
            // name and start our session
         	session_name( 'SESSIONID' );
         	session_start();
            
         }
         
      	// store our session_id
      	Session::id( session_id() );
      	
      	// set the scriptkey	for security rehashes
      	$scriptkey = Settings::get( 'site', 'scriptkey' );
         
         // make sure our session is valud, if not, kill it.
      	if( Session::get( 'scriptkey' ) != $scriptkey ) {
      		
      	   // re-initialize the session-store
      	   $_SESSION['sessionstore'] = array();
      	   
      	   // set the script-key so future loads don't reinit
      		Session::set( 'scriptkey', $scriptkey );
      		
      	}
         
      }
      
      static function delete( $key ) {

         if( isset( $_SESSION['sessionstore'][$key] ) ) {
            unset( $_SESSION['sessionstore']["$key"] );
            return true;
         } else {
            return false;
         }
         
      }
      
      static function get( $key, $default = null ) {
         
         return isset( $_SESSION['sessionstore'][$key] ) ? $_SESSION['sessionstore'][$key] : $default;
         
      }
      
      static function set( $key, $value ) {
         
         return $_SESSION['sessionstore'][$key] = $value;
         
      }
      
      static function fetch( $key, $values ) {
         
         if( !is_array( $values ) ) return array();
         if( !is_array( $_SESSION['sessionstore'][$key] ) ) return array();
         return array_intersect( $_SESSION['sessionstore'][$key], $values );
         
      }
      
      static function merge( $key, $values ) {
         
         if( !is_array( $values ) ) return true;
         
         if( is_array( $_SESSION['sessionstore'][$key] ) ) {
            $_SESSION['sessionstore'][$key] = util::mergeArrays( $_SESSION['sessionstore'][$key], $values );
         } else {
            $_SESSION['sessionstore'][$key] = $values;
         }
         
      }
      
      static function pipe( $key, $value = null, $default = false, $keep = false ) {
         
         if( isset( $value ) ) {
            
            return $_SESSION['sessionpipe'][$key] = $value;
            
         } else {
            
            if( isset( $_SESSION['sessionpipe'][$key] ) ) {
               
               $value = $_SESSION['sessionpipe'][$key];
               if( !$keep ) unset( $_SESSION['sessionpipe'][$key] );
               return $value;
               
            } else {
               
               return $default;
               
            }
            
         }
         
      }
      
      static function id( $sessionkey = null ) {
         
         if( isset( $sessionkey ) ) {
            
            return Session::$key = $sessionkey;
            
         } else {
            
            return Session::$key;
            
         }
         
      }
      
   }
   
?>