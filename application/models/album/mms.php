<?php

   /**
    * @author André Nordstrand <andre@iw.no>
    * 
    * 
    */
   
   import( 'core.model' );
   
   class DBMMS extends Model  {
      
      static $table = 'mms';
      
      static $fields = array (
         'mmsid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
         ),
         'mmskode' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => true,
            'default' => '',
         ),
         'uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => '',
         ),
         'tidspunkt' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => '',
         ),
         'path' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
         'kampanje_kode' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
         'deleted' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
      
      );
      
   }

?>