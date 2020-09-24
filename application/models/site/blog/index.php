<?PHP

   import( 'core.model' );
   
   class DBSiteBlog extends Model {

      static $table = 'blog';

      static $basename = 'site';

      static $fields = array(
         'blogid'     => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'uid'        => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'shortname'  => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 100,
            'default' => '',
         ),
         'theme'      => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'title'      => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'description'=> array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16581375,
            'default' => '',
         ),
         'numposts'   => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => 'now',
         ),
         'updated'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
      public function setShortname( $shortname ) {
         
         $this->fieldSet( 'shortname', $shortname ? util::urlize( $shortname ) : null );
         
      }
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         return parent::save();
         
      }
      
   }

?>