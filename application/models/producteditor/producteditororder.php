<?php
   /**
    * @author Tor Inge Lovland
    *
    */
   class DBproducteditorOrder extends Model {
      
      static $table = 'producteditor_order';
      
      static $fields = array(
            'id'    => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'userid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'malid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            ),
            'orderid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,   
               'default'   => null,
            )
         );

   }