<?PHP
/*CREATE TABLE reedfoto_project(
      id serial NOT NULL,
      projectname character varying,
      school character varying,
      imported timestamp without time zone
  
);*/
 
import( 'core.model' );
    
class DBReedfotoProject extends Model {
    
    static $table = 'reedfoto_project';
    
    static $fields = array(
        'id' => array(
           'primary' => true,
           'type'    => DB_TYPE_INTEGER,
           'size'    => 11,
           'default' => 0,
        ),
        'projectname' => array(
           'type'    => DB_TYPE_STRING,
           'size'    => 255,
           'default' => '',
        ),
        'school' => array(
           'type'    => DB_TYPE_STRING,
           'size'    => 255,
           'default' => '',
        ),
        'imported' => array(
           'type'    => DB_TYPE_DATETIME,
           'null'    => true,
           'default' => null,
        ),
       
    );
      
}



?>