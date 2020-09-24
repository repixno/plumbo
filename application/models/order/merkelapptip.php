<?php

   /**
    * DB model for Merkelapp tipping
    * 
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    *
    */

   import( 'core.model' );

   class DBMerkelappTip extends Model {

      static $table = 'tip';
      static $basename = 'merkelapp';

      static $fields = array(
         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'userid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'friends_email'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 256,
            'default'   => '',
         ),
         'coupon_code'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 256,
            'default'   => '',
         ),
         'date_tipped'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'date_coupon_used'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'date_collected'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'campaign_code'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 256,
            'default'   => '',
         ),
      );

   }


?>