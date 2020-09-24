<?php

   model( 'site.textentity' );
   
   class DBPaymentType extends DBTextEntity {
      
      static $table = 'payment_options';
      
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