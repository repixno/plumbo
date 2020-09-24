<?php
   /**
    * @Tor Inge
    *
    *
    */
   import( 'core.model' );
   
   class DBmediaclipSession extends Model {

      static $table = 'session';
      static $basename = 'mediaclip';

      static $fields = array(
         'id' => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'sessionid' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'userid' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
         ),
         'browser' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'default'   => '',
         ),
         'logintime' => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'loggedin' => array(
            'type'      => DB_TYPE_BOOLEAN,
            'default'   => true,
         ),
         'efcustomer' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'default'   => '',
         )
      );
      
     public function __setup() {
         
         parent::__setup();
         $this->logintime = date( 'Y-m-d H:i:s' );
         
      }

   }

?>