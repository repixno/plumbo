<?php

   import( 'core.model' );

   class DBUserFriend extends Model {

      static $table = 'user_friend';

      static $fields = array(

         'friendid'   => array(
            'type'       => DB_TYPE_INTEGER,
            'size'       => 11,
            'primary'    => true,
            'default'    => 0
         ),
         'userid'     => array(
            'type'       => DB_TYPE_INTEGER,
            'size'       => 11,
         ),
         'name'       => array(
            'type'       => DB_TYPE_STRING,
            'size'       => 255,
         ),
         'email'      => array(
            'type'       => DB_TYPE_STRING,
            'size'       => 255,
         ),
         'cellphone'  => array(
            'type'       => DB_TYPE_STRING,
            'size'       => 255,
         ),
         'created'    => array(
            'type'       => DB_TYPE_DATETIME,
            'null'       => true,
            'default'    => 'now'
         ),
         'updated'    => array(
            'type'       => DB_TYPE_DATETIME,
            'null'       => true,
            'default'    => null
         ),
         'sourcetype' => array(
            'type'       => DB_TYPE_STRING,
            'size'       => 255,
         ),
         'sourceid'   => array(
            'type'       => DB_TYPE_STRING,
            'size'       => 255,
         )
  
      );

      /**
       * Fetch object as array
       *
       * @return Array
       */
      
      public function asArray() {
         
         return array(
            'friendid'  => $this->friendid,
            'userid'    => $this->userid,
            'name'      => $this->name,
            'email'     => $this->email,
            'cellphone' => $this->cellphone,
            'created'   => $this->created,
            'updated'   => $this->updated,
            'sourcetype'=> $this->sourcetype,
            'sourceid'  => $this->sourceid
         );
         
      }
      
      /**
       * override save, sets updated date
       *
       */
      
      public function save() {

         $this->updated = date( 'Y-m-d H:i:s' );
         
         return parent::save();
            
      }
      
      /**
       * override delete, removes friend from groups
       *
       */
      
      public function delete() {
         
         DB::query( "DELETE FROM user_group_members WHERE friendid = ?", $this->friendid );
         
         return parent::delete();
      }
      
   }
?>