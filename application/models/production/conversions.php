<?php

   /**
    * Class for production conversions
    * 
    * @author Tor Inge Lovland
    *
    */

   class DBConversions extends Model {
      
      static $table = 'conversions';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'order_date'=> array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            'order_id'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'article_id'=> array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
               'alias'     => 'artnr',
            ),
            'added_at'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            'hostname'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'began_at'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
            'ended_at'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
            'status'    => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
         );

   }