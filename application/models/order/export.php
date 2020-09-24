<?php

   class DBOrderExport extends model {
      
      static $table = 'order_export';
      
      static $fields = array(
         'batchid' => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
            'alias'     => 'id',
         ),
         'firstorder'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'lastorder'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'generated'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'uploaded'     => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'portal'     => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => '',
         ),
      );
      
   }


?>