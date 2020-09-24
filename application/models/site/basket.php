<?PHP

   import( 'core.model' );
   
   class DBBasket extends Model {

      static $table = 'basket';

      static $basename = 'site';

      static $fields = array(
         'uid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'basket' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16711425,
            'default' => '',
         ),
         'updated'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'notified'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         return parent::save();
         
      }
      
   }

?>