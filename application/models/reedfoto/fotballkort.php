<?PHP
 /*CREATE TABLE project_fotballkort(
      id serial NOT NULL,
      imagid character varying,
      filename character varying,
      name character varying,
      team character varying,
      mobile integer,
      imported timestamp without time zone,
      processed timestamp without time zone
  );*/
 
   import( 'core.model' );

   class fotballkortDB extends Model {

      static $basename = 'project';
      static $table = 'fotballkort';
      
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'imageid' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0,
         ),
         'filename' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'name' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0,                 
        ),
         'team' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => 0,                 
        ),
        'mobile' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'imported' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'processed' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
      );
      
   }



?>