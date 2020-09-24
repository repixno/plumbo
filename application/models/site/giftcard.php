<?php

   /**
    * Model for giftcards
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );
   
   class DBGiftcard extends Model {
      
      static $table = 'giftcard';
      
      static $fields = array(
         'giftcardid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
            'alias'   => 'id',
         ),
         'buyerid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => true,
            'default' => null
         ),
         'userid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => true,
            'default' => null,
         ),
         'refid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => true,
            'default' => null,
         ),
         'orgvalue' => array(
            'type'      => DB_TYPE_FLOAT,
            'default'   => 0.0,
         ),
         'value' => array(
            'type' => DB_TYPE_FLOAT,
            'default' => 0.0,
         ),
         'code' => array(
            'type' => DB_TYPE_STRING,
            'size' => 255,
            'null' => false,
         ),
         'description' => array(
            'type' => DB_TYPE_STRING,
            'size' => 65536,
            'null' => true,
            'default' => null,
         ),
         'expires' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'registered' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'changed' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         ),
         'usedorderid' => array(
            'type' => DB_TYPE_STRING,
            'null' => true,
            'default' => null,
         ),
         'orderid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => true,
            'default' => null,
         )
         
      );
      
   }


?>