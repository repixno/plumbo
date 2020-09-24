<?php

   include "../../bootstrap.php";
   config( 'website.config' );
   
   model( 'user.customer' );

   import( 'website.group' );
   import( 'website.userfriend' );
   import( 'website.usergroup' );

   $groups = array();
   
   foreach ( DB::query( 'select uid from brukar where deleted is NULL' )->fetchAll() as $friendrow ) {
      
      $uid = $friendrow[0];
      
      $friendgroups = array();
      
      foreach ( DB::query( 'select friend, groupid from grouplist where uid=?', $uid)->fetchAll() as $grouprow ) {
         
         $friendid = $grouprow[0];
         $groupid = $grouprow[1];
         
         $friendgroups[$friendid] = $groupid;
         
      }

      foreach( listFriends( $uid ) as $friend ) {
         
         if ( trim( $friend['username'] ) != '' ) {
            
            echo sprintf( "userid: %s email: %s name: %s sourceid: %s sourcetype: %s\n", $uid, $friend['username'], $friend['name'], $friend['id'], 'oldfriendlist' );
   
            $userfriend = new UserFriend();
            $userfriend->userid = $uid;
            $userfriend->email = $friend['username'];
            $userfriend->name = $friend['name'];
            $userfriend->sourceid = $friend['id'];
            $userfriend->sourcetype = 'oldfriendlist';
            
            if (trim($friend['cellphone']) != "") $userfriend->cellphone = $friend['cellphone'];
            
            $userfriend->created = $friend['created'];
            $userfriend->save();
            
            DB::query( 'update tilgangtilalbum_dedikert set friendid=? where uid=?', $userfriend->friendid, $friend['id']);
            
            if ( $friendgroups[ $friend[ 'id' ] ] ) {
               
               $groupid = $friendgroups[$friend['id']];
               
               list( $groupname, $created ) = DB::query( 'select group_name,div_info from groups where uid=? and groupid=? and group_name is not NULL', $uid, $groupid )->fetchRow();
               
               if ( !$groups[$groupid] && ( $groupname )) {
                  
                  $group = new UserGroup();
                  $group->name = $groupname;
                  $group->userid = $uid;
                  $group->sourcetype = 'oldgroups';
                  $group->sourceid = $groupid;
                  
                  if ($created != "") {
                     $group->created = $created;
                  }
                  
                  $group->save();
                  
                  $group->addMember( $userfriend->friendid );
                  
                  $groups[$groupid] = $group;
                  
               } else {
                  
                  if ($groups[$groupid] instanceof UserGroup) $groups[$groupid]->addMember( $userfriend->friendid );
                  
               }
            }
         }
         
      }
   }

   function listFriends( $uid, $group = null, $sort = 'ala' ) {

      // Params for query, as array.
      $queryParams = array(
         $uid
      );

      // Extra query data when group filtering is active.
      $groupFrom = $groupWhere = '';
      if ( isset( $group ) ) {

         $groupFrom = 'JOIN grouplist AS b ON a.friend=b.friend ';
         $groupWhere = 'AND b.uid=? AND b.groupid=?';
         array_push( $queryParams, $uid, $group );

      }

      // Define sort order.
      $sortOrder = 'a.fnamn ASC, a.enamn ASC';
      switch ( $sort ) {

         default:
            break;

      }

      // Build query.
      $queryString = "
         SELECT
            a.*
         FROM
            friendslist AS a
            $groupFrom
         WHERE
            a.uid=?
            $groupWhere
         ORDER BY
            $sortOrder
         ";

      // Do query.
      $ret = array();
      foreach ( DB::query( $queryString, $queryParams )->fetchAll( DB::FETCH_ASSOC ) as $friend ) {

         $user = null;

         // Set display name.
         $nameFirst = trim( sprintf( '%s %s', $friend[ 'fnamn' ], $friend[ 'mnamn' ] ) );
         $nameLast = $friend[ 'enamn' ];
         if ( empty( $nameFirst ) && empty( $nameLast ) ) {

            if ( !empty( $friend[ 'alias' ] ) ) {

               $nameFirst = $friend[ 'alias' ];
               $nameLast = '';

            } else {

               try {

                  $user = new User( $friend[ 'friend' ] );
                  if ( $user->isLoaded() && $user instanceof User ) {

                     $nameFirst = trim( sprintf( '%s %s', $user->firstname, $user->middlename ) );
                     $nameLast = $user->lastname;

                     // TODO? Maybe update fnamn/enamn in friendslist with this information.

                  }

               } catch ( Exception $e ) {}

            }

         }

         $username = $friend[ 'brukarnamn' ];
         if ( empty( $friend[ 'brukarnamn' ] ) ) {

            if ( !isset( $user ) ) {

               $user = new DBUser( $friend[ 'friend' ] );

            }

            if ( $user->isLoaded() && $user instanceof DBUser ) {

               $username = $user->username;

               // TODO? Maybe update brukarnamn in friendslist with this information.

            }

         }

         $fullName = trim( sprintf( '%s %s', $nameFirst, trim( $nameLast ) ) );
         $ret[] = array(
            'userid' => $friend[ 'uid' ],
            'id' => $friend[ 'friend' ],
            'cellphone' => $friend['mobilnr'],
            'username' => $username,
            'title' => $fullName,
            'namefirst' => $nameFirst,
            'namelast' => $nameLast,
            'name' => $fullName,
            'created' => $friend['div_info']
         );

      }

      return $ret;

   }

?>
