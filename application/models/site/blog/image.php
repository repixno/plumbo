<?PHP
   
   import( 'core.model' );
   
   class DBSiteBlogImage extends Model {
      
      static $table = 'blog_images';
      
      static $basename = 'site';
      
      static $fields = array(
         'postimageid'     => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'postid'     => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'imageid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => 'now',
         ),
         'createdby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
      );
      
      public function __setup() {
         
         $setup = parent::__setup();
         $this->createdby = Login::userid();
         return $setup;
         
      }
      
   }
   
?>