<?php

   /**
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'core.model' );
   
   class DBOrderCallbackUrl extends Model {
      
      static $table = 'order_callback_urls';
      
      static $fields = array(
         'ordercallbackid' => array( 
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'id',
         ),
         'portalcode' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null, 
         ),
         'url' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null, 
         ),
      
      );
      
   }


?>