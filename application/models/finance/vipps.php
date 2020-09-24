<?php

   /**
    * 
    * @author Tor Inge
        CREATE TABLE vipps_transaction(
           id SERIAL PRIMARY KEY,
           vippsid VARCHAR NOT NULL,
           orderid INT,
           status VARCHAR,
           amount FLOAT,
           merchantid INT
        );
    * 
    */


   import( 'core.model' );

   class DBVippsTransaction extends Model {
      
      static $table = 'vipps_transaction';
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null' => false,
         ),
         'vippsid' => array(
            'type' => DB_TYPE_STRING,
            'size' => 255,
            'null' => false,
         ),
         'orderid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => true,
         ),
         'status' => array(
            'type' => DB_TYPE_STRING,
            'size' => 4,
            'null' => false,
         ),
         'amount' => array(
            'type' => DB_TYPE_FLOAT,
            'size' => 11,
            'null' => false,
         ),
         'merchantid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         )
      );
      
   }


?>