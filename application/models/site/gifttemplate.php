<?PHP
   
   import( 'core.model' );
   import( 'core.util' );
   import( 'math.uuid' );
   import( 'legacy.ef2x' );
   
   class DBGiftTemplate extends Model implements ModelCaching {
      
      static $table = 'mal';
      
      static $fields = array(
         'malid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'id',
         ),
         'name'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'description'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'artikkelnr'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'articleid',
         ),
         'category'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'portal' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'language'   => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'visible'  => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => true,
         ),
         'priority'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'image_medium'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         
      );

      // return a proper string from the description property
      public function getDescription() {
         
         return EF2x::getLanguageResource( $this->fieldGet( 'description' ) );
         
      }
      
      // make description read-only
      public function setDescription() {
         
         return false;
         
      }
      
      // return a proper string from the description property
      public function getName() {

         return EF2x::getLanguageResource( $this->fieldGet( 'name' ) );
         
      }
      
      // make description read-only
      public function setName() {
         
         return false;
         
      }
      
   }
   
?>
