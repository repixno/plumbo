<?PHP

   import( 'core.model' );

   class DBCardcatergoryCewe extends Model {
        
        static $table = 'card_category_cewe';
      
        static $basename = 'site';

        static $fields = array(
            'id'           => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'default'   => 0,
            ),
            'articleid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null,
            ),
            'grouping'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 16581375,
               'default'   => '',
            ),
            'catid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null,
            ),
            'link'=> array(
               'type'    => DB_TYPE_STRING,
               'size'    => 16777216,
               'default' => '',
            ),
            'title'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 16581375,
               'default'   => '',
            ),
            'hit' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null,
            ),
            'sort' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null,
            ),
            'thumbnail'=> array(
               'type'    => DB_TYPE_STRING,
               'size'    => 16777216,
               'default' => '',
            ),
            'visible'      => array(
               'type'      => DB_TYPE_BOOLEAN,
               'null'      => false,
               'default'   => true
            ),
            'created'      => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null
            ),
        );
    
   }
   
   
?>