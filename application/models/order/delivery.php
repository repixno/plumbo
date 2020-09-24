<?php

   /**
    * DB class for historie_delivery
    * This name is just too sad.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBOrderDelivery extends Model {
      
      static $table = 'historie_delivery';
      
      static $fields = array(
         'ordrenr'         => array(
            'primary'         => true,
            'type'            => DB_TYPE_INTEGER,
            'size'            => 11,
            'null'            => true,
            'default'         => null,
            'alias'           => 'orderid',
         ),
         'deliverymethod'  => array(
            'type'            => DB_TYPE_INTEGER,
            'size'            => 11,
            'null'            => true,
            'default'         => null,
         ),
         'paymentmethod'   => array(
            'type'            => DB_TYPE_INTEGER,
            'size'            => 11,
            'null'            => true,
            'default'         => null,
         ),
         'weight'          => array(
            'type'            => DB_TYPE_INTEGER,
            'size'            => 11,
            'null'            => true,
            'default'         => null,
         ),
         'shopid'          => array(
            'type'            => DB_TYPE_INTEGER,
            'size'            => 11,
            'null'            => true,
            'default'         => null,
         ),
         
      );
      
   }


?>