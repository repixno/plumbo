<?php

   /**
    * DB model for historie_mal
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   
   import( 'core.model' );

   class DBOrderTemplate extends Model {
      
      static $table = 'historie_mal';
      
      static $fields = array(
         'ordrenr'      => array(
            'primary'      => true,
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'orderid',
         ),
         'artikkelnr'   => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'artnr',
         ),
         'malid'        => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'templateid',
         ),
         'lopenummer'       => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'lot',
         ),
         'page'         => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'bid'          => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'imageid',
         ),
         'antall'       => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
            'alias'        => 'quantity',
         ),
         'filnamn'      => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
            'alias'        => 'filename',
         ),
         'text'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'user_mod'     => array(
            'type'         => DB_TYPE_BOOLEAN,
            'size'         => 1,
            'null'         => true,
            'default'      => null,
         ),
         'redeye'     => array(
            'type'         => DB_TYPE_BOOLEAN,
            'size'         => 1,
            'null'         => true,
            'default'      => null,
         ),
         'varnish'     => array(
            'type'         => DB_TYPE_BOOLEAN,
            'size'         => 1,
            'null'         => true,
            'default'      => null,
         ),
         'upgrade'     => array(
            'type'         => DB_TYPE_BOOLEAN,
            'size'         => 1,
            'null'         => true,
            'default'      => null,
         ),
         
      );
      
   }


?>