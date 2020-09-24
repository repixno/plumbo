<?php

   /**
    * Class for production conversions
    * 
    * @author Tor Inge Lovland
    *
    */
   
    /******************
    CREATE TABLE production_utestemme (
          id serial,
          utestemmeid integer,
          downloaded timestamp with time zone,
          toproduction timestamp with time zone,
          sent timestamp with time zone
        );
    *******************/

   class DBUtestemme extends Model {
      
      static $table = 'production_utestemme';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'utestemmeid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'downloaded'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            'toproduction'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
            'sent'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
            'eforderid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'projecttype'    => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
         );

   }