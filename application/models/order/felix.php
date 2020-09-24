<?php

   /**
    * DB model for order_felix
    * 
    * @author Tor Inge Lovland
    *
    */

    /**
   CREATE TABLE order_felix (
        id serial,
        orderid integer,
        productid integer,
        email text,
        newsletterjp boolean,
        newsletterfelix boolean,
        created timestamp with time zone
    );
   *******/
   
   import( 'core.model' );

   class DBFelix extends Model {
      
        static $table = 'order_felix';
      
        static $fields = array(
          'id'            => array(
                'primary'   => true,
                'type'      => DB_TYPE_INTEGER,
                'size'      => 11,
                'default'   => 0,
           ),
           'orderid'      => array(
                'type'         => DB_TYPE_INTEGER,
                'size'         => 11,
                'null'         => true,
                'default'      => null,
           ),
           'productid'      => array(
                'type'         => DB_TYPE_INTEGER,
                'size'         => 11,
                'null'         => true,
                'default'      => null,
           ),
           'created'      => array(
                'type'      => DB_TYPE_DATETIME,
                'null'      => true,
                'default'   => null
           ),
           'processed'      => array(
                'type'      => DB_TYPE_DATETIME,
                'null'      => true,
                'default'   => null
           ),
        );
      
   }


?>