<?php

   /**
    * DB model for ukplan_order
    * 
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    *
    */

   import( 'core.model' );

   class DBUkeplanOrder extends Model {

      static $table = 'orders';
      static $basename = 'ukeplan';

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
         'orderid'  => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'articleid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'quantity' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'project_xml'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 16581375,
            'default'   => '',
         ),
         'used_images'=> array(
            'type'    => DB_TYPE_STRING,
            'size'    => 16777216,
            'default' => '',
         ),
         'date'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'order_row_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'processed'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),

      );

      public function getXml() {

         return $this->fieldGet( 'project_xml' );

      }


      public function setXml( $xml ) {

         $this->fieldSet( 'project_xml', $xml );

      }

   }


?>