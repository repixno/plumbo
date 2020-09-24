<?php

   model( 'user.usergroup' );
   model( 'user.userfriend' );

   class UserGroup extends DBUserGroup {

      /**
       * Get groups
       *
       * @return Array groups
       */
      
      public function getGroups( ) {
         
         $groups = array();
         
         foreach ( DB::query( "SELECT groupid FROM user_group WHERE userid = ?", Login::userid() )->fetchAll() as $group ) {

            $groupObj = new DBUserGroup( $group[ 0 ] );
            $groups[] = $groupObj->asArray();
            
         }
         
         return $groups;
         
      }
      
      /**
       * Add member to group
       *
       * @param Integer $userid
       * @return Boolean success true or false
       */
      
      public function addMember( $friendid ) {
         
         try {
            
            return DB::query( "insert into user_group_members (friendid, groupid, created) values (?, ?, ?)", $friendid, $this->groupid, date( 'Y-m-d H:i:s' ) );
           
         } catch ( Exception $e ) {
           
            return false;
         
         }
         
      }

      /**
       * Remove member from group
       *
       * @param Integer $userid
       * @return Boolean success true or false
       */
      
      public function removeMember( $friendid ) {
         try {
            
            return DB::query( "delete from user_group_members where friendid=? and groupid=?", $friendid, $this->groupid );
           
         } catch ( Exception $e ) {
           
            return false;
         
         }
      }

      /**
       * Get members from group
       *
       * @return Array array of member objects
       */
       
      public function getMembers() {
         
         $members = array();
         
         foreach ( DB::query( "SELECT friendid FROM user_group_members WHERE groupid = ?", $this->groupid )->fetchAll() as $member ) {

            $members[] = new UserFriend( $member[ 0 ] );

         }
         
         return $members;
         
      }
      
      public function asArray() {
         
      }
      
   }
   
?>