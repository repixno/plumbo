<?PHP
/*CREATE TABLE project_images(
      id serial NOT NULL,
      project_id integer,
      filename character varying,
      bid integer,
      imported timestamp without time zone,
      cameraid character varying,
      splitname character varying,
      exifdate timestamp without time zone,
      timediff float,
      splittime timestamp without time zone
  
);*/
 
   import( 'core.model' );

   class DBProjectImages extends Model {

      static $basename = 'project';
      static $table = 'images';
      
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'project_id' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'filename' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'bid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'imported' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'cameraid' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0,
         ),
         'splitname' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0,
         ),
         'exifdate' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'timediff' => array(
            'type'    => DB_TYPE_FLOAT,
            'null'    => true,
            'default' => null,
         ),
         'splittime' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0,
         ),
         
      );
      
   }



?>