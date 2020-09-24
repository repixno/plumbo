<?php
   /**
    * @author Tor Inge Løvland
    *
    *
    */
 

   import( 'core.model' );

   class DBminSkyProject extends Model {

        static $table = 'projects';
        static $basename = 'minsky';
        static $fields = array(

        'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
        ),
        'projectxml'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 16581375,
            'default'   => '',
        ),
        'productid'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
        ),
        'created'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
        ),
        'count'        => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
        ),
        'shareid'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
        ),
        'imagelist'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 16581375,
            'default'   => '',
        ),
        'projectid'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
        ),
        'activated'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
        ),
        'name'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
        ),
        'email'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
        ),
        'phone'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
        ),
        
      );
   }

?>