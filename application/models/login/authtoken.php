<?PHP
   
   import( 'core.model' );
   import( 'math.uuid' );
   
   class DBAuthToken extends Model {
      
      static $basename = 'login';
      static $table = 'authtokens';
      
      static $fields = array(
         'token' => array(
            'primary' => true,
            'type'    => DB_TYPE_STRING,
            'size'    => 40,
            'default' => '',
         ),
         'authkeyid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'userid'     => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'sessionid'  => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 40,
            'default' => '',
         ),
         'ipaddress'  => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 15,
            'default' => '',
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => 'now',
         ),
         'signed'     => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'login'      => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
      public function __setup() {
         
         $result = parent::__setup();
         
         $this->token = UUID::create();
         
         return $result;
         
      }
      
   }
   
?>