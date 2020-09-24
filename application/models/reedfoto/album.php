<?php

   /***
    * @author Tor Inge Lovland <tor.inge@eurofoto.no>
    ***/

   Import( 'core.model' );

   class DBReedfotoAlbum extends Model implements ModelCaching {

      static $table = 'reedfoto_album';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
         ),
         'identifier' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'uniqueid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
         ),
         'batchid' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'fname' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => false,
            'default' => '',
         ),
         'ename' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => false,
            'default' => '',
         ),
         'address' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => false,
            'default' => '',
         ),
         'zip' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'city' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => '',
         ),
         'aid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'activated' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'activatedby' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'school' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => '',
         ),
         'grade' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => '',
         ),
         
      );

   }

?>