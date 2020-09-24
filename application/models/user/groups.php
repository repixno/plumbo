<?php
   /**
   * @author Svein Arild Bergset <sab@interweb.no>
   *
   *
   */

   import( 'core.model' );

   class DBUserGroups extends Model {

      static $table = 'groups';

      static $fields = array(

         'groupid'       => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default' => 0
         ),
         'uid'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
         ),
         'group_name'=> array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'div_info'  => array(
            'type'      => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null
         ),

      );

      public function __postSetup() {

         $this->div_info = date( 'Y-m-d H:i:s' );

         return parent::__postSetup();

      }

      public function save() {

         if ( !$this->groupid ) {

            $this->groupid = DB::query( "SELECT nextval('group_seq')" )->fetchSingle();

         }

         return parent::save();

      }

      public function getId() {

         return $this->groupid;

      }

      public function getUserId() {

         return $this->uid;

      }

      public function setUserId( $id ) {

         return $this->uid = (int) $id;

      }

      public function getName() {

         return $this->group_name;

      }

      public function setName( $name ) {

         return $this->group_name = $name ;

      }

   }

?>