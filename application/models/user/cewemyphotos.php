<?php
   /**
    * @author Tor Inge Løvland
    *
    *
    */
   
   import( 'core.model' );

   class DBceweMyphotos extends Model {

          static $table = 'myphotos';
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
          'clid' => array(
               'type' => DB_TYPE_STRING,
               'size' => 255,
               'default' => ''
          ),
          'refreshtoken' => array(
               'type'    => DB_TYPE_STRING,
               'size'    => 255,
               'default' => ''
          ),
          'updated'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
          ),
        
      );
   }

?>