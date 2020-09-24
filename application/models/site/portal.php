<?PHP

   import( 'core.model' );

   class DBPortal extends Model {

      static $table = 'kampanje';

      static $fields = array(
         'kode' => array(
            'alias'   => 'code',
            'primary' => true,
            'type'    => DB_TYPE_STRING,
            'size'    => 6,
            'default' => 0
         ),
         'namn' => array(
            'alias'   => 'title',
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => ''
         ),
         'start_dato' => array(
            'alias'   => 'validfrom',
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => ''
         ),
         'stop_dato' => array(
            'alias'   => 'validto',
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => ''
         ),
         'velkomst' => array(
            'alias'   => 'welcometext',
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0
         )

      );

      public function getCode() {

         return $this->kode;

      }

      public function getTitle() {

         return $this->namn;

      }

      public function setTitle( $title = '' ) {

         $this->namn = $title;

      }

   }

?>