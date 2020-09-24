<?php


   /**
    * Model for mediaclip_orders
    *
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'core.model' );

   class DBUserProjectOrder extends Model {

      static $table = 'orders';
      static $basename = 'mediaclip';

      static $fields = array(
         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'user_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'order_id'  => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'project_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'product_id'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'article_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'quantity' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'project_title' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'project_xml'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 16581375,
            'default'   => '',
         ),
         'cancelled' => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'processed'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'xtra' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'order_time'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'color' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'keyhole' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'sheetcount' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'order_row_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null' => true,
            'default'   => null,
         ),
         'production_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'used_images'=> array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'redeye' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'no_qenhancer'  => array(
            'type'      => DB_TYPE_BOOLEAN,
            'null'      => true,
            'default'   => false,
         ),

      );



      public function getTitle() {

         return $this->project_title;

      }

      public function setTitle( $title ) {

         $this->project_title = $title;

      }

      public function getXml() {

         return $this->fieldGet( 'project_xml' );

      }


      public function setXml( $xml ) {

         $this->fieldSet( 'project_xml', $xml );

      }

   }


?>