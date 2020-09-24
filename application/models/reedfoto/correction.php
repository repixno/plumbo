<?PHP

   import( 'core.model' );

   class DBRFCorrection extends Model {

      static $table = 'correction';
      static $basename = 'reedfoto';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'correctionid',
         ),
         'userid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'state' => array(
            'type'    => DB_TYPE_ENUM,
            'size'    => 255,
            'constraints' => array( 'setup', 'ready', 'opened', 'approved' ),
            'default' => 'setup',
         ),
         'title' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'comment' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 1024,
            'default' => '',
         ),
         'created' => array(
            'type'    => DB_TYPE_DATETIME,
            'default' => null,
            'null'    => true,
         ),
         'createdby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'ready' => array(
            'type'    => DB_TYPE_DATETIME,
            'default' => null,
            'null'    => true,
         ),
         'readyby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'opened' => array(
            'type'    => DB_TYPE_DATETIME,
            'default' => null,
            'null'    => true,
         ),
         'openedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'approved' => array(
            'type'    => DB_TYPE_DATETIME,
            'default' => null,
            'null'    => true,
         ),
         'approvedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
      );

      public function __setup() {

         /*
         $this->created = date( 'Y-m-d H:i:s' );
         $this->createdby = Login::userid();
         */

         return parent::__setup();

      }

   }

?>