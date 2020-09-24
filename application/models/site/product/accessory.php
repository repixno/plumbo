<?PHP
   
   import( 'core.model' );
   
   class DBProductAccessory extends Model implements ModelCaching {
      
      static $table = 'product_accessories';
      
      static $basename = 'site';
      
      static $fields = array(
         'accessoryid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'productid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'onlyoptionid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'accessoryproductid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'minquantity' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'maxquantity' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
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
      );
      
      public function __postSetup() {
         
         // setup some defaults for new objects
         if( $this->id == Model::CREATE ) {
            $this->createdby = Login::userid();
            $this->created = date( 'Y-m-d H:i:s' );
         }
         
         // run the parent setup
         return parent::__postSetup();
         
      }
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         $this->updatedby = login::userid();
         
         return parent::save();
         
      }
      
   }
   
?>