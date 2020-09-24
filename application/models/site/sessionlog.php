<?php

   import( 'core.model' );
   
   class DBSessionLog extends Model {
      
      static $table = 'oversikt';
      
      static $basename = 'session';
      
      static $fields = array(
         
         'sessionid' => array(
            'primary'  => true,
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'tidspunkt'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
            'alias'   => 'timestamp',
         ),
         'uid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'portal_skin' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
            'alias'    => 'portal'
         ),
         'hostname' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'useragent' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 65535,
            'default'  => '',
         ),
         'remoteip' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'client_x'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'client_y'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         
      );
      
   }
   
?>