<?PHP
   
   import( 'website.image' );
   
   class DBPhotoCompetitionImage extends Image {
      
      static $table = 'photocompetition_images';
      
      static $basename = 'site';
      
      static $fields = array(
         'photocompetitionid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'fielddata'=> array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'submitted'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'submittedby' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'approved' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
      );
      
      public function __setup() {
         
         parent::__setup();
         
         $this->submitted = date( 'Y-m-d H:i:s' );
         $this->submittedby = Login::userid();
         
      }
      
   }
   
?>