<?php
   
   config( 'database.visma' );
   
   class VismaBase {
		
      protected $database = false;
      protected $mssqlmonths = false;
      protected $connection = false;
      
		function __construct( $database = false ) {
			
		   $this->database = $database;
		   
		   $this->mssqlmonths = array( 'JAN' => 1, 
			                            'FEB' => 2, 
			                            'MAR' => 3, 
			                            'APR' => 4, 
			                            'MAY' => 5, 
			                            'JUN' => 6, 
			                            'JUL' => 7, 
			                            'AUG' => 8, 
			                            'SEP' => 9, 
			                            'OCT' => 10, 
			                            'NOV' => 11, 
			                            'DEC' => 12 
		                            );
		}
		
		function debug( $mixed ) {
			
			if( count( $_SERVER ) ) {
				
				echo "<pre>\n";
				print_r( $mixed );
				echo "</pre>\n";
				
			} else {
				
				print_r( $mixed );
				
			}
			
		}
		
		function getTblOwner() {
		   
		   return Settings::Get( 'visma', 'tblowner', '' );
		   
		}
		
		function databases() { 
			
		   // connect to mysql database if not yet connected...!
			$this->__ensureConnection();
         
			$ar = array();
			
			if( mssql_select_db( "master" ) ) { 
				
				if( $res = $this->query( "SELECT name FROM sysdatabases WHERE name <> 'master'" ) ) { 
					
					while( list( $name ) = $this->fetchRow( $res ) ) $ar[] = $name;
					
				}
				
			}
			
			mssql_select_db( Settings::Get( 'visma', 'database', '' ) ); 
			
			return $ar;
			
		}
        		
		function tables() {
			
			$result = array();
			$res = $this->query( "SELECT name FROM sysobjects WHERE type='U' or type='V' AND (name NOT IN ('sysallocations','syscolumns','syscomments','sysdepends','sysfilegroups','sysfiles','sysfiles1','sysforeignkeys','sysfulltextcatalogs','sysindexes','sysindexkeys','sysmembers','sysobjects','syspermissions','sysprotects','sysreferences','systypes','sysusers','sysalternates','sysconstraints','syssegments','REFERENTIAL_CONSTRAINTS','CHECK_CONSTRAINTS','CONSTRAINT_TABLE_USAGE','CONSTRAINT_COLUMN_USAGE','VIEWS','VIEW_TABLE_USAGE','VIEW_COLUMN_USAGE','SCHEMATA','TABLES','TABLE_CONSTRAINTS','TABLE_PRIVILEGES','COLUMNS','COLUMN_DOMAIN_USAGE','COLUMN_PRIVILEGES','DOMAINS','DOMAIN_CONSTRAINTS','KEY_COLUMN_USAGE')) ORDER BY name" );
			while( list( $table ) = $this->fetchRow( $res ) ) {
				$result[] = array( $table );
			}	$this->freeSet( $res );
			
			return $result;
			
		}
		
		function columns( $table ) {
			
			$result = array();
			$res = $this->query( sprintf( "SELECT c.name,t.name,c.length FROM syscolumns c JOIN systypes t ON t.xusertype=c.xusertype JOIN sysobjects o ON o.id=c.id WHERE o.name='%s'", $table ) );
			while( list( $column, $type, $length ) = $this->fetchRow( $res ) ) {
				$result[] = array( $column, $type, $length );
			}	$this->freeSet( $res );
			
			return $result;
			
		}
		
		function query( $query, $debug = false ) {
			
			// connect to mysql database if not yet connected...!
			$this->__ensureConnection();

			// enable debugging?
			if ($debug) $this->debug( $query."\n" );
			
			if( ( substr( $query, 0, 6 ) != 'SELECT' ) && ( substr( $query, 0, 7 ) != 'sp_help' ) ) die( "$query\n". 'ONLY SELECTs, please!' );

			// query the backend database
			$return = mssql_query( $query, $this->connection );
			
			// did it fail?
			if( !$return ) die( "QUERY ERROR:\n".@mssql_get_last_message()."\n" );
			
			// return the result
			return $return;
			
		}
		
		function error() {

			return mssql_get_last_message();

		}
		
		function errNo() {
			
			$res = $this->query( "select @@ERROR" );
			if (!$res) return false;
			
			$arr = $this->fetchArr( $res );
			$this->freeSet( $res );
			
			if( is_array($arr) ) 
				return $arr[0];
		   else
		    	return -1;
		    
		}
		
		function transactBegin() {
			
			$this->query( "BEGIN TRAN" );
			$this->inTransaction = true;
			
		}
		
		function transactCommit() {
			
			$this->query( "COMMIT TRAN" );
			$this->inTransaction = false;
			
		}
		
		function transactRollback() {
			
			$this->query( "ROLLBACK TRAN" );
			$this->inTransaction = false;
			
		}
		
		function transactActive() {
			
			return $this->inTransaction ? true : false;
			
		}
		
      function affectedRows( $resultSet ) {
      	
      	return @mssql_rows_affected( $resultSet );
			/*
				$res = $this->query('select @@rowcount');
				if( !$res ) return false;
				list( $id ) = $this->fetchRow( $res );
				$this->freeSet( $res );
				return $id;
         */
		}
		
		function freeSet( $resultSet ) {
			
			return @mssql_free_result( $resultSet );
			
		}
		
		function fetchRow( $resultSet ) {
			
			// return a row from libmySQL
			return @mssql_fetch_row( $resultSet );
			
		}
		
		function fetchObj( $resultSet ) {
			
			// return a row from libmySQL
			return @mssql_fetch_object( $resultSet );
			
		}
		
		function fetchAssoc( $resultSet ) {
			
			// return a row from libmySQL
			return @mssql_fetch_assoc( $resultSet );
			
		}
		
		function fetchArr( $resultSet ) {
			
			// return a row from libmySQL
			return @mssql_fetch_array( $resultSet );
			
		}
		
		function numRows( $resultSet ) {
			
			// return number of rows in set
			return @mssql_num_rows( $resultSet );
			
		}
		
		function insertId() {
			
			// return the last autoindex from MSSQL
			$res = $this->query( "select @@identity" );
			if( !$res ) return false;
			list( $id ) = $this->fetchRow( $res );
			$this->freeSet( $res );
			return $id;
			
		}
		
		function escape( $string ) {
			
			// connect to mysql database if not yet connected...!
			$this->__ensureConnection();
			
			// escape the result via addslashes
			return addslashes( $string );
			
		}
		
		function __ensureConnection() {
			
		   $dbsettings = Settings::GetSection( 'visma', array() );
			
			// connect to mysql database if not yet connected...!
			if( !$this->connection ) {
				#	print_r( array( $this->hostname, $this->username, $this->password ) ); die();
				$this->connection = mssql_connect( $dbsettings['hostname'], $dbsettings['username'], $dbsettings['password'] ) or die( "FATAL ERROR: Unable to connect to the database server!\n" );
			   mssql_select_db( $this->database ? $this->database : $dbsettings['database'], $this->connection ) or die( "FATAL ERROR: Unable to select the local database!\n" );
			}
			
		}
		
		// mssql uses a default date like Dec 30 2000 12:00AM
		function UnixDate( $v ) {
			
			//Mon Dec 30 2000 12:00AM
			if (!ereg( "([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4}))"
				,$v, $rr)) return $v;
				
			if ($rr[3] <= 1970) return 0;
			
			$themth = substr(strtoupper($rr[1]),0,3);
			$themth = $this->mssqlmonths[$themth];
			if ($themth <= 0) return false;
			
			// h-m-s-MM-DD-YY
			return  mktime(0,0,0,$themth,$rr[2],$rr[3]);

		}
		
		function UnixTimeStamp( $v ) {
			
			//Dec 30 2000 12:00AM
			if (!ereg( "([A-Za-z]{3})[-/\. ]+([0-9]{1,2})[-/\. ]+([0-9]{4}) +([0-9]{1,2}):([0-9]{1,2}) *([apAP]{0,1})"
				,$v, $rr)) return $v;
			if ($rr[3] <= 1970) return 0;
			
			$themth = substr(strtoupper($rr[1]),0,3);
			$themth = $this->mssqlmonths[$themth];
			if ($themth <= 0) return false;
			
			if (strtoupper($rr[6]) == 'P') {
				if ($rr[4]< 12) $rr[4]+= 12;
			} else {
				if ($rr[4]==12) $rr[4] = 0;
			}
			
			// h-m-s-MM-DD-YY
			return  mktime($rr[4],$rr[5],0,$themth,$rr[2],$rr[3]);
			
		}
		
	}
	
?>