<?php

   /**
    * Class for production conversions
    * 
    * @author Tor Inge Lovland
    *
    */

   class DBMapaid extends Model {
      
      static $table = 'production_mapaid';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'mapaidid'  => array(
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
            )
         );

   }