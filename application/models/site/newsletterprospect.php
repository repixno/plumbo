<?php

   import( 'core.model' );
   /*
   CREATE TABLE site_newsletterprospect (
        id serial NOT NULL,
        email text,
        portal text,
        registered timestamp without time zone,
        sent timestamp without time zone
    );*/
   
   
   class DBNewsletterprospect extends Model {
      
      static $table = 'newsletterprospect';
      
      static $basename = 'site';
      
      static $fields = array(
         'id' => array(
            'type'   => DB_TYPE_INTEGER,
            'size'   => 11,
            'default'   => 0,
            'primary' => true,
         ),
         'email' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'portal' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'registered'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'sent'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
   }
   
?>