<?PHP
   
   model( 'site.textentity' );
   
   class DBProductOption extends DBTextEntity {
      
      static $table = 'product_option';
      
      static $basename = 'site';
      
      static $fields = array(
         'productid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'refid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'refsubid' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'prodno' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'tags' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 65535,
            'default'  => '',
         ),
         'purchaseurl' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'orderkey'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'thumb' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         
      );
      
      public function getTitle( $language = false ) {
         
         $title = parent::getTitle( $language );
         return empty( $title ) ? 'Standard' : $title;
         
      }
      
      public function setRefId( $refid ) {
         
         $this->fieldSet( 'refid', $refid );
         
         $this->setProdNo( $refid );
         
      }
      
      public function setProdNo( $prodno ) {
         
         $this->fieldSet( 'prodno', sprintf( '%04d', $prodno ) );
         
      }
      
      public function save() {
         
         if( strlen( $this->prodno ) < 4 ) {
            
            $this->setProdNo( $this->prodno );
            
         }
         
         return parent::save();
         
      }
      
   }
   
?>
