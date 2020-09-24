<?php
   /**
    * @author Tor Inge Løvland
    *
    *
    */
 

     /******************
    CREATE TABLE cewe_uploads (
          bid integer PRIMARY KEY,
          aid integer,
          uid integer,
          ceweimageid text,
          cewealbumid text,
          ceweuserid text,
          created timestamp with time zone,
          started timestamp with time zone,
          finished timestamp with time zone
        );
    *******************/
 
   import( 'core.model' );

   class DBceweUploads extends Model {

        static $table = 'uploads';
        static $basename = 'cewe';
        static $fields = array(

          'bid'  => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'default'   => 0,
          ),
          'aid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'default'   => 0,
          ),
          'uid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'default'   => 0,
          ),
          'ceweimageid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
          'cewealbumid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
          'ceweuserid'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'default'   => '',
          ),
          'created'      => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null
          ),
          'started'      => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null
          ),
          'finished'      => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null
          ),
        
      );
   }

?>