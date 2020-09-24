<?php

   /**
    * Class for production conversions Fovea
    * 
    * @author Tor Inge Lovland
    *
    */
   
    /******************
    CREATE TABLE production_fovea (
          id serial,
          foveaid integer,
          downloaded timestamp with time zone,
          toproduction timestamp with time zone,
          sent timestamp with time zone
        );
    *******************/

   class DBFovea extends Model {
      
      static $table = 'production_fovea';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'foveaid'  => array(
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
            'eforderid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'deleted'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
         );

   }