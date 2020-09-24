<?php

   /**
    * Class for Archive cd/dvd
    * 
    * @author Tor Inge <tor.inge@eurofoto.no>
    *
    **/

   class DBArchive extends Model {
      
      static $table = 'arkivcd';
      
      static $fields = array(
            'ordrenr'=> array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'orderid',
         ),
         'tidspunkt' => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null,
         ),
         'uid'       => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'userid',
         ),
         'cd'        => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
         ),
         'dvd'       => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
         ),
         'exported'  => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null,
         ),
         'locked'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'download_began_at'  => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null,
         ),
         'download_ended_at'  => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null,
         ),
         
      );
      
   }


?>