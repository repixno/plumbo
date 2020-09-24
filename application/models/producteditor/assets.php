<?php

   /**

    * @author Tor Inge Lovland
    *
    */
   

   class DBproducteditorAssets extends Model {
      
      static $table = 'producteditor_assets';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'filename'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 65535,
               'null'      => true,   
               'default'   => null,
            ),
            'title'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'description'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 65535,
               'null'      => true,   
               'default'   => null,
            ),
            'width'        => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'height'       => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'category'    => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'created'   => array(
                'type'      => DB_TYPE_DATETIME,
                'null'      => true,
                'default'   => null
            ),
            'siteid'     => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'visible'   => array(
                'type'      => DB_TYPE_BOOLEAN,
                'default'   => true,
            ),
            'sorting'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'type'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            
         );

   }