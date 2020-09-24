<?php

   /**
    * Class for a single order row
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class DBEcwidOrderRow extends Model {
      
      static $table = 'historie_ordrelinje';
      
      static $fields = array(
         'id'           => array(
            'primary'      => true,
            'type'         => DB_TYPE_INTEGER,
            'size'         => 11,
            'null'         => false,
         ),
         'ordrenr'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'orderid',
         ),
         'artikkelnr'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'artnr',
         ),
         'antall'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'quantity',
         ),
         'pris'      => array(
            'type'      => DB_TYPE_FLOAT,
            'default'   => 0.0,
            'alias'     => 'price',
         ),
         'tekst'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65536,
            'null'      => true,
            'default'   => null,
            'alias'     => 'text',
         ),
         'malid'     => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
            'alias'     => 'templateid',
         ),
         'ordreskjemaid'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
            'alias'     => 'ordersheetid',
         ),
         'export'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'product_id'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 64,
            'null'      => true,
            'default'   => null,
            'alias'     => 'productid',
         ),
         'attributes'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65536,
            'null'      => true,
            'default'   => null,
         ),
      );
      
      
      public function setText( $text ) {
         
         $this->tekst = $text;
         
      }
      
      
      public function getText() {
         
         return $this->tekst;
         
      }
      
   }


?>