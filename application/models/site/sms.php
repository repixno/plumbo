<?php

   /**
    * model for site_sms
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   class DBSiteSMS extends Model {
      
      static $table = 'site_sms';
      
      static $fields = array(
         'id'  => array(
            'primary' => true,
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
         ),
         'message' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'default'   => '',
         ),
         'price' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
      );
      
   }


?>