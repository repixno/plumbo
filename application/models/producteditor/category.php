<?php

   /**

    * @author Tor Inge Lovland
    *
    */
   

   class DBproducteditorCategory extends Model {
      
      static $table = 'producteditor_category';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'type'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'title'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            'sorting'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => true,
            ),
            'visible' => array(
                'type'  => DB_TYPE_BOOLEAN,
                'default' => true,
            )
            
         );

   }