<?PHP
   
   import( 'core.model' );
   import( 'core.util' );
   
   class DBTextEntityRevision extends Model implements ModelCaching {
      
      static $table = 'textentity_revisions';
      
      static $basename = 'site';
      
      static $fields = array(
         'textrevisionid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'textentityid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'languageid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'title' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'ingress'   => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'body' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'urlname' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
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
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         $this->updatedby = login::userid();
         
         return parent::save();
         
      }
      
   }
   
?>