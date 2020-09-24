<?php

    import( 'core.model' );
   
   
    class DBDelivery extends Model {
        
        static $table = 'delivery';
        static $basename = 'site';
      
        static $fields = array(
            'id' => array(
                'primary' => true,
                'type'    => DB_TYPE_INTEGER,
                'size'    => 11,
                'null' => false,
            ),
            'portalid' => array(
                'type'    => DB_TYPE_INTEGER,
                'size'    => 11,
                'default' => 0,
            ),
            'siteid' => array(
                'type'   => DB_TYPE_INTEGER,
                'size'   => 11,
                'default'   => 0,
            ),
            'deliverytype' => array(
                'type'   => DB_TYPE_INTEGER,
                'size'   => 11,
                'default'   => 0,
            ),
            'weight' => array(
                'type'   => DB_TYPE_INTEGER,
                'size'   => 11,
                'default'   => 0,
            ),
            'price'    => array(
                'type'    => DB_TYPE_FLOAT,
                'size'    => 11,
                'default' => 0.00,
            ),
            'active' => array(
                'type'     => DB_TYPE_BOOLEAN,
                'default'  => true,
            ),
            'paymentoptions' => array(
                
                'type' => DB_TYPE_STRING,
                'default' => ''
                
            )
        );
    }


?>