<?PHP
   
   import( 'core.model' );
   
   class LanguageTranslation extends Model {
      
      static $basename = 'i18n';
      
      static $fields = array(
         'translationid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'languageid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'stringid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'translation' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
         'confirmed' => array(
            'type'   => DB_TYPE_BOOLEAN,
            'default'=> false,
         ),
      );
      
   }
   
?>