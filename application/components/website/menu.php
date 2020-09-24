<?PHP

   model( 'site.menu' );

   import( 'core.dispatcher' );
   import( 'cache.engine' );
   import( 'website.article' );
   import( 'website.product' );

   class MenuItem extends DBMenu implements ModelCaching {

      public $partialMatch = false;

      public function save() {

         // for now, just force an update to the URL
         if( !$this->urlname ) $this->urlname = util::urlize( $this->title );

         // Generate url.
         $this->url = $this->generateUrl( $this->urlname );

         // update translations
         $this->updateTranslation();

         // call the save routine
         $result = parent::save();

         // clear the list caches
         Menu::clearItemsCache();

         // return back
         return $result;

      }

      public function getTitle( $translated = true ) {

         return $translated ? $this->getTranslatedTitle( i18n::$language ) : $this->fieldGet( 'title' );

      }

      public function getTranslation() {

         $translation = @unserialize( $this->fieldGet( 'translation' ) );
         return is_array( $translation ) ? $translation : array();

      }

      public function setTranslation( Array $translation ) {

         $this->fieldSet( 'translation', serialize( $translation ) );

      }

      public function updateTranslation() {

         $translation = $this->translation;

         foreach( $translation as $langcode => $langdata ) {

            $urls = array( util::urlize( $this->getTranslatedURLName( $langcode ) ) );
            $parentid = $this->parentid;
            while( $parentid > 0 ) {
               $object = new MenuItem( $parentid );
               $urlname = $object->getTranslatedURLName( $langcode );
               $parentid = $object->parentid;
               array_unshift( $urls, util::urlize( $urlname ) );
            }

            $translation[$langcode]['url'] = sprintf( '/%s/', implode( '/', $urls ) );

         }

         $this->translation = $translation;

      }

      public function getTranslatedUrl( $langcode ) {

         $translation = $this->translation;
         return $translation[$langcode]['url'] ? $translation[$langcode]['url'] : $this->fieldget( 'url' );

      }

      public function setTranslatedUrl( $langcode, $url ) {

         $translation = $this->translation;
         $translation[$langcode]['url'] = $url;
         $this->translation = $translation;

      }

      public function getTranslatedTitles() {

         $results = array();
         $translation = $this->translation;
         foreach( $translation as $langcode => $langdata ) {
            $results[] = array(
               'code' => $langcode,
               'title' => $langdata['title'],
            );
         }

         return $results;

      }

      public function setTranslatedTitles( $titles ) {

         $translation = $this->translation;
         foreach( $titles as $langcode => $title ) {
            $translation[$langcode]['title'] = $title;
         }
         $this->translation = $translation;

      }

      public function getTranslatedURLNames() {

         $results = array();
         $translation = $this->translation;
         foreach( $translation as $langcode => $langdata ) {
            $results[] = array(
               'code' => $langcode,
               'urlname' => $langdata['urlname'],
            );
         }

         return $results;

      }

      public function setTranslatedURLNames( $urlnames ) {

         $translation = $this->translation;
         foreach( $urlnames as $langcode => $urlname ) {
            $translation[$langcode]['urlname'] = $urlname;
         }
         $this->translation = $translation;

      }

      public function getTranslatedURLName( $langcode ) {

         $translation = $this->translation;
         return $translation[$langcode]['urlname'] ? $translation[$langcode]['urlname'] : $this->fieldget( 'urlname' );

      }

      public function setTranslatedURLName( $langcode, $urlname ) {

         $translation = $this->translation;
         $translation[$langcode]['urlname'] = $urlname;
         $this->translation = $translation;

      }

      public function getTranslatedTitle( $langcode ) {

         $translation = $this->translation;
         return $translation[$langcode]['title'] ? $translation[$langcode]['title'] : $this->fieldget( 'title' );

      }

      public function setTranslatedTitle( $langcode, $title ) {

         $translation = $this->translation;
         $translation[$langcode]['title'] = $title;
         $this->translation = $translation;

      }

      public function generateUrl( $urlname ) {

         $urls = array( util::urlize( $urlname ) );
         $parentid = $this->parentid;
         while( $parentid > 0 ) {
            $object = new MenuItem( $parentid );
            $parentid = $object->parentid;
            array_unshift( $urls, util::urlize( $object->urlname ) );
         }

         return sprintf( '/%s/', implode( '/', $urls ) );

      }

      public function urlExists( $urlname ) {

         $testUrl = $this->generateUrl( $urlname );
         $siteid = Session::get( 'adminsiteid', 0 );
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', 1 );
         }
         
         return count( DB::query( "SELECT id FROM site_menu WHERE id!=? AND url=? AND siteid = ? AND deleted IS NULL", $this->id, $testUrl, $siteid )->fetchAll() ) == 0 ? false : true;

      }

      public function getDescendants() {

         $menu = new Menu();
         return $menu->getDescendants( $this->id );

      }

      public function updateDescendantUrls() {

         foreach ( $this->getDescendants() as $id ) {

            $item = new MenuItem( $id );
            $item->save();

         }

      }

      public function asArray() {

         if( $articleid = $this->articleid ) {

            $article = new Article( $articleid );
            if( $article->isLoaded() ) {

               return array(
                  'title' => $this->title,
                  'image' => $this->image,
                  'score' => $this->score,
                  'url' => $this->getTranslatedUrl( i18n::$language ),
                  'identifier' => $this->identifier,
                  'article' => $article->asArray(),
               );

            }

         }

         return array(
            'title' => $this->title,
            'image' => $this->image,
            'score' => $this->score,
            'url' => $this->getTranslatedUrl( i18n::$language ),
            'identifier' => $this->identifier,
         );

      }

      /**
       * @todo Update Cache in array
       */
      public function addContent( $textentityid, $sortorder = 0, $section = '' ) {

         if( !$this->hasContent( $textentityid, $section ) ) {

            DB::query( "INSERT INTO site_menu_contents (menuid, textentityid, sortorder, section, created, createdby) VALUES (?, ?, ?, ?, NOW(), ?)", $this->id, $textentityid, $sortorder, $section, Login::userid() );

         }

      }

      /**
       * @todo Update Cache in array
       */
      public function removeContent( $textentityid, $section = '' ) {

         DB::query( "DELETE FROM site_menu_contents WHERE menuid = ? AND textentityid = ? AND section = ?", $this->id, $textentityid, $section );

      }

      /**
       * @todo Update Cache in array
       */
      public function setContentOrder( $textentityid, $sortorder = 0, $section = '', $oldsection = '' ) {

         if( !$this->hasContent( $textentityid, $section ) ) {

           DB::query( "UPDATE site_menu_contents SET sortorder=?, section=? WHERE menuid=? AND textentityid=? AND section=?", $sortorder, $section, $this->id, $textentityid, $oldsection );

         }

      }

      public function hasContent( $textentityid, $section = '' ) {

         if ( !isset( $section ) ) {

            $contents = $this->getContentIds();
            return in_array( $textentityid, $contents );

         } else {

            $contents = $this->getContentIds( true, true );
            return isset( $contents[ $section ] ) ? in_array( $textentityid, $contents[ $section ] ) : false;

         }            

      }

      public function getContentObjects( $sectioned = false ) {

         import( 'website.article' );
         ///import( 'website.product' );

         $objects = array();

         if( $sectioned ) {
            
            foreach( $this->getContentIds( true, true ) as $section => $items ) {
               
               foreach( $items as $id => $class ) {
   
                  switch( $class ) {
      
                     case 'article':
                     case 'product':
                        $object = new $class( $id );
                        if( $object->asArray() ){
                           $objects[$section][] = $object->asArray();
                        }
                        break;
                        
                  }
                  
               }
               
            }
            
         } else {
         
            foreach( $this->getContentIds( true ) as $id => $class ) {
   
               switch( $class ) {
   
                  case 'article':
                  case 'product':
                     $object = new $class( $id );
                     
                     if( $object->asArray() ){
                        $objects[] = $object->asArray();
                     }
                     
                     break;
   
               }
   
            }
            
         }
         
         return $objects;

      }

      /**
       * @todo Cache content in an array
       */
      public function getContentIds( $keyed = false, $sectioned = false ) {

         $contents = array();
         foreach( DB::query( "SELECT c.textentityid, t.type, c.section FROM site_menu_contents c LEFT JOIN site_textentity t ON t.id=c.textentityid WHERE c.menuid = ? ORDER BY c.sortorder ASC", $this->id )->fetchAll() as $row ) {
            list( $textentityid, $type, $section ) = $row;
            if( $keyed ) {
               if( $sectioned ) {
                  $contents[$section][$textentityid] = $type;
               } else {
                  $contents[$textentityid] = $type;
               }
            } else {
               $contents[] = $textentityid;
            }
         }

         return $contents;

      }

      public function getContentList() {

         $contentList = array();
         foreach( DB::query( "SELECT * FROM site_menu_contents AS a JOIN site_textentity AS b ON a.textentityid=b.id JOIN site_textentity_content AS c ON b.id=c.textentityid WHERE a.menuid = ? AND c.languageid = ? ORDER BY a.sortorder ASC", $this->id, i18n::languageid() )->fetchAll( DB::FETCH_ASSOC ) as $row ) {
            $contentList[] = $row;
         }

         return $contentList;

      }
      
      public function getContentListSection($section = 'standard') {

         $contentList = array();
         foreach( DB::query( "SELECT * FROM
                                 site_menu_contents AS a
                              JOIN site_textentity AS b
                                 ON a.textentityid=b.id
                              JOIN site_textentity_content AS c
                                 ON b.id=c.textentityid
                              WHERE
                                 a.menuid = ?
                              AND
                                 c.languageid = ?
                              AND
                                 a.section = ?
                              ORDER BY
                                 a.sortorder ASC", $this->id, i18n::languageid(), $section )->fetchAll( DB::FETCH_ASSOC ) as $row ) {
            $contentList[] = $row;
         }

         return $contentList;

      }

      /**
       * @todo Cache content in an array
       */
      public function getPortals() {

         $portals = array();
         foreach( DB::query( "SELECT portal FROM site_menu_portals WHERE menuid = ?", $this->id )->fetchAll() as $row ) {
            list( $portal ) = $row;
            $portals[] = $portal;
         }

         return $portals;

      }

      public function hasPortal( $portal ) {

         $portals = $this->getPortals();
         return in_array( $portal, $portals );

      }

      /**
       * @todo Update Cache in array
       */
      public function addPortal( $portal ) {

         if( !$this->hasPortal( $portal ) ) {

            DB::query( "INSERT INTO site_menu_portals (menuid, portal, created, createdby) VALUES (?, ?, NOW(), ?)", $this->id, $portal, Login::userid() );

         }

      }

      /**
       * @todo Update Cache in array
       */
      public function removePortal( $portal ) {

         if( $this->hasPortal( $portal ) ) {

            DB::query( "DELETE FROM site_menu_portals WHERE menuid=? AND portal=?", $this->id, $portal );

         }

      }

      public function updateSortOrder( $origPos, $newPos ) {

         $menu = new Menu();

         DB::begintransaction();

         $affecteditems = array( $newPos[ 'itemid' ] => true );

         foreach ( $menu->getDescendants( $newPos[ 'itemid' ] ) as $id ) {
            $affecteditems[$id] = true;
         }


         // Change order of new submenu.
         foreach( DB::query( "SELECT id FROM site_menu WHERE parentid=? AND sortorder>=?", $newPos[ 'parentid' ], $newPos[ 'position' ] )->fetchAll() as $row ) {
            $affecteditems[array_pop($row)] = true;
         }
         DB::query( "UPDATE site_menu SET sortorder=sortorder+1 WHERE parentid=? AND sortorder>=?", $newPos[ 'parentid' ], $newPos[ 'position' ] );

         // Move item.
         DB::query( "UPDATE site_menu SET parentid=?, sortorder=? WHERE id=?", $newPos[ 'parentid' ], $newPos[ 'position' ], $newPos[ 'itemid' ] );

         // Change order of old submenu.
         foreach( DB::query( "SELECT id FROM site_menu WHERE parentid=? AND sortorder>?", $origPos[ 'parentid' ], $origPos[ 'position' ] )->fetchAll() as $row ) {
            $affecteditems[array_pop($row)] = true;
         }
         DB::query( "UPDATE site_menu SET sortorder=sortorder-1 WHERE parentid=? AND sortorder>?", $origPos[ 'parentid' ], $origPos[ 'position' ] );

         DB::commit();

         // ensure a clean cache by rebuilding affected items
         foreach( $affecteditems as $menuid => $enabled ) {

            // first find and delete the object
            $menuitem = new MenuItem( $menuid );
            $menuitem->deleteFromObjectCache();

            // secondly, re-load from DB and save
            $menuitem = new MenuItem( $menuid );
            $menuitem->save();

         }

      }

   }

   class Menu {

      static $cache = false;
      static $index = false;

      static $itemscache = false;
      static $childcache = false;

      static function createMenuTree( $items, $children, $activeid, $parentid = 0, $level = 0, $includecontent = false, $includebodies = true ) {
         
         $results = array();
         $hasactivechild = false;

         if( is_array( $children[$parentid] ) )
         foreach( $children[$parentid] as $itemid ) {

            $hasactivesubchild = false;

            $array = $items[$itemid];
            $array['level'] = $level;
            $array['active'] = $itemid == $activeid ? true : false;
            $array['expanded'] = false;
            
            if( !$array['score'] ) $array['score'] = 1 - ( $level / 10 );

            if( $array['active'] ) $hasactivesubchild = true;

            if( $includecontent ) {
               $item = new MenuItem( $itemid );
               $content = $item->getContentObjects();
               if( count( $content ) > 0 ) {
                  foreach( $content as $contentitemdata ) {
                     if( $contentitemdata['urlname'] ) {
                        $contentitem = array(
                           'title' => $contentitemdata['title'],
                           'image' => '',
                           'score' => 1 - ( ( $level + 1 ) / 10 ),
                           'url' => sprintf( '%s/%s', rtrim( $array['url'], '/' ), $contentitemdata['urlname'] ),
                           'level' => $level + 1,
                           'active' => false,
                           'expanded' => false,
                        );
                        if( $includebodies ) {
                           $contentitem['body'] = $contentitemdata['body'];
                           $contentitem['ingress'] = $contentitemdata['ingress'];
                        }
                        $array['content'][] = $contentitem;
                     }
                  }
               }
            }
            
            list( $subchildren, $subhasactivechild ) = Menu::createMenuTree( $items, $children, $activeid, $itemid, $level + 1, $includecontent );

            if( $subhasactivechild ) $hasactivesubchild = true;

            if( $hasactivesubchild ) {
               $array['expanded'] = true;
               $hasactivechild = true;
            }
            if( count( $subchildren ) > 0 ) {
               $array['children'] = $subchildren;
            }
            
            $results[] = $array;

         }

         return $level == 0 ? $results : array( $results, $hasactivechild );

      }

      static function findItemFromURL( $url ) {

         // if we don't have a cache, create one.
         if( empty( Menu::$cache ) ) Menu::cacheMenuItems();
         
         // first off, split up and prepare the target for a closest possible match
         $target = preg_split( '/\//', $url, 0, PREG_SPLIT_NO_EMPTY );
         $target1 = '/'.implode( '/', $target ).'/'; array_pop( $target );
         $target2 = '/'.implode( '/', $target ).'/';
         $partialMatch = false;
         $menuid = 0;

         foreach( Menu::$cache as $match => $id ) {

            if( $target1 == $match ) {

               $menuid = $id;
               break;

            }

         }

         if( $menuid == 0 )
         foreach( Menu::$cache as $match => $id ) {

            if( $target2 == $match ) {

               $menuid = $id;
               $partialMatch = true;
               break;

            }

         }

         if( $menuid > 0 ) {

            $menuitem = new MenuItem( $menuid );
            $menuitem->partialMatch = $partialMatch;
            return $menuitem;

         } else {

            return false;

         }

      }

      static function findItemFromIdentifier( $identifier ) {

         // calculate the identifier
         $lookupkey = sprintf( 'MenuID-Lookup-%s', $identifier );

         // do we already know this identifier/menu in the cache?
         if( $menuid = CacheEngine::read( $lookupkey, false ) ) {

            // return the cached menu item
            return new MenuItem( $menuid );

         }

         // if we don't have a cache, create one.
         if( empty( Menu::$index ) ) Menu::cacheMenuItems();

         // check if our identifier is defined
         if( isset( Menu::$index[$identifier] ) ) {

            // find the menuid of this identifier
            $menuid = Menu::$index[$identifier];

            // write it to the cache engine for future use
            CacheEngine::write( $lookupkey, $menuid, 86400 );

            // return the cached menu item
            return new MenuItem( $menuid );

         } else {

            return false;

         }

      }

      static function enumItems() {

         // if we don't have a cache, create one.
         if( empty( Menu::$cache ) ) Menu::cacheMenuItems();

         // create a list of items
         $items = $distinct = array();
         foreach( Menu::$cache as $menuid ) {
            $distinct[$menuid] = true;
         }

         foreach( $distinct as $menuid => $dummy ) {
            $items[$menuid] = new MenuItem( $menuid );
         }

         // return the list
         return $items;

      }

      static function clearItemsCache() {

         model( 'site.portal' );
         
         // find the site-list
         $sites = Settings::Get( 'application', 'sites', array() );
         
         // rebuild the menu cache for all languages
         $collection = new DBPortal();
         foreach( $collection->collection( 'code' )->fetchAll() as $row ) {

            list( $portal ) = $row;
            
            foreach( i18n::getLanguages() as $language => $langname ) {
               
               if( is_array( $sites ) && count( $sites ) > 0 ) {
                  
                  foreach( $sites as $siteid => $site ) {
                     
                     $cachekey = sprintf( 'MenuCache-%s-%s-%d', $portal, $language, $siteid );
                     $indexkey = sprintf( 'MenuIndex-%s-%s-%d', $portal, $language, $siteid );
                     $itemskey = sprintf( 'MenuItems-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
                     $childkey = sprintf( 'MenuChild-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
                     
                     CacheEngine::erase( $cachekey );
                     CacheEngine::erase( $indexkey );
                     CacheEngine::erase( $itemskey );
                     CacheEngine::erase( $childkey );
                     
                  }
                  
               } else {
                  
                  $siteid = Session::get( 'siteid', 1 );
               
                  $cachekey = sprintf( 'MenuCache-%s-%s-%d', $portal, $language, $siteid );
                  $indexkey = sprintf( 'MenuIndex-%s-%s-%d', $portal, $language, $siteid );
                  $itemskey = sprintf( 'MenuItems-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
                  $childkey = sprintf( 'MenuChild-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
                  
                  CacheEngine::erase( $cachekey );
                  CacheEngine::erase( $indexkey );
                  CacheEngine::erase( $itemskey );
                  CacheEngine::erase( $childkey );
                  
               }

            }

         }

         // create a fresh cache
         Menu::cacheMenuItems( true );

      }

      static function cacheMenuItems( $force = false, $language = false ) {
         
         $siteid = Session::get( 'siteid', 1 );
         $language = $language ? $language : i18n::$language;
         $cachekey = sprintf( 'MenuCache-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
         $indexkey = sprintf( 'MenuIndex-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
         $itemskey = sprintf( 'MenuItems-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
         $childkey = sprintf( 'MenuChild-%s-%s-%d', Dispatcher::getPortal(), $language, $siteid );
         
         if( !$force ) $menucache = CacheEngine::read( $cachekey, null );
         if( !$force ) $menuindex = CacheEngine::read( $indexkey, null );
         if( !$force ) $menuitems = CacheEngine::read( $itemskey, null );
         if( !$force ) $menuchild = CacheEngine::read( $childkey, null );

         if( $force || empty( $menucache ) || empty( $menuindex ) || empty( $menuitems ) || empty( $menuchild ) ) {

            $menucache = array();
            $menuindex = array();
            $menuitems = array();
            $menuchild = array();

            //$collection = new MenuItem();
            //foreach( $collection->collection( array( 'id' ), array( 'deleted' => null ), 'sortorder' )->fetchAllAs('MenuItem') as $menuitem ) {
            foreach( DB::query( "SELECT t1.id FROM site_menu AS t1 LEFT JOIN site_menu_portals t2 ON t2.menuid=t1.id WHERE ( portal='all' OR portal=? ) AND t1.deleted IS NULL AND t1.siteid = ? ORDER BY t1.sortorder", Dispatcher::getPortal(), $siteid )->fetchAll() as $row ) {

               try {

                  $menuitem = new MenuItem( array_shift( $row ) );

                  // add the menu item to the lookup-cache
                  $url = '/'.implode( '/', preg_split( '/\//', $menuitem->url, 0, PREG_SPLIT_NO_EMPTY ) ).'/';
                  $menucache[$url] = $menuitem->id;

                  // add translated URL too, if it exists
                  $translated = $menuitem->translation;

                  if( is_array( $translated ) && isset( $translated[i18n::$language] ) && $translated[i18n::$language]['url'] ) {
                     $url = '/'.implode( '/', preg_split( '/\//', $translated[i18n::$language]['url'], 0, PREG_SPLIT_NO_EMPTY ) ).'/';
                     $menucache[$url] = $menuitem->id;
                  }

                  // add the identifier to the index
                  $menuindex[$menuitem->identifier] = $menuitem->id;

                  // prepare the remaining caches
                  $menuitems[$menuitem->id] = $menuitem->asArray();
                  $menuchild[$menuitem->parentid][] = $menuitem->id;

               } catch( Exception $e ) {}

            }

            #krsort( $menucache );

            CacheEngine::write( $cachekey, $menucache );
            CacheEngine::write( $indexkey, $menuindex );

         }

         Menu::$childcache = $menuchild;
         Menu::$itemscache = $menuitems;
         Menu::$cache = $menucache;
         Menu::$index = $menuindex;

      }

      static function getMenuTree( $activeid = 0, $parentid = 0, $includecontent = false, $includebodies = true ) {

         // if we don't have a cache, create one.
         if( empty( Menu::$itemscache ) || empty( Menu::$childcache ) ) Menu::cacheMenuItems();

         // loop through the menutree, creating a branched tree
         return Menu::createMenuTree( Menu::$itemscache, Menu::$childcache, $activeid, $parentid, 0, $includecontent, $includebodies );

      }

      static function getRootSize() {

         $siteid = Session::get( 'adminsiteid', 0 );
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', 1 );
         }

         return DB::query( "SELECT COUNT(*) FROM site_menu WHERE parentid = 0 AND siteid = ?", $siteid )->fetchSingle();

      }

      static function listMenuItems( $portal = null ) {

         Menu::clearCache();

         $portalJoin = '';
         if ( isset( $portal ) && !empty( $portal ) ) {

            $portalJoin = "
               JOIN site_menu_portals AS b
                  ON a.id=b.menuid AND portal='$portal'
               ";

         }
         
         $siteid = Session::get( 'adminsiteid', 0 );
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', 1 );
         }

         $queryString = "
            SELECT
               a.id, a.url
            FROM
               site_menu AS a
               $portalJoin
            WHERE
               a.deleted IS NULL
            AND
               a.siteid = ?
            ORDER BY
               a.parentid, a.sortorder
            ";

         return DB::query( $queryString, $siteid )->fetchAllAs( 'MenuItem' );

      }

      static function clearCache() {

         $cacheEngine = CacheEngineFactory::current();
         if ( !empty( Menu::$cache ) ) {

            foreach ( Menu::$cache as $match => $objectId ) {

               $objectKey = sprintf( 'object-cache-%s-%s', 'MenuItem', $objectId );
               $cacheEngine->erase( $objectKey );

            }

         }

      }

      public function itemPath( $startId ) {

         if ( !is_numeric( $startId ) ) return false;

         $curId = $startId;
         $pathExtd = array();
         $pathSmpl = array();
         $i = 0;
         do {

            $menuitems = new MenuItem( $curId );
            $parentId = $menuitems->parentid;
            $title = $menuitems->title;

            $newNode = array(
               'id' => $curId,
               'parentid' => $parentId,
               'title' => $title
               );
            array_unshift( $pathExtd, $newNode );
            array_unshift( $pathSmpl, $title );

            $curId = $parentId;

         } while ( $parentId != 0 );

         return array( $pathSmpl, $pathExtd );

      }

      public function deleteMenuItems( $items, $parent, $position ) {

         // Delete menu items (and sub items).
         foreach ( $items as $id ) {

            $tmpItem = new MenuItem( $id );
            $tmpItem->delete();

         }

         // Find items du change sortorder for.
         foreach ( DB::query( "SELECT id FROM site_menu WHERE parentid=? AND sortorder>?", $parent, $position )->fetchAll() as $id ) {

            $tmpItem = new MenuItem( $id );
            $tmpItem->sortorder--;
            $tmpItem->save();

         }

      }

      public function getChildrenRecursive( $id ) {

         $ret = array();
         $children = DB::query( "SELECT id FROM site_menu WHERE parentid=? AND deleted IS NULL", $id )->fetchAll();

         if ( count( $children ) ) {

            foreach ( $children as $child ) {

               array_push( $ret, $child[ 0 ] );
               $ret = array_merge( $ret, $this->getChildrenRecursive( $child[ 0 ] ) );

            }

         }

         return $ret;

      }

      public function getDescendants( $id ) {

         $decendants = $this->getChildrenRecursive( $id );
         return $decendants;

      }


   }

?>
