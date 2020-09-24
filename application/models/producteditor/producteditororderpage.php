<?php
   /**
    * @author Tor Inge Lovland
    *
    */
   class DBproductOrderPage extends Model {
      
      static $table = 'producteditor_order_page';
      
      static $fields = array(
            'id'    => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'refid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'pagenr'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'content'  => array(
                'type'      => DB_TYPE_STRING,
                'size'      => 16581375,
                'default'   => '',
            ),
            'thumb'  => array(
                'type'      => DB_TYPE_STRING,
                'size'      => 16581375,
                'default'   => '',
            ),
            'x'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'y'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'color'  => array(
                'type'      => DB_TYPE_STRING,
                'size'      => 16581375,
                'default'   => '',
            ),
         );

   }