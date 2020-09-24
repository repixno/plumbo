<?php

   model( 'site.textentity' );
   
   class DBDeliveryType extends DBTextEntity {
      
      static $table = 'delivery_options';
      
      static $basename = 'site';
      
      static $fields = array(
         'refid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         )
      );
      
   }


?>