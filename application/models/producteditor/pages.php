<?php

   /**

    * @author Tor Inge Lovland
    *
    */
   

   class DBproducteditorPages extends Model {
      
      static $table = 'producteditor_pages';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'templateid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'title'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'content'  => array(
                'type'      => DB_TYPE_STRING,
                'size'      => 16581375,
                'default'   => '',
            ),
            'sorting'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'visible' => array(
                'type'  => DB_TYPE_BOOLEAN,
                'default' => true,
            ),
            'printheight'=> array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'printwidth'=> array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'thumbnail'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 16581375,  
               'default'   => '',
            ),
            
         );

   }