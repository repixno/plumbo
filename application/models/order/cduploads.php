<?php

   /**
    * Class for Archive cd/dvd
    * 
    * @author Tor Inge <tor.inge@eurofoto.no>
    *
    **/

   class DBCdUploads extends Model {
      
      static $table = 'cduploads';
      
      static $fields = array(
            'id'       => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
         ),
         'userid'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'uid',
         ),
         'email'       => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'null'      => false,
         ),
         'location'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65536,
            'null'      => false,
         ),
         'date' => array(
            'type'     => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null,
         ),
         'done' => array(
            'type'     => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null,
         ),
         
      );
      
   }


?>