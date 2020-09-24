<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBStoreOrder extends Model {
      
      static $table = 'site_store_order';
      
      static $fields = array(
         'storeorderid' => array(
            'primary' => true,
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         ),
         'storeuserid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'   => true,
            'default' => null,
         ),
         'orderid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null' => true,
            'default' => null,
         ),
         'received' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'null' => true,
            'default' => null,
         ),
         'smssent' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'emailsent' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'shipped' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'edi' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 256,
            'null' => true,
            'default' => null,
         ),
      );
      
      
      static function fromOrderId( $orderid ) {
         
         try {

            return DBStoreOrder::fromFieldValue(
               array(
                  'orderid' => $orderid,
               ),
               'DBStoreOrder'
            );

         } catch( Exception $e ) {

            return false;

         }
         
      }
      
   }


?>