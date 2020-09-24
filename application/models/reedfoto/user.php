<?PHP

   import( 'core.model' );

   class DBRFUser extends Model {

      static $table = 'user';
      static $basename = 'reedfoto';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'userid',
         ),
         'type' => array(
            'type'    => DB_TYPE_ENUM,
            'size'    => 255,
            'constraints' => array( 'user', 'admin' ),
            'default' => 'user',
         ),
         'fullname' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'username' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'password' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         /*
         'created'    => array(
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
         */
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