<?php

   import( 'core.db' );
   import( 'cache.engine' );

   define( 'DB_TYPE_INTEGER',    0x01 );
   define( 'DB_TYPE_FLOAT',      0x02 );
   define( 'DB_TYPE_STRING',     0x03 );
   define( 'DB_TYPE_BOOLEAN',    0x04 );
   define( 'DB_TYPE_DATETIME',   0x05 );
   define( 'DB_TYPE_DATE',       0x06 );
   define( 'DB_TYPE_TIME',       0x07 );
   define( 'DB_TYPE_ENUM',       0x08 );

   /**
    * Base Model / Object-Relational Mapper
    *
    * Handles mapping of object oriented object structures to a "flat relational database".
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Model implements ArrayAccess, Iterator, Countable {

      static $fieldnames = array();
      static $tablealias = array();
      static $fieldalias = array();
      static $querycache = array();
      static $fieldaliasbytable = array();
      static $datatype = array();
      static $datasize = array();
      static $keyfield = array();
      static $treebuilt = array();
      
      private $data = array();
      private $isloaded = false;
      private $issaved = true;
      private $debugging = false;
      private $changes = array();
      
      private $modelcachingdisabled = false;

      /**
       * Default id for new elements
       */
      const CREATE = 0;

      /**
       * Constructor - automatically loads the id from disk if given
       *
       * @param integer $id id to load (if applicable)
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function __construct( $id = Model::CREATE ) {
         
         // check if model caching is disabled
         $this->modelcachingdisabled = !Settings::Get( 'model', 'caching', true );
         
         // setup defaults
         $this->__setup();
         
         // were we given an id?
         if( $id ) {

            // attempt to laod it
            $result = $this->load( $id );
            
         } else {

            // return success
            $result = true;

         }

         // run the post-setup
         $this->__postSetup();
         
         // return the result
         return $result;

      }

      /**
       * Generic object setter
       *
       * @param string $key the key to set
       * @param string $val the value to set
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function __set( $key, $val ) {

         // is there an override method for this field?
         if( method_exists( $this, $method = 'set'.$key ) ) {

            // if so, call it
            return call_user_func( array( $this, $method ), $val );

         } else {

            // call the internal setter function
            return $this->fieldSet( $key, $val );

         }

      }

      /**
       * Generic object getter
       *
       * @param string $key the key to get
       * @return string the value set in $key
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function __get( $key ) {

         // is there an override method for this field?
         if( method_exists( $this, $method = 'get'.$key ) ) {

            // if so, call it
            return call_user_func( array( $this, $method ) );

         } else {

            // call the internal getter function
            return $this->fieldGet( $key );

         }

      }

      /**
       * The actual internal setter function.
       * Handles type safety and storing
       *
       * @param string $key The name of the field to set
       * @param mixed $val The value stored in the field
       * @return mixed Depends on the datatype of the field
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      protected function fieldSet( $key, $val ) {

         // find the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // resolve field aliases
         $key = Model::$fieldalias[$classname][$key];

         // make sure this field has a datatype
         if( isset( Model::$datatype[$classname][$key] ) ) {

            // mark it as not saved
            $this->issaved = false;

            // check the datatype and validate each value
            switch( Model::$datatype[$classname][$key] ) {

               default:
               case DB_TYPE_ENUM:
               case DB_TYPE_STRING:
                  $newvalue = is_null( $val ) ? null : substr( $val, 0, isset( Model::$datasize[$classname][$key] ) ? Model::$datasize[$classname][$key] : 255 );
                  break;
               case DB_TYPE_BOOLEAN:
                  $newvalue = is_null( $val ) ? null : ( $val ? 't' : 'f' );
                  break;
               case DB_TYPE_DATETIME:
                  $newvalue = ($unix = strtotime( $val ) ) !== false ? date( 'Y-m-d H:i:s', $unix ) : null;
                  break;
               case DB_TYPE_FLOAT:
                  $newvalue = is_null( $val ) ? null : (float) str_replace( ',', '.', $val );
                  break;
               case DB_TYPE_INTEGER:
                  $newvalue = is_null( $val ) ? null : (int) $val;
                  break;

            }

            // has the value actually changed?
            if( $newvalue !== $this->data[$key] ) {

               // mark this field as changed
               $this->changes[$key] = true;

            }

            // set the data value and return it
            return $this->data[$key] = $newvalue;

         } else {

            return null;

         }

      }

      /**
       * The actual internal getter function.
       *
       * @param string $key The name of the field to get
       * @return mixed The value stored in the field or null if the field does not exist
       */
      protected function fieldGet( $key ) {

         // find the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // resolve field aliases
         $key = Model::$fieldalias[$classname][$key];
         
         // check the datatype and validate each value
         switch( Model::$datatype[$classname][$key] ) {
            
            case DB_TYPE_BOOLEAN:
               return is_null( $this->data[$key] ) ? null : ( $this->data[$key] == 't' ? true : false );
               break;
               
            default:
               return isset( $this->data[$key] ) ? $this->data[$key] : null;
               break;
               
         }
      }

      /**
       * Gets all fields in the object as an associative array
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      protected function fieldsGet() {

         // return a copy of our data
         return $this->data;

      }

      /**
       * Sets all fields in the object form an associative array
       *
       * @param array $fields the fields to set
       * @return boolean False if the input is not an array, otherwise true
       * @author Oyvind Selbek
       */
      protected function fieldsSet( $fields ) {

         // make sure this is an array before we start
         if( !is_array( $fields ) ) return false;

         // iterate through the fields
         foreach( $fields as $field => $value ) {

            // proxy to the fieldset method
            $this->fieldSet( $field, $value );

         }

         // return success
         return true;

      }

      /**
       * Executes the internal save subroutine
       *
       * You can override this routine in your class to validate or
       * alter data prior to utilizing parent::__save() to save data.
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function save() {

         // call the __save() routine
         return $this->__save();

      }

      /**
       * Executes the internal load subroutine
       *
       * You can override this routine in your class to validate or
       * alter data after utilizing parent::__save() to load data.
       *
       * @param integer $id The id to load
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function load( $id ) {

         // call the __load() routine
         if( !empty( $id ) ) {

            if( !$this->__load( $id ) || !$this->isLoaded() ) {

               throw new Exception( sprintf( '%s failed to load', get_class( $this ) ), 0 );
               return false;

            } else {

               return true;

            }

         } else {

            return true;

         }

      }

      /**
       * Executes the internal delete subroutine
       *
       * You can override this routine in your class to validate or
       * alter data before utilizing parent::__delete() to delete it.
       *
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function delete() {

         // call the __delete() routine
         return $this->__delete();

      }

      /**
       * Builds the required save query and executes it
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final protected function __save() {

         // fetch the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // find the primary key field for this table
         $primarykey = Model::$keyfield[$classname];
         
         // find the associated tables
         $tables = Model::$fieldnames[$classname];

         // if we don't have at least one key-field, fail to save.
         if( !isset( $this->data[$primarykey] ) ) return false;

         // start a fresh transaction
         DB::beginTransaction();

         // try...
         try {

            // find the value of the current Primary ID-field 
            $id = $this->data[$primarykey];

            // is this a new object?
            if( !$this->isloaded ) {

               // if so, iterate through all the tables...
               foreach( $tables as $table => $subfields ) {

                  $fields = array( $primarykey );
                  $values = !$id ? array() : array( $id );
                  $setkey = !$id ? array( 'DEFAULT' ) : array( '?' );

                  foreach( $subfields as $field => $data ) {

                     if( !isset( $data['primary'] ) || !$data['primary'] ) {
                        $fields[] = '"'.$field.'"';
                        $setkey[] = '?';

                        if( Model::$datatype[$classname][$field] == DB_TYPE_BOOLEAN ) {
                           $values[] = isset( $this->data[$field] ) ? $this->data[$field] == 't' ? 't' : 'f' : null;
                        } else {
                           $values[] = isset( $this->data[$field] ) ? $this->data[$field] : null;
                        }

                     }

                  }

                  // if we have no ID yet, create a new record in the primary table and return the ID
                  if( $id === Model::CREATE ) {

                     $query = "INSERT INTO $table (".implode( ',', $fields ).') VALUES ('.implode(',', $setkey ).') RETURNING '.$primarykey;
                     $id = DB::query( $query, $values )->fetchSingle();
                     $this->{$primarykey} = $id;
                     if( $this->debugging ) util::Debug( $query, $values, 'NEW ID: '. $id );

                  // otherwise, just grab the current id and run with it
                  } else {

                     $query = "INSERT INTO $table (".implode( ',', $fields ).') VALUES ('.implode(',', $setkey ).')';
                     if( $this->debugging ) util::Debug( $query, $values );
                     DB::query( $query, $values );

                  }

               }

            // no? update it then
            } else {

               // iterate through all the tables
               foreach( $tables as $table => $subfields ) {

                  $fields = array();
                  $values = array();

                  // find all changed fields...
                  foreach( $subfields as $field => $data ) {

                     if( isset( $this->changes[$field] ) ) {

                        if( !isset( $data['primary'] ) || !$data['primary'] ) {
                           $fields[] = '"'.$field.'" = ?';
                           $values[] = isset( $this->data[$field] ) ? $this->data[$field] : null;
                        }

                     }

                  }

                  $values[] = $id;

                  // and if we found at least one...
                  if( count( $fields ) > 0 ) {

                     // update the corresponding table.
                     $query = sprintf( 'UPDATE "%s" SET %s WHERE %s = ?', $table, implode( ', ', $fields ), $primarykey );
                     if( $this->debugging ) util::Debug( $query, $values );
                     DB::query( $query, $values );

                  }

               }

            }

            // commit the transaction
            DB::commit();

            // mark it as saved
            $this->issaved = true;
            $this->isloaded = true;

         // if something bad happens...
         } catch( Exception $e ) {

            // rollback the transaction
            DB::rollback();

            // throw a database-exception
            throw new DatabaseException( $e->getMessage(), 0 );

            // return failure
            return false;

         }

         // is this object cacheable?
         if( $this instanceof ModelCaching && !$this->modelcachingdisabled ) {

            // cache the object data
            $this->saveToObjectCache();

         }

         // return success!
         return true;

      }

      /**
       * Deletes the current object from all linked tables
       *
       * @return boolean true on success, false on failure
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final protected function __delete() {

         // fetch the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // find the primary key field for this table
         $primarykey = Model::$keyfield[$classname];
         
         // if we don't have a keyfield defined, return failure
         if( !isset( $this->data[$primarykey] ) ) return false;

         // is this object cacheable?
         if( $this instanceof ModelCaching && !$this->modelcachingdisabled ) {

            // cache the object data
            $this->deleteFromObjectCache();

         }

         // begin the database transaction
         DB::beginTransaction();

         // catch any problems...
         try {

            // find the id to delete on
            $id = $this->data[$primarykey];

            // iterate through all linked tables and delete based on the key
            foreach( Model::$fieldnames[$classname] as $table => $subfields ) {

               // build the query...
               $query = sprintf( 'DELETE FROM "%s" WHERE %s = ?', $table, $primarykey );

               // ...and execute it!
               DB::query( $query, $id );

            }

            // commit the transaction!
            DB::commit();

            // return successful!
            return true;

         // ...in case of problems...
         } catch( Exception $e ) {

            // rollback the transaction
            DB::rollback();

            // throw a database-exception
            throw new DatabaseException( $e->getMessage(), 0 );

            // return failure
            return false;

         }

      }

      /**
       * Loads an object from the current cache engine
       *
       * @param integer $objectid The id of the object to load
       * @return boolean True on success, false if not found
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      protected function loadFromObjectCache( $objectid ) {

         // fetch the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // make sure we have the primary key field defined
         if( !isset( $this->data[Model::$keyfield[$classname]] ) ) return false;

         // create/find the cache engine
         $cacheengine = CacheEngineFactory::current();

         // create the object key
         $objectkey = sprintf( 'object-cache-%s-%s', $classname, $objectid );

         // attempt to read back the object from cache
         $data = $cacheengine->read( $objectkey, null );

         // if we failed, simply return failure
         if( !is_array( $data ) ) return false;

         // iterate through the fetched data and store
         foreach( $data as $key => $value ) {
            $this->data[$key] = $value;
         }

         // clear changes list
         $this->changes = array();

         // return success
         return true;

      }

      /**
       * Saves an object to the current cache engine
       *
       * @return boolean True on success, false if no keyfield is set
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      protected function saveToObjectCache() {

         // fetch the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // make sure we have the primary key field defined
         if( !isset( $this->data[Model::$keyfield[$classname]] ) ) return false;

         // create/find the cache engine
         $cacheengine = CacheEngineFactory::current();

         // find the objectid to cache under
         $objectid = $this->data[Model::$keyfield[$classname]];

         // create the object key
         $objectkey = sprintf( 'object-cache-%s-%s', $classname, $objectid );

         // write the data to the cache
         $cacheengine->write( $objectkey, $this->data, 7200 );

         // return success
         return true;

      }

      /**
       * Deletes an object from the current cache engine
       *
       * @return boolean True on success, false if no keyfield is set
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function deleteFromObjectCache() {

         // fetch the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // find the primary key field for this table
         $primarykey = Model::$keyfield[$classname];
         
         // make sure we have the primary key field defined
         if( !isset( $this->data[$primarykey] ) ) return false;

         // find the objectid to cache under
         $objectid = $this->data[$primarykey];
         
         // drop the class object from cache
         return Model::deleteFromObjectCacheByClassAndId( $classname, $objectid );
         
      }

      /**
       * Deletes an object from the current cache engine
       *
       * @param string $class The class of the object to delete
       * @param integer $objectid The id of the object to delete
       */
      static function deleteFromObjectCacheByClassAndId( $class, $objectid ) {
         
         // create/find the cache engine
         $cacheengine = CacheEngineFactory::current();
         
         // create the object key
         $objectkey = sprintf( 'object-cache-%s-%s', strtolower( $class ), $objectid );
         
         // write the data to the cache
         $cacheengine->erase( $objectkey );
         
         // return success
         return true;
         
      }

      /**
       * Builds the required load query and executes it
       *
       * @param integer $id The id of the primary key to load
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final protected function __load( $id = Model::CREATE ) {

         // fetch the classname of the current object
         $classname = strtolower( get_class( $this ) );
         
         // find the primary key field for this table
         $primarykey = Model::$keyfield[$classname];
         
         // make sure we have the primary key field defined
         if( !isset( $this->data[$primarykey] ) ) return false;

         // is this an attempt to load, and is the object cacheable?
         if( $id != Model::CREATE && $this instanceof ModelCaching && !$this->modelcachingdisabled ) {

            // attempt to load the object from cache
            if( $this->loadFromObjectCache( $id ) ) {

               // do we have debugging enabled?
               if( $this->debugging ) util::Debug( 'Object loaded from memory cache', 'ID: '. $id );

               // mark it as loaded
               $this->isloaded = true;

               // if we succeed, return
               return true;

            }

         }

         // go!
         try {

            // build the initial query, selecting all fields
            list( $query, $fieldmap ) = $this->__buildBaseSelect();

            // build the finalized query including the where statement
            $query = sprintf( '%s WHERE %s.%s=? LIMIT 1', $query, $fieldmap[$primarykey], $primarykey );

            // do we have debugging enabled?
            if( $this->debugging ) util::Debug( $query, 'ID: '. $id );
            
            // execute the query
            $res = DB::query( $query, $id );

            // did we find any rows?
            if( count( $res ) ) {

               // fetch the row
               $row = $res->fetchRow( PDO::FETCH_ASSOC );

               // iterate the row...
               foreach( $row as $key => $value ) {

                  // ...and set it on the object
                  $this->fieldSet( $key, $value );

               }

               // clear changes list
               $this->changes = array();

               // mark it as loaded
               $this->isloaded = true;

            } else {

               // define the key
               $this->data[$primarykey] = $id;

               // return failure
               return false;

            }

            // is this object cacheable?
            if( $this instanceof ModelCaching && !$this->modelcachingdisabled ) {

               // cache the object data
               $this->saveToObjectCache();

            }

            return true;

         } catch( Exception $e ) {

            // throw a database-exception
            throw new DatabaseException( $e->getMessage(), 0 );

            return false;

         }

      }

      /**
       * Returns whether an object was originally loaded from db or created in memory
       *
       * @return boolean True if loaded from db, false if created in memory
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function isLoaded() {

         // forward the internal state
         return $this->isloaded ? true : false;

      }

      /**
       * Returns whether an object has been edited since it was last opened or saved.
       *
       * @return boolean True if the object is saved, false otherwise
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function isSaved() {

         // forward the internal state
         return $this->issaved ? true : false;

      }

      /**
       * Enable/disable query generation debugging
       *
       * @param boolean $enabled True to enable, false to disable
       */
      public function debug( $enabled = true ) {

         $this->debugging = $enabled;

      }

      /**
       * Returns a collection of objects of the instances type
       *
       * @param array $fields An optional list of fields to fetch, otherwise all
       * @param array $where An array of key/value-pairs to lookup
       * @param string $orderby An optional field to order by, otherwise none
       * @return DBResult A DBresult object from which one can fetch the fields
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function collection( $fields = null, $where = array(), $orderby = null, $limit = 0, $from = 0 ) {

         // find the classname
         $classname = strtolower( get_class( $this ) );

         if ( !isset( $where ) ) $where = array();

         // get a list of fields to include
         $includefields = array_keys( $where );

         // initialize the where-list
         $wherefields = array();
         $wherevalues = array();
         $requiredtables = array();

         // iterate through all the fields
         foreach( Model::$fieldnames[$classname] as $table => $tablefields ) {

            // find the alias map for this table
            $tablefields = array_keys( Model::$fieldaliasbytable[$classname][$table] );

            // iterate the list of fields (if any) present in this table
            foreach( array_intersect( $includefields, $tablefields ) as $field ) {
               
               // is this a null-value?
               if( is_null( $where[$field] ) ) {

                  // build the where-fields list
                  $wherefields[] = sprintf( '%s.%s IS NULL', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field] );

               // is this an array-style parameter
               } else if( is_array( $where[$field] ) ) {

                  // expand the opcode and values
                  list( $opcode, $value ) = $where[$field];

                  // based on opcode value
                  switch( $opcode ) {

                     // default opcode set
                     case '>=': case '>':
                     case '<=': case '<':
                     case '!=': 
                     case 'LIKE':
                     case 'ILIKE':
                        
                        // build the where-fields list using the custom opcode
                        $wherefields[] = sprintf( '%s.%s %s ?', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field], $opcode );

                        // add it to the values-record
                        $wherevalues[] = $value;

                        break;
                        
                     case 'BETWEEN':
                        
                        // build the where-fields list using the custom opcode
                        $wherefields[] = sprintf( '%s.%s %s ? AND ?', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field], $opcode );
                        
                        // add it to the values-record
                        $wherevalues[] = $value[0];
                        $wherevalues[] = $value[1];
                        
                        break;

                     // support 'in (s,e,t)' syntax)
                     case 'IN':

                        // build the where-fields list using the custom opcode
                        if( !is_array( $value ) ) {
                           $value = explode(',', $value);
                        }

                        // create the wherefield and the filldata placeholders
                        $filldata = implode( ', ', array_fill( 0, count( $value ), '?' ) );
                        $wherefields[] = sprintf( '%s.%s IN ('.$filldata.')', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field] );
                        
                        // add it to the values-record
                        foreach( $value as $fillvalue ) {
                           $wherevalues[] = $fillvalue;
                        }
                        break;

                     case 'IS':

                        $value = strtoupper( $value );
                        switch( $value ) {

                           case 'NULL':
                           case 'NOT NULL':

                              // build the where-fields list
                              $wherefields[] = sprintf( '%s.%s IS %s', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field], $value );
                              break;

                        }
                        break;

                     // otherwise
                     default:

                        // build the where-fields list
                        $wherefields[] = sprintf( '%s.%s = ?', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field] );

                        // add it to the values-record
                        $wherevalues[] = $value;
                        break;

                  }

               // otherwise...
               } else {

                  // build the where-fields list
                  $wherefields[] = sprintf( '%s.%s = ?', Model::$tablealias[$classname][$table], Model::$fieldalias[$classname][$field] );

                  // add it to the values-record
                  $wherevalues[] = $where[$field];

               }

               // add to required tables
               $requiredtables[] = $table;

            }

         }

         // build the base query
         list( $query, $fieldmap ) = $this->__buildBaseSelect( $fields, $requiredtables );

         // prepare the base query and implode the where-field list
         $wherequery = count( $wherefields ) > 0 ? sprintf( 'WHERE %s', implode( ' AND ', $wherefields ) ) : '';
         
         // do we have an order by?
         if( is_null( $orderby ) ) {

            // prepare the query and implode the where-field list
            $query = sprintf( '%s %s', $query, $wherequery );

         } else {

            // prepare the query and implode the where-field list with order-by
            $query = sprintf( '%s %s ORDER BY %s', $query, $wherequery, $orderby );

         }

         // prepare limitations
         $limit = (int) $limit;
         $from = (int) $from;

         // limit the result-set?
         if( $limit > 0 ) {

            if( $from > 0 ) {

               $query = sprintf( '%s LIMIT %d OFFSET %d', $query, $limit, $from );

            } else {

               $query = sprintf( '%s LIMIT %d', $query, $limit );

            }

         } else if( $from > 0 ) {

            $query = sprintf( '%s OFFSET %d', $query, $from );

         }

         // if debugging, dump
         if( $this->debugging ) {

            die( util::Debug( $query, $wherevalues ) );

         }

         // return the DBResult object
         return DB::query( $query, $wherevalues );

      }

      private function __buildBaseSelect( $findfields = null, $requiredclasses = array() ) {

         // find the classname
         $classname = strtolower( get_class( $this ) );
         
         // calculate the signature
         $signature = md5( serialize( array( $findfields, $requiredclasses ) ) );
         
         // first, check if its cached
         if( isset( Model::$querycache[$classname][$signature] ) && 
          is_array( Model::$querycache[$classname][$signature] ) ) {
            return Model::$querycache[$classname][$signature];
         }
         
         // initialize variables
         $tableid = 0;
         $fields = array();
         $fieldmap = array();

         // remap required-classes to be key-indexed
         $requiredclasses = is_array( $requiredclasses ) ? array_flip( $requiredclasses ) : array();
         $tempfields = is_array( $findfields ) ? array_flip( $findfields ) : null;
         $findfields = array();

         // do reverse array-mapping to findfields
         if( !empty( $tempfields ) ) {
            foreach( $tempfields as $field => $key ) {
               $findfields[Model::$fieldalias[$classname][$field]] = $key;
            }
         } else {
            $findfields = $tempfields;
         }
         
         // find the list of fields
         $tables = Model::$fieldnames[$classname];

         // create a list of tables (in reverse order)
         $tlist = array_reverse( array_keys( $tables ) );

         // create entry for the initial table
         $table = array_shift( $tlist );

         // find the alias for this table
         $tablealias = Model::$tablealias[$classname][$table];

         // remember the last alias (for joins)
         $prevalias = $tablealias;

         // create the initial join-record
         $joins = array( sprintf( '"%s" AS %s', $table, $tablealias ) );

         // iterate the fields of this table
         foreach( $tables[$table] as $field => $data ) {

            // if findfields is an array, make sure the field is in it
            if( !is_array( $findfields ) || isset( $findfields[$field] ) ) {

               // add the field to the query field-list
               $fields[$field] = $tablealias;

               // add the field to the field-map
               $fieldmap[$field] = $tablealias;

            }

         }

         // do we have more tables?
         if( count( $tlist ) > 0 ) {

            // iterate the list of tables
            foreach ( $tlist as $table ) {

               // should we include this table?
               $dotable = is_array( $requiredclasses ) ? ( isset( $requiredclasses[$table] ) ? true : false ) : false;

               // iterate
               if( isset( $tables[$table] ) ) foreach( $tables[$table] as $field => $data ) {

                  // if findfields is an array, make sure the field is in it
                  if( !is_array( $findfields ) || isset( $findfields[$field] ) ) {

                     // find the alias for this table
                     $tablealias = Model::$tablealias[$classname][$table];

                     // add the field to the query field-list
                     $fields[$field] = $tablealias;

                     // add the field to the field-map
                     $fieldmap[$field] = $tablealias;

                     // make sure this table is included
                     $dotable = true;

                  }

               }

               // at least one field is set for this table, or its
               // required by a where-clause and should be included.
               if( $dotable == true ) {

                  // find the alias for this table
                  $tablealias = Model::$tablealias[$classname][$table];

                  // add the table to the query
                  $joins[] = sprintf( '"%s" AS %s ON %s.%s=%s.%s', $table, $tablealias, $prevalias, Model::$keyfield[$classname], $tablealias, Model::$keyfield[$classname] );

                  // remember the last alias (for joins)
                  $prevalias = $tablealias;

               }

            }

         }

         // iterate through fieldfields and build the final field-list in the correct order.
         $foundfields = array();

         // is the correct order by given field order?
         if( isset( $findfields ) && is_array( $findfields ) ) {

            // iterate through the fields and use that order
            foreach( $findfields as $field => $orderkey ) {
               $foundfields[] = sprintf( '%s.%s', $fields[$field], $field );
            }

         } else {

            // lookup the field order in the table definitions
            foreach( $tables as $table => $fielddefinitions ) {
               foreach( $fielddefinitions as $field => $fielddata ) {
                  $foundfields[] = sprintf( '%s.%s', $fields[$field], $field );
               }
            }

         }

         // expand the fields into a string
         $fields = implode( ', ', $foundfields );

         // expand the joins into a string
         $joins = implode( ' LEFT JOIN ', $joins );

         // build the final query and return it
         return Model::$querycache[$classname][$signature] = array( sprintf( 'SELECT %s FROM %s', $fields, $joins ), $fieldmap );

      }

      /**
       * Class setup routine. Here one can setup default values using PHP.
       * One may implement this function in a decendant class and forward
       * the call to this function, then set the default values.
       *
       * In your own overridden decendant, call parent::__setup(); first.
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      protected function __setup() {

         // build the field tree for data handling.
         $this->__buildFieldTree();

      }


      protected function __postSetup() {



      }

      static function fromFieldValue( $fieldset, $classname = null, $returnemptyobject = true ) {

         // instanciate the collection
         $collection = new $classname();
         
         // find the real tablename
         $keyfield = Model::$keyfield[strtolower($classname)];
         
         // create a new class instance
         $objectid = (int) $collection->collection( array( $keyfield ), $fieldset )->fetchSingle();
         
         // did we find a row?
         if( $objectid ) {

            // ...and instantiate it
            return new $classname( $objectid );

         } else {
            
            //do we want to return a empty object when the object id is not found?
            if ( $returnemptyobject ) {
               
               // instantiate the default class
               return new $classname();
               
            } else {
               
               //return false
               return false;
            }

         }

      }

      static function getTableName( $classname ) {

         // find the classvars defined in this class
         $classvars = get_class_vars( $classname );

         // find the fields defined in this class
         return strtolower( (
                isset( $classvars['basename'] )
                     ? $classvars['basename'].'_'
                     : '' ) . (
                isset( $classvars['table'] )
                     ? $classvars['table']
                     : $classname ) );

      }

      /**
       * Builds a complete filetree for use in save/load query building.
       * Also sets a default data set for all fields in the class tree.
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final private function __buildFieldTree() {

         // intialize the tableid field
         $tableid = 0;

         // get the initial classname
         $classname = strtolower( get_class( $this ) );
         $loopclass = $classname;
         
         // have we already built this class' fieldtree?
         if( !isset( Model::$treebuilt[$classname] ) ) {
            
            // find the classvars defined in this class
            $classvars = get_class_vars( $loopclass );
            
            // find the fields defined in this class
            Model::$fieldnames[$classname] = array( strtolower( (
                               isset( $classvars['basename'] )
                                    ? $classvars['basename'].'_'
                                    : '' ) . (
                               isset( $classvars['table'] )
                                    ? $classvars['table']
                                    : $loopclass ) )
                            => isset( $classvars['fields'] )
                                    ? $classvars['fields']
                                    : array()
                            );
            
            // iterate all parent classes until one reaches the model parent
            while( ( ( $loopclass = strtolower( get_parent_class( $loopclass ) ) ) != 'model' ) && $loopclass ) {
               
               // find the classvars defined in this class
               $classvars = get_class_vars( $loopclass );
               
               // find the fields defined in this class
               Model::$fieldnames[$classname] = array_merge(
                                  array( strtolower( (
                                     isset( $classvars['basename'] )
                                          ? $classvars['basename'].'_'
                                          : '' ) . (
                                     isset( $classvars['table'] )
                                          ? $classvars['table']
                                          : $loopclass ) )
                                  => isset( $classvars['fields'] )
                                          ? $classvars['fields']
                                          : array()
                                  ), Model::$fieldnames[$classname]
                               );
               
               // ensure fieldaliasbytable has each and every table in its array list
               Model::$fieldaliasbytable[$classname][$this->getTableName( $loopclass )] = array();
               
            }
            
            // iterate through all classes...
            foreach( Model::$fieldnames[$classname] as $table => $fields ) {
               
               // create a new alias for this table
               Model::$tablealias[$classname][$table] = sprintf( 't%d', ++$tableid );
   
               // find the table for this class
               $table = $this->getTableName( $table );
   
               // ...and iterate all fields ...
               foreach( $fields as $field => $data ) {
   
                  // is this the primary key?
                  if( isset( $data['primary'] ) && $data['primary'] ) {
   
                     // store it as the keyfield
                     Model::$keyfield[$classname] = $field;
   
                  }
   
                  // add the field itself to the alias-list
                  Model::$fieldalias[$classname][$field] = $field;
                  Model::$fieldaliasbytable[$classname][$table][$field] = $field;
   
                  // append any alias of this fields to the map
                  if( is_array( $data['alias'] ) ) {
                     foreach( $data['alias'] as $fieldalias ) {
                        Model::$fieldalias[$classname][$fieldalias] = $field;
                        Model::$fieldaliasbytable[$classname][$table][$fieldalias] = $field;
                     }
                  } elseif( !empty( $data['alias'] ) ) {
                     Model::$fieldalias[$classname][$data['alias']] = $field;
                     Model::$fieldaliasbytable[$classname][$table][$data['alias']] = $field;
                  }
   
                  // build the datatype structure
                  Model::$datatype[$classname][$field] = isset( $data['type'] ) ? $data['type'] : DB_TYPE_STRING;
                  Model::$datasize[$classname][$field] = isset( $data['size'] ) ? $data['size'] : 255;
   
                  // ... and build a data section
                  if( isset( $data['default'] ) ) {
                     
                     $this->fieldSet( $field, $data['default'] );
                     
                  } else {
                     
                     $this->data[$field] = isset( $data['null'] )
                                               && $data['null']
                                                ? null
                                                : '';
                  }
                  
               }
   
            }

            // tag this class' field tree as built
            Model::$treebuilt[$classname] = true;
            
         } else {
            
            // iterate through all classes...
            foreach( Model::$fieldnames[$classname] as $class => $fields ) {
               
               // ...and iterate all fields ...
               foreach( $fields as $field => $data ) {
                  
                  // ... and build a data section
                  if( isset( $data['default'] ) ) {
                     
                     $this->fieldSet( $field, $data['default'] );
                     
                  } else {
                     
                     $this->data[$field] = isset( $data['null'] )
                                               && $data['null']
                                                ? null
                                                : '';
                  }
                  
               }
               
            }
            
         }
         
         // mark it as not loaded
         $this->isloaded = false;

         // mark it as not saved
         $this->issaved = false;

      }

      /**
       * Implements ArrayAccess::offsetGet()
       *
       * @param string $offset The offset to retreive
       * @return mixed The value stored at the offset
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetGet( $offset ) {

         // map to the field of hte same name
         return $this->__get( $offset );

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

         // set the field of the same name to the value
         return $this->__set( $offset, $value );

      }

      /**
       * Implements ArrayAccess::offsetExists()
       *
       * @param string $offset The offset to validate
       * @return boolean True if the offset is valid
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetExists( $offset ) {

         // is there a field of this name defined?
         return isset( $this->data[$offset] );

      }

      /**
       * Implements ArrayAccess::offsetUnset()
       *
       * @param string $offset The offset to unset
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function offsetUnset( $offset ) {

         // nil the field in the class
         $this->__set( $offset, null );

      }

      /**
       * Implements Iterator::rewind()
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function rewind() {

         // reset the classData array
         reset( $this->data );

      }

      /**
       * Implements Iterator::valid()
       *
       * @return boolean True while there is a valid array, false otherwise
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function valid() {

         // make sure the entry is valid
         return !is_null( key( $this->data ) );

      }

      /**
       * Implements Iterator::key()
       *
       * @return string The current key of the array
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function key() {

         // proxy to the key method
         return key( $this->data );

      }

      /**
       * Implements Iterator::current()
       *
       * @return mixed The current value of the array
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function current() {

         // proxy to the current method
         current( $this->data );

         // retrieve the current value of this key
         return $this->__get( key( $this->data ) );

      }

      /**
       * Implements Iterator::next()
       *
       * @return mixed Moves the iterator pointer forward
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function next() {

         // proxy to the next method
         next( $this->data );

         // retrieve the current value of this key
         return $this->__get( key( $this->data ) );

      }

      /**
       * Implements Countable::count()
       *
       * @return integer The number of fields in this class
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      final public function count() {

         // proxy to the count method
         return count( $this->data );

      }

      public function __toString() {

         $result = '';
         foreach( $this as $key => $val ) {
            $result .= "$key:\t$val\n";
         }
         return $result;

      }

      public function __clone() {

         $classname = strtolower( get_class( $this ) );
         $defaultvalue = Model::$fieldnames[$classname][ $this->getTableName( $classname ) ][ Model::$keyfield[$classname] ][ 'default' ];
         $this->{Model::$keyfield[$classname]} = $defaultvalue;

         $this->issaved = false;
         $this->isloaded = false;

      }

   }
   
   interface ModelCaching {}
   
?>