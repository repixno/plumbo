<?php

   /**
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   Import( 'core.model' );
   
   class DBAlbumIdentifier extends Model {
      
      static $table = 'album_identifiers';

      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
         ),
         'identifier' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'batchid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'project'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'mobile'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'activated'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'activatedby'=> array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'notified'  => array(
            'type'    => DB_TYPE_BOOLEAN,
            'null'    => true,
            'default' => null,
         ),
         'mmssent'  => array(
            'type'    => DB_TYPE_BOOLEAN,
            'null'    => true,
            'default' => null,
         ),         
      );
      
      public function __postSetup() {

         // setup some defaults for new objects
         if( !$this->created ) {
            $this->created = date( 'Y-m-d H:i:s' );
         }
         
         // run the parent setup
         return parent::__postSetup();

      }
      
   }

?>