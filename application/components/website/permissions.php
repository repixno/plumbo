<?php
   
   define( 'PERMISSION_DENIED', 0x00 );
   define( 'PERMISSION_OWNER',  0x01 );
   define( 'PERMISSION_SHARED', 0x02 );
   define( 'PERMISSION_PUBLIC', 0x03 );

   import( 'session.memcache' );

   class PermissionManager {
      
      static $privateInstance = false;
      static $sessionInstance = false;
      
      private $acl = array();
      private $key = '';
      private $userid = 0;
      private $identifier = '';
      private $sessionkey = '';
      private $tokens = array();
      
      static function current() {
         
         if( Login::isLoggedIn() ) {
            
            if( !PermissionManager::$privateInstance instanceof PermissionManager ) {
               
               PermissionManager::$privateInstance = new PermissionManager( Login::userid() );
               
            }
            
            return PermissionManager::$privateInstance;
            
         } else {
            
            if( !PermissionManager::$sessionInstance instanceof PermissionManager ) {
               
               PermissionManager::$sessionInstance = new PermissionManager( Session::id() );
               
            }
            
            return PermissionManager::$sessionInstance;
            
         }
         
      }
      
      public function __construct( $identifier ) {
         
         $this->userid = is_integer( $identifier ) ? (int) $identifier : 0;
         $this->identifier = $identifier;
         $this->key = sprintf( 'PermissionCache[%s]', $identifier );
         $this->tokenkey = sprintf( 'PermissionTokens[%s]', Session::id() );
         $this->sessionkey = sprintf( 'PermissionCache[%s]', Session::id() );
         $this->reloadCache();
         
      }
      
      public function reloadCache() {
         
         $permissionlist = MemCacheSessionConnection::current()->read( $this->key );
         
         if( is_array( $permissionlist ) ) {
            
            $this->acl = $permissionlist;
            
         } else {
            
            // if we're logged in...
            if( $this->userid > 0 ) {
               
               // attempt to transfer permissions from the SessionID-based PermissionCache
               $permissionlist = MemCacheSessionConnection::current()->read( $this->sessionkey );
               if( is_array( $permissionlist ) ) {
                  
                  // if it worked, set it, then...
                  $this->acl = $permissionlist;
                  
                  // store it back as our new PermissionCache for this userid
                  $this->saveACL();
                  
               } else {
                  
                  // rebuild the cache from scratch
                  # $this->rebuildCache();
                  
               }
               
            } else {
               
               // rebuild the cache from scratch
               # $this->rebuildCache();
               
            }
            
         }
         
         $tokenlist = MemCacheSessionConnection::current()->read( $this->tokenkey );
         if( is_array( $tokenlist ) ) {
            $this->tokens = $tokenlist;
         }
         
      }
      
      public function rebuildCache( $class = null, $objectid = null ) {
         
         $aclchanged = false;
         $result = false;
         
         if( $class != 'album' ) {
            
            import( 'website.uploadedimagesarray' );
            foreach( UploadedImagesArray::get() as $imageid ) {
               $this->acl['image'][$imageid] = PERMISSION_OWNER;
               $aclchanged = true;
            }
            
            if( $objectid ) {
            
               if( isset( $this->acl['image'][$objectid] ) ) {
                  
                  $result = true;
                  
               } else {
               
                  $aid = (int) DB::query( 'SELECT aid FROM bildeinfo WHERE bid = ?', $objectid )->fetchSingle();
                  if( $aid ) {
                     $result = $this->rebuildCache( 'album', $aid );
                  }
                  else if($aid == null){
                     #temporary bugfix 
                     $owner_uid = (int) DB::query( 'SELECT owner_uid FROM bildeinfo WHERE bid = ?', $objectid )->fetchSingle();
                     if($this->userid == $owner_uid){
                        $this->acl['image'][$objectid] = PERMISSION_OWNER;
                     }
                  }
                  
               }
               
            }
            
         } else {
            
            if( $this->userid > 0 ) {
               
               $aid = DB::query( 'SELECT aid FROM bildealbum WHERE uid = ? AND aid = ?', $this->userid, $objectid )->fetchSingle();
               if( $aid > 0 ) {
                  
                  $this->acl['album'][$objectid] = PERMISSION_OWNER;
                  $aclchanged = true;
                  
               } else {
                  
                  $aid = DB::query( 'SELECT aid FROM tilgangtilalbum_dedikert WHERE uid = ? AND aid = ?', $this->userid, $objectid )->fetchSingle();
                  if( $aid > 0 ) {
                     $this->acl['album'][$objectid] = PERMISSION_SHARED;
                     $aclchanged = true;
                  }
                  
               }
               
            }
            
            if( $this->acl['album'][$objectid] > PERMISSION_DENIED ) {
               
               $res = DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ?', $objectid );
               while( list( $imageid ) = $res->fetchRow() ) {
                  $this->acl['image'][$imageid] = $this->acl['album'][$objectid];
                  $aclchanged = true;
               }
               
            }
            
            $result = true;
            
         }
         
         #MemCacheSessionConnection::current()->write( $this->key, $this->acl, 86400 );
         if( $aclchanged ) {
            
            register_shutdown_function( 'permissions_flush' );
            
         }
         
         return $result;
         
      }
      
      public function clearCache( $class = null ) {
         
         if( isset( $class ) ) {
            
            $this->acl[strtolower( $class )] = array();
            
         } else {
            
            $this->acl = array();
            
         }
         
         register_shutdown_function( 'permissions_flush' );
         
      }
      
      public function saveACL() {
         
         MemCacheSessionConnection::current()->write( $this->key, $this->acl, 86400 );
         
      }
      
      public function grantAccessTo( $objectid, $class, $permissiontype = PERMISSION_SHARED ) {
         
         if( is_array( $objectid ) ) {
            
            foreach( $objectid as $subobjectid ) {
               
               $this->acl[$class][$subobjectid] = $permissiontype;
               
               /*
               // EF 2.x compat-layer
               if( $class == 'image' ) {
                  $_SESSION['client_info']['auth_bid'][$subobjectid] = true;
               }
               */
               
            }
            
         } else {
            
            $this->acl[$class][$objectid] = $permissiontype;
            
            /*
            // EF 2.x compat-layer
            if( $class == 'image' ) {
               $_SESSION['client_info']['auth_bid'][$objectid] = true;
            }
            */
            
         }
         
         #MemCacheSessionConnection::current()->write( $this->key, $this->acl, 86400 );
         register_shutdown_function( 'permissions_flush' );
         
      }
      
      public function hasPublicAccessToAlbum( $albumid ) {
         
         if( DB::query( "SELECT aid FROM public_album WHERE aid = ?", $albumid )->count() > 0 ) {
            
            $this->grantAccessTo( $albumid, 'album', PERMISSION_PUBLIC );
            
            $images = array();
            $res = DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL', $albumid );
            while( list( $objectid ) = $res->fetchRow() ) {
               $images[] = $objectid;
            }
            
            $this->grantAccessTo( $images, 'image', PERMISSION_PUBLIC );
            
            return true;
            
         } else {
            
            return false;
            
         }

      }
      
      public function hasGroupAccessToAlbum( $albumid ) {
         
         if( DB::query( "SELECT a.aid 
                           FROM grupp_tilgangtilalbum_dedikert a
                      LEFT JOIN grouplist gl 
                             ON gl.groupid = a.gruppid
                          WHERE gl.friend = ? AND a.aid = ?", Login::userid(), $albumid )->count() > 0 ) {
            
            $this->grantAccessTo( $albumid, 'album', PERMISSION_PUBLIC );
            
            $images = array();
            $res = DB::query( 'SELECT bid FROM bildeinfo WHERE aid = ? AND deleted_at IS NULL', $albumid );
            while( list( $objectid ) = $res->fetchRow() ) {
               $images[] = $objectid;
            }
            
            $this->grantAccessTo( $images, 'image', PERMISSION_PUBLIC );
            
            return true;
            
         } else {
            
            return false;
            
         }

      }
      
      public function hasPublicAccessToImage( $imageid ) {
         
         if( DB::query( "SELECT bid FROM bildeinfo WHERE bid = ? AND aid IN ( SELECT aid FROM public_album )", $imageid )->count() > 0 ) {
            
            $this->grantAccessTo( $imageid, 'image', PERMISSION_PUBLIC );
            
            return true;
            
         } else {
            
            return false;
            
         }
         
      }
      
      public function hasGroupAccessToImage( $imageid ) {
         
         if( DB::query( "SELECT bid 
                           FROM bildeinfo 
                          WHERE bid = ? 
                            AND aid IN ( 
                            
                              SELECT aid 
                                FROM grupp_tilgangtilalbum_dedikert a 
                           LEFT JOIN grouplist gl 
                                  ON gl.groupid = a.gruppid 
                               WHERE gl.friend = ? 
                            
                            )", $imageid,
                                Login::userid()
         )->count() > 0 ) {
            
            $this->grantAccessTo( $imageid, 'image', PERMISSION_PUBLIC );
            
            return true;
            
         } else {
            
            return false;
            
         }
         
      }
      
      public function permissionType( $objectid, $class ) {
         
         if( !$objectid ) return PERMISSION_OWNER;
         
         $class = strtolower( $class );
         
         if( isset( $this->acl[$class][$objectid] ) ) {
            
            return $this->acl[$class][$objectid];
            
         } else {
            
            return PERMISSION_DENIED;
            
         }
         
      }
      
      public function hasAccessTo( $objectid, $class ) {
         
         $class = strtolower( $class );
         
         if( !isset( $this->acl[$class][$objectid] ) ) {
            
            $this->rebuildCache( $class, $objectid );
            
            if( !isset( $this->acl[$class][$objectid] ) ) {
               
               switch( $class ) {
                  
                  case 'image':
                     if( !$this->hasPublicAccessToImage( $objectid ) ) {
                        return $this->hasGroupAccessToImage( $objectid );
                     } else {
                        return true;
                     }
                     break;
                     
                  case 'album':
                     if( !$this->hasPublicAccessToAlbum( $objectid ) ) {
                        return $this->hasGroupAccessToAlbum( $objectid );
                     } else {
                        return true;
                     }
                     break;
                  
               }
               
               return false;
            
            }
            
         }
         
         return true;
         
      }
      
      public function compareTokenFor( $objectid, $class, $token ) {
         
         $class = strtolower( $class );
         if( !isset( $this->tokens[$class][$objectid] ) ) return false;
         return $this->tokens[$class][$objectid] == md5( $token );
         
      }
      
      public function saveTokens() {
         
         MemCacheSessionConnection::current()->write( $this->tokenkey, $this->tokens );
         
      }
      
      public function addTokenFor( $objectid, $class, $token ) {
         
         $class = strtolower( $class );
         $this->tokens[$class][$objectid] = md5( $token );
         $this->saveTokens();
         return true;
         
      }
      
   }
   
   function permissions_flush() {
      
      PermissionManager::current()->saveACL();
      exit;
      
   }
   
?>
