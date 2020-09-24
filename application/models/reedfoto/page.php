<?PHP

   import( 'core.model' );
   import( 'math.uuid' );

   class DBRFPage extends Model {

      static $table = 'page';
      static $basename = 'reedfoto';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'pageid',
         ),
         'correctionid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'title' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'imageguid' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'orderkey' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'pagetext' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 2048,
            'default' => '',
         ),
         'width' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'height' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
      );

      public function __setup() {

         $result = parent::__setup();
         $this->imageguid = UUID::create();
         return $result;

      }

   }

?>