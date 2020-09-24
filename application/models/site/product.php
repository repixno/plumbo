<?PHP
   
   model( 'site.textentity' );
   
   class DBProduct extends DBTextEntity {
      
      static $table = 'product';
      
      static $basename = 'site';
      
      static $fields = array(
         'images' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
      );
      
      public function Save() {
         
         Model::deleteFromObjectCacheByClassAndId( 'DBTextEntity', $this->id );
         Model::deleteFromObjectCacheByClassAndId( 'TextEntity', $this->id );
         
         return parent::save();
         
      }
      
   }
   
?>