<?php
   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    *
    */


   import( 'core.model' );


   class DBUserProjectUnique extends Model {

      static $table = 'unique';
      static $basename = 'mediaclip';

      static $fields = array(

         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'user_id'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0
         ),
         'project'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'created'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'host'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         )

      );
      
     public function __setup() {
         
         parent::__setup();
         $this->created = date( 'Y-m-d H:i:s' );
         
      }

      public function getUserId() {

         return $this->user_id;

      }

      public function setUserId( $userid ) {

         return $this->user_id = $userid;

      }

   }

?>