<?php
   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    *
    */


   import( 'core.model' );

   class DBUserDiscountHistory extends Model {

      static $table = 'campaign_history';
      static $basename = 'discount';

      static $fields = array(

         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'discount_campaign_id'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => 0
         ),
         'user_id'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0
         ),
         'code'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'used'         => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'ordernr'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
            'alias'  => 'orderid',
         ),
         'amount'       => array(
            'type'      => DB_TYPE_FLOAT,
            'size'      => 11,
            'default'   => 0.0,
         ),
         'type'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'activated'         => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         )

      );

      public function getUserId() {

         return $this->user_id;

      }

      public function setUserId( $userid ) {

         return $this->user_id = $userid;

      }

      public function getCampaignId() {

         return $this->discount_campaign_id;

      }

      public function setCampaignId( $id ) {

         return $this->discount_campaign_id = $id;

      }

   }

?>
