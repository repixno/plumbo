<?php

   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   model( 'user.groups' );

   class Group extends DBUserGroups {

      public function delete() {

         if ( $this->id ) {

            $user = new User();
            $queryString = "
               SELECT
                  uid
               FROM
                  grouplist
               WHERE
                  groupid=?
               ";
            foreach ( DB::query( $queryString, $this->id )->fetchAll() as $item ) {

               list( $uid ) = $item;
               $user->removeFriendFromGroup( $uid, $this->id );

            }

         }

         return parent::delete();

      }

      public function getMembers() {

         $ret = array();
         foreach ( DB::query( "SELECT friend FROM groups AS a JOIN grouplist AS b ON a.groupid=b.groupid WHERE a.uid=?", $this->userid )->fetchAll() as $item ) {

            $ret[] = new DBUserFriends( $item[ 'friend' ] );

         }

      }

      public function addMember( $item ) {

         //return DB::query( 'INSERT INTO grouplist (uid,friend,groupid) VALUES (?,?,?)', Login::userid(), $item, $this->groupid );

         $user = new User();
		   return $user->addFriendToGroup( $item, $this->groupid );

      }

      public function removeMember( $item, $groupid = null ) {

         if ( !isset( $groupid ) ) $groupid = $this->groupid;
         if ( empty( $groupid ) || !is_numeric( $groupid ) ) return false;

         $user = new User();
		   $user->removeFriendFromGroup( $item, $groupid );

      }

   }

?>
