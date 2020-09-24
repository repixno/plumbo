<?PHP
   
   import( 'core.model' );
   
   class DBSiteBlogPost extends Model {
      
      static $table = 'blog_posts';
      
      static $basename = 'site';
      
      static $fields = array(
         'postid'     => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'blogid'     => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'title'      => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'intro'      => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16581375,
            'default' => '',
         ),
         'body'       => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16581375,
            'default' => '',
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
      );
      
      public function __setup() {
         
         $setup = parent::__setup();
         $this->createdby = Login::userid();
         return $setup;
         
      }
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         $this->updatedby = Login::userid();
         return parent::save();
         
      }
      
   }
   
?>