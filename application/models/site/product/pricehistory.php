<?PHP
   
   import( 'core.model' );
   
   class DBProductPriceHistory extends Model implements ModelCaching {
      
      static $table = 'product_pricehistory';
      
      static $basename = 'site';
      
      static $fields = array(
         'priceid' => array(
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
         'price' => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => 0.00,
         ),
         'portal' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 6,
            'default' => 'EF-997',
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
         'active' => array(
            'type'     => DB_TYPE_BOOLEAN,
            'default'  => true,
         ),
      );
      
      public function __setup() {
         
         // do the default setup
         $setup = parent::__setup();
         
         // setup some defaults for new objects
         $this->createdby = Login::userid();
         $this->created = date( 'Y-m-d H:i:s' );
         
         // run the parent setup
         return $setup;
         
      }
      
      public function asArray() {
         
         return array(
            'id' => $this->priceid,
            'price' => $this->price,
            'portal' => $this->portal,
            'created' => $this->created,
            'createdby' => $this->createdby,
            'active' => $this->active,
         );
         
      }
      
   }
   
?>