<?php

   /**
    * DB class for historie_options
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   

   class DBOrderOption extends Model {
      
      static $table = 'historie_options';
      
      static $fields = array(
         'ordrenr'   => array(
            'primary'      => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
            'alias'     => 'orderid',
         ),
         'malid'     => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
            'alias'     => 'templateid',
         ),
         'option'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'suboption' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'antall'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
            'alias'     => 'quantity',
         )
         
      );
      
   }


?>