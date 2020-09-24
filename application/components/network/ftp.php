<?PHP
   
   /**
    * FTP Transfer class
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class FTP {
      
      private $connection;
      private $hostname;
      private $timeout = 30;
      private $port = 21;
      
      /**
       * Constructor
       *
       * @param string $hostname     The hostname to use
       * @param integer $port        The port to use, default is 21
       * @param boolean $autoconnect Whether or not to automatically connect now, default is true
       * @param integer $timeout     The timeout to use, default is 30
       */
      public function __construct( $hostname, $port = 21, $autoconnect = true, $timeout = 30 ) {
         
         if( $autoconnect ) {
            
            $this->connect( $hostname, $port, $timeout );
            
         }
         
      }
      
      /**
       * Connect and open a session towards a remote server
       *
       * @param string $hostname     The hostname to use
       * @param integer $port        The port to use, default is 21
       * @param integer $timeout     The timeout to use, default is 30
       * @return boolean True on success, false on failure
       */
      public function connect( $hostname, $port = 21, $timeout = 30 ) {
         
         $this->connection = ftp_connect( $hostname, $port, $timeout );
         return true;
         
      }
      
      /**
       * Logs in using given credentials
       *
       * @param string $username The user to login as
       * @param string $password The password to validate
       * @return boolean True on success, false on failure
       */
      public function login( $username, $password ) {
         
         return ftp_login( $this->connection, $username, $password );
         
      }
      
      /**
       * Enable/Disable PASV mode
       *
       * @param boolean $on Whether or not to turn PASV on
       * @return boolean True on success, false on failure
       */
      public function pasv( $on = true ) {
         
         return ftp_pasv( $this->connection, $on );
         
      }
      
      /**
       * Disconnect the current session and destroy ot
       *
       * @return boolean True on success, false on failure
       */
      public function disconnect() {
         
         return ftp_close( $this->connection );
         
      }
      
      /**
       * Puts a local file and stores it remotely
       *
       * @param string $localfile What to put
       * @param string $remotefile Where to store it
       * @return boolean True on success, false on failure
       */
      public function put( $localfile, $remotefile ) {
         
         return ftp_put( $this->connection, $remotefile, $localfile, FTP_BINARY );
         
      }
      
      /**
       * Gets a remote file and stores it locally
       *
       * @param string $remotefile What to get
       * @param string $localfile Where to put it
       * @return boolean True on success, false on failure
       */
      public function get( $remotefile, $localfile ) {
         
         return ftp_get( $this->connection, $localfile, $remotefile, FTP_BINARY );
         
      }
      
      /**
       * Deletes a remote file
       *
       * @param string $remotefile The remote file to delte
       * @return boolean True on success, false on failure
       */
      public function delete( $remotefile ) {
         
         return ftp_delete( $this->connection, $remotefile );
         
      }
      
      /**
       * Create a directory on the remote server
       *
       * @param string $remotepath The remote path to create
       * @return boolean True on success, false on failure
       */
      public function mkdir( $remotepath ) {
         
         return ftp_mkdir( $this->connection, $remotepath );
         
      }
      
      /**
       * Removes a directory on the remote server
       *
       * @param string $remotepath The remote path to remove
       * @return boolean True on success, false on failure
       */
      public function rmdir( $remotepath ) {
         
         return ftp_rmdir( $this->connection, $remotepath );
         
      }
      
   }
   
?>