<?PHP
   
   import( 'core.model' );
   
   class Language extends Model {
      
      static $basename = 'i18n';
      
      static $fields = array(
         'languageid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'elementname' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'code' => array( 
            'type'    => DB_TYPE_STRING,
            'default' => '',
            'size'    => 5,
         ),
         'isdefault'  => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => 0,
         ),
         'short' => array( 
            'type'    => DB_TYPE_STRING,
            'default' => '',
            'size'    => 2,
         ),
         'country' => array( 
            'type'    => DB_TYPE_STRING,
            'default' => '',
            'size'    => 2,
         ),
         'active'  => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => 0,
         ),
      );
      
   }
   
?>