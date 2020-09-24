<?php
   
   import( 'core.model' );
   
   class DBPrice extends Model {
      
      static $table = 'product_price';
      
      static $basename = 'site';
      
      static $fields = array(
         
         'id' => array(
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
         'countryid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'price'    => array(
            'type'    => DB_TYPE_FLOAT,
            'size'    => 11,
            'default' => 0.00,
         ),
         
      );
      
   }
   
?>