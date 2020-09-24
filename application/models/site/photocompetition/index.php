<?PHP
   
   import( 'core.model' );
   import( 'core.util' );
   
   class DBPhotoCompetition extends Model implements ModelCaching {
      
      static $table = 'photocompetition';
      
      static $basename = 'site';
      
      static $fields = array(
         'id' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
            'primary' => true,
         ),
         'userid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'uploadaid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'approvedaid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'title'      => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'description'=> array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'oncompleted'=> array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'urlname'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'template'   => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'createdby' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         
      );
      
      public function setUrlName( $urlname ) {
         
         $this->fieldSet( 'urlname', util::urlize( $urlname ) );
         $this->template = sprintf( 'competitions.photo.%s', $this->urlname );
         
      }
      
      public function __setup() {
         
         parent::__setup();
         
         $this->userid = Login::userid();
         $this->created = date( 'Y-m-d H:i:s' );
         $this->createdby = Login::userid();
         
      }
      
   }
   
?>