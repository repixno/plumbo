<?php
   /**
   * @author Svein Arild Bergset <sab@interweb.no>
   *
   *
   */


   import( 'core.model' );

   class DBUserOrders extends Model {

      static $table = 'historie_ordre';

      static $fields = array(

         'id' => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
         ),
         'ordrenr'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'alias' => 'ordernum'
         ),
         'uid'          => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false
         ),
         'tidspunkt'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'pris'    => array(
            'type'      => DB_TYPE_FLOAT,
            'default'   => 0.0
         ),
         'medarbeidar'    => array(
            'type'      => DB_TYPE_INTEGER
         ),
         'kommentar'    => array(
            'type'      => DB_TYPE_STRING,
            'size' => 65536
         ),
         'kampanje_kode'    => array(
            'type'      => DB_TYPE_STRING,
            'size' => 255
         ),
         'to_production'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'deleted'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'customerlock'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'exported'    => array(
            'type'      => DB_TYPE_STRING,
            'size' => 255
         ),
         'serverlock'    => array(
            'type'      => DB_TYPE_STRING,
            'size' => 255,
             'null'         => true,   
             'default'      => null
         ),
         'faktura'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size' => 11,
            'null'         => true,   
            'default'      => null
         ),
         'valutacode'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size' => 11
         ),
         'download_began_at'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'download_ended_at'    => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'source'       => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65536, 
            'default'   => '',
         ),
         'cewe_export'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'logingroup'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'trackingcode' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'klarnaid' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65536,
            'default'   => '',
         ),
      );

      public function __setup() {

         $return = parent::__setup();
         
         if( class_exists('Dispatcher') ) {
            $this->logingroup = Dispatcher::getLoginGroup();
            $this->trackingcode = Dispatcher::getTrackingCode();
         }
         
         return $return;

      }
      
      public function getOrderId() {

         return $this->id;

      }

      public function getUserId() {

         return $this->uid;

      }

      public function getComment() {

         return $this->kommentar;

      }

      public function setComment( $comment ) {

         return $this->kommentar = $comment;

      }

      public function getPrice() {

         return $this->pris;

      }


      public function setPrice( $price ) {

         return $this->pris = $price;

      }

      public function getDate() {

         return $this->tidspunkt;

      }
      
   }

?>