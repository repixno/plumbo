<?php

   /**
    *
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class DBSMSHistory extends Model {
      
      static $table = 'user_sms_services_history';
      
      static $fields = array(
         'id'  => array(
            'primary' => true,
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
          'uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
          'cellnr' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 20,
            'default'   => '',
         ),
         'service_type' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'created' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         
      );
      
   }


?>