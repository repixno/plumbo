<?php
   /**
    * @author Tor Inge Løvland
    *
    *   CREATE TABLE cewe_image(
          bid serial PRIMARY KEY,
          ceweid varchar(256)
        );
    *
    */
   
   import( 'core.model' );

   class DBceweImage extends Model {

          static $table = 'image';
          static $basename = 'cewe';
          static $fields = array(

          'bid' => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'default'   => 0,
          ),
          'ceweid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
      );
   }

?>