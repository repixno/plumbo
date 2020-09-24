<?php
   
   import( 'cache.memcache' );

   // if you have session.gc_maxlifetime configured to something, 
   // you should use that instead of the hardcoded number.
	$GLOBALS['sessionlifetime'] = 14400; // get_cfg_var("session.gc_maxlifetime");
	$GLOBALS['sessionreadsuccess'] = false;
	
	// make sure we always complete our writes
	ignore_user_abort( true );
	
	class MemCacheSessionConnection {
	   
	   static $connection = false;
	   
	   static function current() {
	      
	      if( !MemCacheSessionConnection::$connection instanceof MemCacheEngine ) {
            MemCacheSessionConnection::$connection = new MemCacheEngine( 'session' );
         }
         
         return MemCacheSessionConnection::$connection;
	      
	   }
	   
	}
	
	/**
	 * Opens up a session for reading. Not used.
	 *
	 * @param string $save_path
	 * @param string $session_name
	 * @return boolean Always returns true
	 */
	function sess_open( $save_path, $session_name ) {
		
		// return success
		return true;
		
	}
	
	/**
	 * Closes a session after reading/writing. Not used.
	 *
	 * @return boolean Always returns true
	 */
	function sess_close() {
		
	   // return success
		return true;
		
	}

	/**
	 * Logs a session event to the memcache session log
	 *
	 * @param string $event The event to log
	 */
	function session_log( $event ) {
   
      $f = fopen( '/tmp/memcachesessions.log', 'a' );
      fwrite( $f, sprintf( "%s - %s - %s - %s\n", date( 'Y-m-d H:i:s' ), $event, $_SERVER['REMOTE_ADDR'], $_SERVER['PHP_SELF'] ) );
      fclose( $f );
      
   }
   
	/**
	 * Reads a session from memory
	 *
	 * @param string $key The session key to look up
	 * @return string The serialized session data, or a blank string on failure
	 */
	function sess_read( $key ) {
		
	   $expiryTime = 3;
	   $waitUtime = 101;
	   $maxTries = 100000;
	   $lock = 'lock:'.$key;
      
	   $memcache = MemCacheSessionConnection::current();
	   
      // get the lock {{{
      if( $maxTries > 0 ) {
         for( $tries = 0; $tries < $maxTries; ++$tries ) {
            if( $memcache->add( $lock, 1, $expiryTime ) ) { break; }
            // session_log( 'LOCK LOOP: '.$key );
            usleep( $waitUtime );
         }
         if ($tries == $maxTries) {
            return false;
         }
      } else {
         while (!$memcache->add( $lock, 1, $expiryTime ) ) {
            // session_log( 'LOCK LOOP: '.$key );
            usleep( $waitUtime );
         }
      }
	   
	   // import global
	   global $sessionreadsuccess;
	  
      // session_log( 'BEGIN READ: '.$key );
      
      // attempt to read back the session data
		$ret = $memcache->read( 'session['.$key.']' );
		
		if( !$ret ) {
		   
         // attempt to re-read if unable to read
         $ret = $memcache->read( 'session['.$key.']' );
		   
		}
		
		// if we got a response return it, else ""
      // session_log( 'END READ: '.$key );
      try {
		   
         // attempt to decompress the data
         $ret = (string) @gzuncompress( $ret );
		   
		   // we succeeded at reading
		   if( $ret ) $sessionreadsuccess = true;
         
      } catch( Exception $e ) {
         
         // failing that, return empty session
         $ret = (string) "";
         
      }
      
      // ok, attempt to return the session data
		return $ret ?  $ret : (string) "";
		
	}
	
	/**
	 * Writes a session to memory, making sure its also present in the session index table.
	 *
	 * @param string $key The session key to store it under
	 * @param string $val Serialized session to be placed in memory
	 * @return boolean Always returns true
	 */
	function sess_write( $key, $val ) {
		
      $lock = 'lock:'.$key;
      
      if( !$val ) return true;
   		global $sessionreadsuccess, $sessionlifetime;
         // session_log( 'BEGIN WRITE: '.$key );
   
         // check if the session already exists, if not...
         if( !$sessionreadsuccess ) {
   	

			// added after memcached turn off
                         $botfound = false;
		
            // figure out more data about the user
            $ip = addslashes( $_SERVER['REMOTE_ADDR'] );
   			$agent = addslashes( $_SERVER['HTTP_USER_AGENT'] );
   			$bots = array('baidu', 'spider', 'crawler', 'bot', 'slurp', 'touch' );
   			while((list(, $bot) = each($bots)) && !$botfound) if(stristr($agent, $bot)) $botfound = 'bot';
   			$isbot = $botfound ? 1 : 0;
   			
   			try {
   			   
      			// ...and store it in a session table
      			if( !DB::query( 'SELECT count( sessionid ) FROM sessions WHERE sessionid=?', $key )->fetchSingle() ) {
      			   DB::query( 'INSERT INTO sessions (sessionid, ipaddress, agent, isbot, created) VALUES (?, ?, ?, ?, now())', $key, $ip, $agent, $isbot );
      			} else {
      			   DB::query( 'UPDATE sessions SET ipaddress=?, agent=?, isbot=?, created=now() WHERE sessionid = ?', $ip, $agent, $isbot, $key );
      			}
		         
            } catch( Exception $e ) {
               
            }
   			
   		}
   		
   		$memcache = MemCacheSessionConnection::current();
   		
   		// finally, write the session data to the memory cache
   		$memcache->replaceOrAdd( 'session['.$key.']', @gzcompress( $val ), 14400 );
         
         // session_log( 'END WRITE: '.$key );
         
         // clear the lock
         $memcache->erase( $lock );
      
      // return success
		return true;
		
	}
	
	/**
	 * Destroys a given session key
	 *
	 * @param string $key The key to destroy
	 * @return boolean Always returns true
	 */
	function sess_destroy( $key ) {
		
	   $memcache = MemCacheSessionConnection::current();
	   
	   // first, erase the session
		$memcache->erase( 'session['.$key.']' );
		
		// then, delete from the session table
		DB::query( 'DELETE FROM sessions WHERE sessionid = ?', $key );
		
		// return success
		return true;
		
	}
	
	/**
	 * Garbage collector. Ran by PHP every n requests.
	 * You can configure the n in your PHP.INI. 
	 * The default is about every 100 requests.
	 *
	 * @param integer $maxlifetime The maximum lifetime of a session. Not used.
	 * @return boolean Always returns true
	 */
	function sess_gc( $maxlifetime ) {
		
	   // run the internal cleanup routine
	   // return sess_gccheck();
		return true;
		
	}
   
	/**
	 * Internal function to handle Garbage Collection
	 *
	 * @return boolean True when cleanup is complete
	 */
	function sess_gccheck() {
		
	   $memcache = MemCacheSessionConnection::current();
	   
	   // query the session table
		//$res = query( "SELECT sessionid FROM sessions" );
      foreach( DB::query( 'SELECT sessionid FROM sessions' )->fetchAll( DB::FETCH_ASSOC ) as $row ) {
         
         $key = $row['sessionid'];
         
         // check if the session exists
         if( !$memcache->read( 'session['.$key.']', false ) ) {
			  
			  // drop from the session table
			  DB::query( 'DELETE FROM sessions WHERE sessionid = ?', $key );
			  
		   }
		   
      }
		
		// return success
		return true;
		
	}
	
	function sess_updatestats() {
	   
	   $stats = memCacheStats();
	   
	   $uptime       = (int) $stats['uptime'];
	   $items        = (int) $stats['curr_items'];
	   $bytes        = (int) $stats['bytes'];
	   $usage_user   = (float) $stats['rusage_user'];
	   $usage_system = (float) $stats['rusage_system'];
	   $cmd_get      = (int) $stats['cmd_get'];
	   $cmd_set      = (int) $stats['cmd_set'];
	   $get_hits     = (int) $stats['get_hits'];
	   $get_misses   = (int) $stats['get_misses'];
	   $evictions    = (int) $stats['evictions'];
	   
	   DB::query( "INSERT INTO memcache_stats (uptime,items,bytes,usage_user,usage_system,cmd_get,cmd_set,get_hits,get_misses,evictions,logged) VALUES ('$uptime','$items','$bytes','$usage_user','$usage_system','$cmd_get','$cmd_set','$get_hits','$get_misses','$evictions',NOW())" );
	   
	}
	
	session_set_save_handler (
		"sess_open",
		"sess_close",
		"sess_read",
		"sess_write",
		"sess_destroy",
		"sess_gc"
	);
	
?>
