<?php
   /**
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    *
    *
    */


   import( 'core.model' );


   class DBUserProjectPredefined extends Model {

      static $table = 'predefined_projects';
      static $basename = 'mediaclip';

      static $fields = array(

         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'projectid'   => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0
         ),
         'aid'          => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'status'       => array(
            'type'      => DB_TYPE_INTEGER,
            'null'      => 11,
            'default'   => 0
         ),
         'projecthash'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         )

      );


   }

?>