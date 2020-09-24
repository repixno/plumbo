<?PHP
   
   import( 'core.settings' );
   
   class WebsiteHelper {
      
      static function rootBaseUrl( $current = true ) {
         
         // define email settings
         $domain = Settings::Get( 'default', 'hostname', 'eurofoto.no' );
         $protocol = Settings::Get( 'default', 'protocol', 'http' );
         
         // do we have a domain defined now?
         if( $current && isset( $_SERVER['HTTP_HOST'] ) || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
            
            $domain = $_SERVER['HTTP_HOST'];
            
            // do we have a protocol
            if( WebsiteHelper::isSecure() ) {
               $protocol = 'https';
            }
            
         }
         
         // return it formatted
         return sprintf( '%s://%s', $protocol, $domain );
         
      }
      
      static function isSecure() {
         
         $host = explode( '.', $_SERVER['HTTP_HOST'] );
         return  ( isset( $_SERVER['HTTPS'] ) && end( $host ) != 'mac' ) || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'  ? true : false;
         
      }
      
      static function staticBaseUrl() {
         
         // return it formatted
         return Settings::Get( 'default', 'staticbaseurl', 'http://static.eurofoto.no' );
         
      }

      static function staticRotatedUrl() {
         
         config('website.static');
         $hosts = Settings::Get( 'static', 'hosts', array() );
         
         if( count( $hosts ) > 0 ) {
            $host = $hosts[array_rand($hosts)];
         } else {
            $host = WebsiteHelper::staticBaseUrl();
         }
         
         return $host;
         
      }

      static function streamsRotatedUrl() {
         
         config('website.streams');
         $hosts = Settings::Get( 'streams', 'hosts', array() );
         
         if( count( $hosts ) > 0 ) {
            $host = $hosts[array_rand($hosts)];
         } else {
            $host = WebsiteHelper::staticBaseUrl();
         }
         
         return $host;
         
      }

      static function adminBasePath() {
         
         // return it formatted
         return Settings::Get( 'default', 'adminbasepath', 'http://admin.eurofoto.no/' );
         
      }
      
      static function loginBasePath() {
         
         // return it formatted
         return Settings::Get( 'default', 'loginbasepath', '/login/' );
         
      }
      
      
      static function region() {
         
         $user = new User( Login::userid() );
         
         $local_regionid= array();
      	$local_regionid[160][0] = 1;
      	$local_regionid[160][1] = 3;

      	if( $user->isLoaded() && $user instanceof User ){

      	   if( !$user->country ){
      	      $user->country = Dispatcher::getCustomAttr( 'countryid' );
      		}
      		
      		$portalkode = Dispatcher::getPortal();
      		if( !$portalkode ) {
      			$portalkode = "EF-997";
      		}

      		
      		// Need to move this to dispather domainmap
      		// Faster than putting it in db
      		$portalid = Dispatcher::getCustomAttr( 'portalid' );
      		if(!is_numeric($portalid)){
      			
      		   $portalid = 0;
      		   
      		}
      		
      		if ( $regionid = $local_regionid[$user->country][$portalid] ) {	# Hardcoded most requested query, ugly-bugly but db is strained
      			
      		   
      		} else {

      		   $data = DB::query( "
      		      SELECT region.regionid 
      		      FROM region_nations, region 
      		      WHERE region_nations.regionid = region.regionid AND 
      		      nationid=? AND 
      		      portalid=?
      		   ", $user->country, $portalid );
      		   
      		   list( $regionid ) = $data->fetchRow();
      		   
      		   //$data = sql_allExec("egionid=region.regionid and nationid=".$kundedata["country"]." and portalid=$portalid;");
      			//$regionid = $data[0]['regionid'];
      			
      		}
      		
      	} else {
      	   
      	  $countryid = Dispatcher::getCustomAttr( 'countryid' );
      	  $portalcode = Dispatcher::getPortal();
      	  
      	  if( empty( $portalcode ) ) {
      			$portalcode = "EF-997";
      	  }
      	  
      	  // Need to move this to dispather domainmap
      	  // Faster than putting it in db
      	  $portalid = Dispatcher::getCustomAttr( 'portalid' );
      	  if(!is_numeric($portalid)){
      	     $portalid = 0;
      	  }
      	  
      	  if ( $regionid = $local_regionid[$countryid][$portalid] ) {	# Hardcoded most requested query, ugly-bugly but db is strained
      			
      		   
      	  } else {

      	     $data = DB::query( "
      		      SELECT region.regionid 
      		      FROM region_nations, region 
      		      WHERE region_nations.regionid = region.regionid AND 
      		      nationid=? AND 
      		      portalid=?
      		  ", $countryid, $portalid );
      		   
      		  list( $regionid ) = $data->fetchRow();
      		   
      	  }
      	   
      	}
      	
      	
      	// Do not remove this
      	// It will probably be needed for purchases
      	// when not logged in
      	// Andreas 2009-06-25
      	
      	/*else if($client_info["regionid"]){
      		$regionid = $client_info["regionid"];
      	}*/
      	/*else{ // Not logged in?!?
      		$nationid = run_skin_func("default_nationid");
      		$portalkode = run_skin_func("get_new_campain_code");
      		if(!$portalkode){
      			$portalkode = "EF-997";
      		}
      		$portalid = get_choices_portal_default_campaign_code($portalkode);
      		if(!is_numeric($portalid)){
      			$portalid = 0;
      		}
      		if ( $regionid = $local_regionid[$nationid][$portalid] ) {	# Hardcoded most requested query, ugly-bugly but db is strained
      			;
      		} else {
      			$data = sql_allExec("select region.regionid from region_nations,region where region_nations.regionid=region.regionid and nationid='$nationid' and portalid='$portalid';");
      			$regionid = $data[0]['regionid'];                                                                                 
      		}
      	}*/
      	
      	$cached_regionid = $regionid;
      	
      	return $regionid;
         
      }
      
      static function getLastGalleryImage() {
         
         import( 'website.galleryalbum' );
         config( 'website.gallery' );
         
         $albums = new GalleryAlbum();
         $albumlist = array();

         $portal = Dispatcher::getPortal();
         $portalkeymap = Settings::get( 'gallery', 'portalkeymap', array() );
         $portalkey = isset( $portalkeymap[$portal] ) ? $portalkeymap[$portal] : $portalkeymap['default'];
         
         $albumid = $albums->collection( array( 'aid' ), array( 'uid' => array( '!=', 0 ), 'deleted_at' => null, 'key' => $portalkey ), 'publicshare_time DESC', 1 )->fetchSingle();
         if( $albumid > 0 ) {
            
            try {
               
               $album = new GalleryAlbum( $albumid );
               return @end( $album->listImageIDs() );
               
            } catch ( Exception $e ) {
               
               return 0;
               
            }
            
         } else {
            
            return 0;
            
         }
         
      }
      
      static function getTodaysImage( $step = 0 ) {
         
         config( 'website.todaysphoto' );
         
         $mapping = Settings::Get( 'todaysphoto', 'albums', array() );
         
         $albumid = (int) $mapping[Dispatcher::getPortal()];
         
         if( $albumid > 0 ) {
            
            $step = (int) $step;
            
            $imageid = (int) DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ? ORDER BY sorting LIMIT 1 OFFSET ?', $albumid, $step )->fetchSingle();
            
            if( $imageid > 0 ) {
               
               // because JSR/Ragnar/whomever is a fucking idiot, these public images/albums ARE NOT PUBLIC!
               PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_PUBLIC );
               PermissionManager::current()->grantAccessTo( $albumid, 'album', PERMISSION_PUBLIC );
               
               return $imageid;
               
            }
            
         }
         
         return false;
         
      }
      
   }
   
?>
