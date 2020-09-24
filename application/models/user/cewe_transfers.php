<?php
   /**
    * @author Tor Inge Løvland
    *
      CREATE TABLE cewe_transfers(
          id serial PRIMARY KEY,
          ceweuserid text,
          userid integer,
          orderd timestamp with time zone,
          started timestamp with time zone,
          finished timestamp with time zone,
          verified timestamp with time zone
        );
        
        
    */
   
   import( 'core.model' );

   class DBceweTransfer extends Model {

          static $table = 'transfers';
          static $basename = 'cewe';
          static $fields = array(

          'id' => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'default'   => 0,
          ),
          'ceweuserid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
          'userid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
          'orderd'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
          ),
          'started'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
          ),
          'finished'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
          ),
          'verified'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
          ),
          'taskid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
        
        );
   }

?>