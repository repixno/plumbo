<?php

   /**
    * DB model for leverpostei_order
    * 
    * @author Tor Inge Lovland
    *
    */

    /**
   CREATE TABLE leverpostei_order (
      id serial,
      orderid integer,
      productid integer,
      imageid integer,
      thumbid varchar(255),
      imagepos varchar(255),
      name varchar(255),
      year varchar(255),
      created timestamp with time zone
    );
   *******/
   
   import( 'core.model' );

   class DBLeverpostei extends Model {
      
      static $table = 'leverpostei_order';
      
      static $fields = array(
        'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'orderid'      => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'productid'      => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null,
         ),
         'imageid'   => array(
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => true,
            'default'      => null
         ),
         'thumbid'      => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'imagepos'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'name'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'year'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'created'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'malsize'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         'lokksize'         => array(
            'type'         => DB_TYPE_STRING,
            'size'         => 65535,
            'null'         => true,
            'default'      => null,
         ),
         
      );
      
   }


?>