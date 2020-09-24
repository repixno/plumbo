<?PHP
   
   // load the database configuration
   config( 'database.config' );
   
   /**
    * Used for responding to database errors
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class DatabaseException extends CriticalException {}
   
   /**
    * DBResult - wraps PDOstatements and provide helper 
    * functions and interface implementations for ease
    * of use.
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class DBResult implements Countable {
      
      /**
       * Stores the last SQL statement
       *
       * @var string
       */
      private $statement;
      
      /**
       * Wrap the PDOStatement and store it locally
       *
       * @param PDOStatement $pdo The PDO statement to assign
       */
      public function __construct( PDOStatement $pdo ) {
         
         // store the PDO statement for use in fetch-functions
         $this->statement = $pdo;
         
      }
      
      /**
       * Fetches a single column and returns it
       *
       * @param integer $mode The mode to fetch results in
       * @return mixed The value returned by the column
       */
      public function fetchSingle( $mode = DB::FETCH_NUM ) {
         
         // fetch the first row
         $row = $this->statement->fetch( $mode );
         
         // fetch the first column of the first row and return it
         return is_array( $row ) ? current( $row ) : null;
         
      }
      
      /**
       * Fetches a single row and returns it
       *
       * @param integer $mode The mode to fetch results in
       * @return array The returned row, keyed by the given mode
       */
      public function fetchRow( $mode = DB::FETCH_NUM ) {
         
         // fetch the first row and return it
         return $this->statement->fetch( $mode );
         
      }
      
      /**
       * Fetches a single row and returns it as an associative array
       *
       * @param integer $mode The mode to fetch results in
       * @return array The returned row, keyed by the given mode
       */
      public function fetchAssoc( $mode = DB::FETCH_ASSOC ) {
         
         // fetch the first row and return it
         return $this->statement->fetch( $mode );
         
      }
      
      /**
       * Fetches all rows in a resultset and returns it
       *
       * @param integer $mode The mode to fetch results in
       * @return array An array of rows, each keyed by the given mode
       */
      public function fetchAll( $mode = DB::FETCH_NUM ) {
         
         // fetch all rows using PDOStatement::fetchAll and return them
         return $this->statement->fetchAll( $mode );
         
      }
      
      /**
       * Fetches all rows in a resultset and returns it
       *
       * @param integer $mode The mode to fetch results in
       * @return array An array of rows, each keyed by the given mode
       */
      public function fetchAllAs( $classname ) {
         
         // make sure the class exists first
         if( !class_exists( $classname ) ) {
            throw new CriticalException( 'FetchAllAs: Class does not exist ('.$classname.')' );
         }
         
         // fetch all rows using PDOStatement::fetchAll and return them as instances
         $objects = array();
         foreach( $this->statement->fetchAll( $mode ) as $row ) {
            
            list( $objectid ) = $row;
            $objects[] = new $classname( $objectid );
            
         }
         
         // return the list of objects
         return $objects;
         
      }
      
      /**
       * Implements the Countable::count() interface
       *
       * @return integer the number of rows in the result-set for selects or affected rows for updates/inserts
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function count() {
         
         /*
         // is this a select query?
         if( strpos( $this->statement->queryString, 'SELECT' ) !== false ) {
            
            // if so, fetch resultset size through the SQL query layer.
            return DB::query( 'SELECT FOUND_ROWS()' )->fetchSingle();
            
         } else {
         */   
         // if not, just return the affected rows
         return $this->statement->rowCount();
         /*   
         }
         */
         
      }
      
   }

   /**
    * DBQuery - the PDO wrapper
    *
    */
   class DBQuery extends PDO {
      
      /**
       * Queries the database for a resultset
       * 
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      public function execQuery() {
         
         // prepare the query
         $args = func_get_args();
         $query = array_shift( $args );
         
         // create the resultset and execute
         $pdo = parent::prepare( $query );
         $pdo->execute( $args );
         
         // return the wrapped result
         return new DBResult( $pdo );
         
      }
      
   }
   
   /**
    * The database class
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class DB {
      
      /**
       * Holds the current database engine instance
       *
       * @var DBQuery an instance of the DBQuery class
       */
      static $instance;
      
      /**
       * PDO remaps
       */
      const FETCH_NUM = PDO::FETCH_NUM;
      const FETCH_ASSOC = PDO::FETCH_ASSOC;
      
      /**
       * Static wrapper
       *
       * @return DBResult
       */
      static function query() {
         
         // grab the function argument list
         $args = func_get_args();
         
         // allow passing in array of params as second param
         if( isset( $args[1] ) && is_array( $args[1] ) ) {
            
            // expand the variables
            $query = $args[0];
            $args = $args[1];
            
            // create the new call-stack
            array_unshift( $args, $query );
            
         }
         
         // ensure we're connected
         DB::ensureConnection();
         
         // pass the query to the DB instance
         return call_user_func_array( array( DB::$instance, 'execQuery' ), $args );
         
      }
      
      /**
       * Returns the ID of the last inserted row
       *
       * @param string $sequence Optional name of the sequence bound to the id field for PGSQL
       * @return integer The id of the last inserted row
       */
      static function insertid( $sequence = null ) {
         
         // ensure we're connected
         DB::ensureConnection();
         
         // if we have a defined sequence...
         if( isset( $sequence ) ) {
            
            // pass the sequence to the DB instance
            return DB::$instance->lastInsertId( $sequence );
            
         } else {
            
            // pass the request to the DB instance
            return DB::$instance->lastInsertId();
            
         }
         
      }
      
      /**
       * Initiates a SQL transaction
       *
       * @return boolean true on success, false on failure
       */
      static function beginTransaction() {
         
         // ensure we're connected
         DB::ensureConnection();
         
         // pass the request on to the DB layer
         return DB::$instance->beginTransaction();
         
      }
      
      /**
       * Commits the current SQL transaction
       *
       * @return boolean true on success, false on failure
       */
      static function commit() {
         
         // ensure we're connected
         DB::ensureConnection();
         
         // pass the request on to the DB layer
         return DB::$instance->commit();
         
      }
      
      /**
       * Rolls back the current SQL transaction
       *
       * @return boolean true on success, false on failure
       */
      static function rollBack() {
         
         // ensure we're connected
         DB::ensureConnection();
         
         // pass the request on to the DB layer
         return DB::$instance->rollback();
         
      }
      
      /**
       * Escapes a parameter using the PDO driver for use in a query
       *
       * @param string $string The string to escape
       * @return string The escaped string
       */
      static function escape( $string ) {
         
         // ensure we're connected
         DB::ensureConnection();
         
         // pass the request on to the DB layer
         return DB::$instance->quote( $string );
         
      }
      
      /**
       * Ensures that a valid database connection exists
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function ensureConnection( $forceReCreate = false ) {
         
         // if we don't have a valid instance, or we need to recreate it...
         if( !DB::$instance instanceof DBQuery || $forceReCreate ) {
            
            // ...load the databaase settings...
            $config = Settings::GetSection( 'database' );
            
            // ... and instanciate and setup the database client instance
            DB::$instance = new DBQuery( $config['readwrite']['dsn'], $config['readwrite']['user'], $config['readwrite']['pass'] );
            
            // set a few transaction-level defaults such as errormode handling, fetchmode etc.
            DB::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            DB::$instance->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, DB::FETCH_NUM );
            
         }
         
      }
      
   }
   
   class DBReadOnly extends DB {
      
      /**
       * Ensures that a valid database connection exists
       *
       * @author Oyvind Selbek <oyvind@selbek.com>
       */
      static function ensureConnection( $forceReCreate = false ) {
         
         // if we don't have a valid instance, or we need to recreate it...
         if( !DB::$instance instanceof DBQuery || $forceReCreate ) {
            
            // ...load the databaase settings...
            $config = Settings::GetSection( 'database' );
            
            // ... and instanciate and setup the database client instance
            DB::$instance = new DBQuery( $config['readonly']['dsn'], $config['readonly']['user'], $config['readonly']['pass'] );
            
            // set a few transaction-level defaults such as errormode handling, fetchmode etc.
            DB::$instance->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            DB::$instance->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, DB::FETCH_NUM );
            
         }
         
      }
      
   }
   
?>