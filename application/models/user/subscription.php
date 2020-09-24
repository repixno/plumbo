<?php

   /**
    * Model for payed storage subscription
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBSubscription extends Model {
      
      static $table = 'subscriptions';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'uid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => null,
            'alias'   => 'userid',
         ),
         'type_subscription' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => false,
            'default' => '',
         ),
         'registered'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => false,
            'default' => null,
         ),
         'start'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => false,
            'default' => null,
         ),
         'valid_to'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => false,
            'default' => null,
            'alias'   => 'validto',
         ),
         'latest_changed'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'edited_by'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
          'cancelled'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'active'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
            
         ),
         'order_id'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
            'alias'   => 'orderid',
         ),
      );
      
   }

?>