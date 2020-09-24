<?PHP
   
   import( 'core.model' );
   
   class DBAuthKey extends Model {
      
      static $basename = 'login';
      static $table = 'authkeys';
      
      static $fields = array(
         'authkeyid'  => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'privatekey' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 40,
            'default' => '',
         ),
         'publickey'  => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 40,
            'default' => '',
         ),
         'onlyips'    => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'portal'     => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 10,
            'default' => '',
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => 'now',
         ),
      );
      
   }
   
?>