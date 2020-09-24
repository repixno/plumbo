<?php
   /**
CREATE TABLE mediaclip_versions (
id serial,
project_id integer,
project_xml text,
date_saved timestamp
	)
 	*/

   import( 'core.model' );


   class DBUserProjectVersions extends Model {

      static $table = 'versions';
      static $basename = 'mediaclip';

      static $fields = array(

         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'project_id'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'project_xml'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 16581375,
            'default'   => '',
         ),
         'date_saved'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         )
      );
      
      
     public function __setup() {
         parent::__setup();
         $this->date_saved = date( 'Y-m-d H:i:s' );
      }
   }

?>