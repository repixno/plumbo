<?PHP

   import( 'core.model' );
   import( 'math.uuid' );

   class DBMenu extends Model {

      static $table = 'menu';

      static $basename = 'site';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'parentid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'articleid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'siteid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 1,
         ),
         'identifier' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 36,
            'default' => '',
         ),
         'title' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'url' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'urlname' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 500,
            'default' => '',
         ),
         'template' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'image' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'translation' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16711425,
            'default' => '',
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'createdby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'updated'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'updatedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'deleted'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'deletedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'sortorder' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'score' => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => 0.00,
         ),
      );
      
      public function __postSetup() {
         
         if( !$this->created ) $this->created = date( 'Y-m-d H:i:s' );
         if( !$this->identifier ) $this->identifier = UUID::create();
         if( !$this->siteid ) {
            $siteid = Session::get( 'adminsiteid', 0 );
            if( !$siteid ) $siteid = Session::get( 'siteid', 1 );
            $this->siteid = $siteid;
         }
         return parent::__postSetup();
         
      }
      
      public function setImage( $image ) {
         
         if( $image && $image != 'false' ) {
            
            $this->fieldSet( 'image', $image );
            
         }
         
      }
      
      public function delete() {
         
         $this->deleted = date( 'Y-m-d H:i:s' );
         $this->save();
         
      }
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         return parent::save();
         
      }
      
   }

?>