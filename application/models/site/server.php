<?php
   
   import( 'core.model' );
   
   class DBServer extends Model {
      
      static $table = 'servers';
      
      static $basename = 'site';
      
      static $fields = array(
         
         'serverid' => array(
            'primary'  => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'hostname' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
            'alias'    => 'portal'
         ),
         'port'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'username' => array(
            'type'     => DB_TYPE_STRING,
            'size'     => 255,
            'default'  => '',
         ),
         'active' => array(
            'type'     => DB_TYPE_BOOLEAN,
            'default'  => true,
         ),
         'added'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'updated'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         
      );
      
   }
   
?>