<?php

   /**
    * Class for production lablink
    * 
    * @author Tor Inge Lovland
    *
    */
   
    /******************
    CREATE TABLE production_lablink(
          id serial,
          lablinkid text,
          downloaded timestamp with time zone,
          eforderid integer
        );
    *******************/

   class DBLablink extends Model {
      
      static $table = 'production_lablink';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'lablinkid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 1024,
               'null'      => true,
            ),
            'downloaded'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            'eforderid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
               'default'   => null
            ),
            'partner'    => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'partnerid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
         );

   }