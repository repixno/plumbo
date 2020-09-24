<?php

   import( 'core.model' );

   class DBUserGroup extends Model {

      static $table = 'user_group';

      static $fields = array(

         'groupid'    => array(
            'type'        => DB_TYPE_INTEGER,
            'size'        => 11,
            'primary'     => true,
            'default'     => 0
         ),
         'userid'     => array(
            'type'        => DB_TYPE_INTEGER,
            'size'        => 11,
         ),
         'name'       => array(
            'type'        => DB_TYPE_STRING,
            'size'        => 255,
         ),
         'created'    => array(
            'type'        => DB_TYPE_DATETIME,
            'null'        => true,
            'default'     => 'now'
         ),
         'updated'    => array(
            'type'        => DB_TYPE_DATETIME,
            'null'        => true,
            'default'     => null
         ),
         'sourcetype' => array(
            'type'        => DB_TYPE_STRING,
            'size'        => 255,
         ),
         'sourceid'   => array(
            'type'        => DB_TYPE_STRING,
            'size'        => 1024,
         )
  
      );
      
      /**
       * Fetch object as array
       *
       * @return unknown
       */
      
      public function asArray() {
         
         return array(
            'groupid'   => $this->groupid,
            'userid'    => $this->userid,
            'name'      => $this->name,
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
       * override delete, removes members
       */
      
      public function delete() {
         
         DB::query( "DELETE FROM user_group_members WHERE groupid = ?", $this->groupid );
         
         return parent::save();
      }
      
   }
?>