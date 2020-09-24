<?php
   /**
    * @author Tor Inge Lovland
    *
    */
   class DBproducteditorTemplates extends Model {
      
      static $table = 'producteditor_templates';
      
      static $fields = array(
            'id'    => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'productid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'title' => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'category'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'sorting'   => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'created'      => array(
                'type'      => DB_TYPE_DATETIME,
                'null'      => true,
                'default'   => null
            ),
            'saved'        => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null
            ),
            'visible'   => array(
                'type'      => DB_TYPE_BOOLEAN,
                'default'   => true,
            ),
            'printheight' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'printwidth' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'grouping'  => array(
                'type'      => DB_TYPE_STRING,
                'size'      => 16581375,
                'default'   => '',
            ),
            
         );

   }