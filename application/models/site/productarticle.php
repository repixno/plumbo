<?PHP

   import( 'core.model' );

   class DBProductArticle extends Model implements ModelCaching {

      static $table = 'article';

      static $fields = array(
         'artnr' => array(
            'type'    => DB_TYPE_INTEGER,
            'primary' => true,
            'size'    => 11,
            'default' => 0,
         ),
         'printx' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'printy' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
      );

   }

?>