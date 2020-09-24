<?PHP
   
   model( 'site.textentity' );
   
   class DBArticle extends DBTextEntity {
      
      static $table = 'article';
      
      static $basename = 'site';
      
      static $fields = array(
         
      );
      
      public function Save() {
         
         Model::deleteFromObjectCacheByClassAndId( 'DBTextEntity', $this->id );
         Model::deleteFromObjectCacheByClassAndId( 'TextEntity', $this->id );
         
         return parent::save();
         
      }
      
   }
   
?>