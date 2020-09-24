<?PHP
   
   import( 'core.model' );
   
   class DBUserFastpass extends Model implements ModelCaching {
      
      static $table = 'fastpass';
      
      static $basename = 'user';
      
      static $fields = array(
         'uid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'url' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
         
      );
      
   }
   
?>