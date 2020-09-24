<?php

   import( 'core.model' );
   
   class DBBetaTester extends Model {
      
      static $table = 'betatesters';
      
      static $basename = 'site';
      
      static $fields = array(
         'uid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
            'primary' => true,
         ),
         'started'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'lastlogin'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
   }
   
?>