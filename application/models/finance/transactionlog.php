<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );
   
   class DBFinanceTransactionLog extends Model {
      
      static $table = 'finance_bbs_transaction_log';
      
      static $fields = array(
         'objectid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null' => false,
         ),
         'transactionid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         ),
         'operation' => array(
            'type' => DB_TYPE_STRING,
            'size' => 7,
            'null' => false,
         ),
         'amount' => array(
            'type' => DB_TYPE_FLOAT,
            'size' => 11,
            'null' => false,
         ),
         'responsesource' => array(
            'type' => DB_TYPE_STRING,
            'size' => 2,
            'null' => true,
         ),
         'responsecode' => array(
            'type' => DB_TYPE_STRING,
            'size' => 2,
            'null' => true,
         ),
         'responsetext' => array(
            'type' => DB_TYPE_STRING,
            'size' => 255,
            'null' => true,
         )
      );
      
   }


?>