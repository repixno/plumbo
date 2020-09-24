<?php

   /**
    * Class for production lablink
    * 
    * @author Tor Inge Lovland
    *
    */
   
    /******************
    CREATE TABLE production_jp(
          id serial,
          jpid text,
          downloaded timestamp with time zone,
          sendt timestamp with time zone,
          eforderid integer
        );
    *******************/

   class DBjpProduction extends Model {
      
      static $table = 'production_jp';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'jpid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 1024,
               'null'      => true,
            ),
            
            'jpshopid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 1024,
               'null'      => true,
            ),
            
            'downloaded'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            'sendt'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            
            'jporderid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null
            ),
            'eforderid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null
            )
         );

   }