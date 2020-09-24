<?php

   import( 'core.model' );

   class DBDiscountPortal extends Model {
      
      static $table = 'discount_campaign_portals';
      
      static $fields = array(
         'id' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'null' => false,
         ),
         'discount_campaign_id' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
         ),
         'portal_id' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
         )
      );
      
      
   }


?>