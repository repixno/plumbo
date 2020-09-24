<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'core.model' );

   class DBFinanceTransaction extends Model {
      
      static $table = 'finance_bbs_transaction';
      
      static $fields = array(
         'objectid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null' => false,
         ),
         'orderid' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'null'   => false,
         ),
         'mode' => array(
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