<?PHP
   
   class SSH2 {
      
      private $connection = null;
      
      public function __construct( $hostname = '', $port = 22 ) {
         
         if( !extension_loaded('ssh2') ) {
            
            throw new Exception( 'SSH2 extension not available.' );
            
         }
         
         if( !empty( $hostname ) ) {
            
            $this->connect( $hostname, $port );
            
         }
         
      }
      
      public function connect( $hostname, $port = 22 ) {
         
         $this->connection = ssh2_connect( $hostname, $port );
         
      }
      
      public function authorize( $username, $password ) {
         
         return ssh2_auth_password( $this->connection, $username, $password );
         
      }
      
      public function pubkey( $username ) {
         
         $privatekey = sprintf( '%s/data/keys/ssh2/id_rsa.key', getRootPath() );
         $publickey = sprintf( '%s/data/keys/ssh2/id_rsa.pub', getRootPath() );
         
         return ssh2_auth_pubkey_file( $this->connection, $username, $publickey, $privatekey );
         
      }
      
      public function shell( $termtype = 'xterm' ) {
         
         return ssh2_shell( $this->connection, $termtype );
         
      }
      
      public function execute( $commandline ) {
         
         return ssh2_exec( $this->connection, $commandline );
         
      }
      
      public function hostkey() {
         
         return ssh2_fingerprint( $this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX );
         
      }
      
   }
   
?>