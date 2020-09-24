<?php

   /**
    * Share an album
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    *
    */

   import( 'website.album' );
   import( 'website.image' );
   model( 'user.groups' );

   class MyAccountAlbumShare extends UserPage implements IView {

      protected $template = 'myaccount.album.share.index';

      public function Execute( $aid = 0 ) {

         $album = new Album( $aid );
         $user = new User( login::userid() );

         $this->album = $album->asArray();
         $this->shareurl = $album->getSharingURL();
         $this->galleryurl = $album->getGalleryURL();
         $friends = $user->listFriends();

         $shared = array();
         foreach( DB::query( 'SELECT uid FROM tilgangtilalbum_dedikert WHERE aid = ? AND groupid=0', $aid )->fetchAll() as $row ) {
            $shared[array_shift($row)] = true;
         }

         $sharedto['friends'] = array_keys( $shared );

         foreach( $friends as $key => $friend ) {
            $friends[$key]['shared'] = isset( $shared[$friend['id']] );
         }

         $this->friends = $friends;

         $groups = new Group();

         $shared = array();
         foreach( DB::query( 'SELECT gruppid FROM grupp_tilgangtilalbum_dedikert WHERE aid = ?', $aid )->fetchAll() as $row ) {
            $shared[array_shift($row)] = true;
         }

         $sharedto['groups'] = array_keys( $shared );

         $groupList = array();
         foreach( $groups->collection( array( 'groupid' ), array( 'uid' => Login::userid() ) )->fetchAll() as $groupIdent ) {

            $group = new Group( $groupIdent[ 0 ] );
            $groupList[] = array(
               'id' => $group->id,
               'name' => $group->name,
            );

         }

         $groupMembers = array();
         foreach ( $groupList as $grid ) {

            $groupMembers[] = array(
               'id' => $grid[ 'id' ],
               'name' => $grid[ 'name' ],
               'shared' => isset( $shared[$grid['id']] ),
               'members' => $user->listFriends( $grid[ 'id' ] )
               );

         }

         $this->groups = $groupMembers;
         $this->sharedto = $sharedto;

      }

   }

?>