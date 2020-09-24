<?php

   import( 'core.model' );

   class DBDiscountQuantum extends Model {
      
      static $table = 'discount_campaign_quantums';
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         ),
         'discount_campaign_id' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'alias' => 'campaignid',
         ),
         'article_id' => array(
            'type' => DB_TYPE_INRTEGER,
            'size' => 11,
            'alias' => 'refid',
         ),
         'min_quantity' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'alias' => 'min',
         ),
         'discount' => array(
            'type' => DB_TYPE_FLOAT,
            'size' => 11,
         ),
         'created' => array(
            'type' => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null,
         )
      );
      
      
      static function fromCampaignIdAndrefId( $id = 0, $refid = 0 ) {
         
         $quantums = array();
         $dqs = new DBDiscountQuantum();
         foreach( $dqs->collection( array( 'id' ), array( 'campaignid' => $id, 'refid' => $refid ) )->fetchAllAs( 'DBDiscountQuantum' ) as $dq ) {
            $quantums []= array(
               'refid' => $dq->refid,
               'min' => $dq->min,
               'discount' => $dq->discount,
            );
         }
         
         return $quantums;
         
      }
      
      
      static function fromCampaignId( $id = 0 ) {
         
         $quantums = array();
         $dqs = new DBDiscountQuantum();
         foreach( $dqs->collection( array( 'id' ), array( 'campaignid' => $id ), 'min_quantity ASC' )->fetchAllAs( 'DBDiscountQuantum' ) as $dq ) {
            $quantums[$dq->refid] []= array(
               'refid' => $dq->refid,
               'min' => $dq->min,
               'discount' => $dq->discount,
            );
         }
         
         return $quantums;
         
      }
      
      
      static function asArray() {
         
         return array(
            
         );
         
      }
      
   }


?>