<?PHP

   import( 'core.model' );

   class DBRFPageComment extends Model {

      static $table = 'pagecomments';
      static $basename = 'reedfoto';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'pagecommentid',
         ),
         'pageid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'type' => array(
            'type'    => DB_TYPE_ENUM,
            'size'    => 255,
            'constraints' => array( 'comment', 'file' ),
            'default' => 'comment',
         ),
         'comment' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 1024,
            'default' => '',
         ),
         'filehash' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'filetype' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 25,
            'default' => '',
         ),
         'filesize' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'filename' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'created' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'createdby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'deleted' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'deletedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'x' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
         ),
         'y' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0
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