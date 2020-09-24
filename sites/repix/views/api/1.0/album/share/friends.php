<?php

   /**
    * Share a album to friends
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    * @todo Replace direct queries with models/component methods
    */

   import( 'pages.json' );
   import( 'mail.send' );
   import( 'website.user' );
   import( 'website.album' );

   class APIAlbumShareToFriends extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'friends' => VALIDATE_STRING,
                  'groups' => VALIDATE_STRING,
               ),
            ),
            'enable' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'userid' => VALIDATE_INTEGER,
                  'groupid' => VALIDATE_INTEGER,
                  'message' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               )
            ),
            'disable' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'userid' => VALIDATE_INTEGER,
                  'groupid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
               )
            ),
            'disableall' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER
               ),
               'fields' => array(
                  VALIDATE_INTEGER
               )
            )
         );

      }

 
      /**
       * Enable album sharing (to friends or groups)
       * 
       * @api-name album.share.friends
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-post-optional friends string Commaseparated list of friends
       * @api-param-optional friends String Commaseparated list of friends
       * @api-post-optional groups String Commaseparated list of groups
       * @api-param-optional groups String Commaseparated list of groups
       * @api-result-optional newfriends String Commaseparated list of friends
       * @api-result-optional oldfriends String commaseparated list of friends
       * @api-result-optional addfriends String commaseparated list of friends
       * @api-result-optional delfriends String commaseparated list of friends
       * @api-result-optional newgroups String Commaseparated list of groups
       * @api-result-optional oldgroups String commaseparated list of groups
       * @api-result-optional addgroups String commaseparated list of groups
       * @api-result-optional delgroups String commaseparated list of groups
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */       
      public function Execute() {

         $id = (int) $_POST['albumid'];

         $friends = isset( $_POST['friends'] ) && strlen( trim( $_POST['friends'] ) ) > 0 ? explode( ',', $_POST['friends'] ) : array();
         $groups = isset( $_POST['groups'] ) && strlen( trim( $_POST['groups'] ) ) > 0 ? explode( ',', $_POST['groups'] ) : array();

         if( $_POST['friends'] != '-' ) {

            $existingfriends = array();
            foreach( DB::query( 'SELECT uid FROM tilgangtilalbum_dedikert WHERE aid = ? AND groupid=0', $id )->fetchAll() as $row ) {
               list( $existingfriends[] ) = $row;
            }

            $addfriends = array_diff( $friends, $existingfriends );
            $delfriends = array_diff( $existingfriends, $friends );

            foreach( $addfriends as $friendid ) {
               $this->Enable( $id, $friendid );
            }

            foreach( $delfriends as $friendid ) {
               $this->Disable( $id, $friendid );
            }

            $this->newfriends = implode( ',', $friends );
            $this->oldfriends = implode( ',', $existingfriends );

            $this->addfriends = implode( ',', $addfriends );
            $this->delfriends = implode( ',', $delfriends );

         }

         if( $_POST['groups'] != '-' ) {

            $existinggroups = array();
            foreach( DB::query( 'SELECT gruppid FROM grupp_tilgangtilalbum_dedikert WHERE aid = ?', $id )->fetchAll() as $row ) {
               list( $existinggroups[] ) = $row;
            }

            $addgroups = array_diff( $groups, $existinggroups );
            $delgroups = array_diff( $existinggroups, $groups );

            foreach( $addgroups as $groupid ) {
               $this->Enable( $id, 0, $groupid );
            }

            foreach( $delgroups as $groupid ) {
               $this->Disable( $id, 0, $groupid );
            }

            $this->newgroups = implode( ',', $groups );
            $this->oldgroups = implode( ',', $existinggroups );

            $this->addgroups = implode( ',', $addgroups );
            $this->delgroups = implode( ',', $delgroups );

         }

         $this->albumid = $id;

         $this->result = true;
         $this->message = 'OK';

      }


      /**
       * Enable album sharing (to friend or group)
       * 
       * @api-name album.share.friends.enable
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-post-optional userid Integer The id of the user to share the album with
       * @api-param-optional userid Integer The id of the user to share the album with
       * @api-post-optional groupid Integer The id of the group to share the album with
       * @api-param-optional groupid Integer The id of the group to share the album with
       * @api-post-optional message Integer The message to send to the user
       * @api-param-optional message Integer The message to send to the user
       * @api-result albumid Integer The given album id
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
            
      public function Enable( $id = 0, $uid = 0, $gid = 0, $message = '' ) {

         if( empty( $id ) ) {
            $id = (int) $_POST['albumid'];
         }

         if( empty( $uid ) ) {
            $uid = (int) $_POST['userid'];
         }

         if( empty( $gid ) ) {
            $gid = (int) $_POST['groupid'];
         }

         if( empty( $message ) ) {
            $message = (string) $_POST['message'];
         }

         $this->result = false;
         $this->message = 'Required input parameter missing or invalid (albumid)';
         if( empty( $id ) ) return false;

         try {

            $album = new Album( $id );

         } catch( Exception $e ) {

            $this->result = false;
            $this->message = 'No such album or no access to this album';

            return false;

         }

         $this->result = false;
         $this->message = 'Failed to load album';
         if( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;

         $albumdata = array(
            'album' => $album->asArray(),
            'publiclink' => $album->getSharingURL(),
            'link' => $album->getAlbumURL(),
            'message' => $message,
         );

         $subject = __( '%s has shared an album with you', Login::data('fullname') );

         // is this album password protected?
         if( $album->password ) {

            $albumdata['password'] = $album->password;

         }

         if( $gid > 0 ) {

            if( !DB::query( 'SELECT COUNT(gruppid) FROM grupp_tilgangtilalbum_dedikert WHERE aid = ? AND gruppid = ?', $id, $gid )->fetchSingle() > 0 ) {

               DB::query( 'INSERT INTO grupp_tilgangtilalbum_dedikert (gruppid, aid, div_info) VALUES (?,?,NOW())', $gid, $id );

               foreach( DB::query( 'SELECT friend FROM grouplist WHERE groupid = ?', $gid )->fetchAll() as $row ) {

                  list( $uid ) = $row;

                  if( $uid > 0 && $uid != Login::userid() ) try {

                     $user = new DBUser( $uid );
                     if( $user instanceof DBUser ) {
                        
                        DB::query( 'INSERT INTO tilgangtilalbum_dedikert (uid, aid, groupid) VALUES (?,?,?)', $uid, $id, $gid );
                        
                        try {
                           $isuser = new User( $uid );
                           MailSend::Simple( $user->email, $subject, 'share.to-group', $albumdata );
                        } catch( Exception $e ) {
                           MailSend::Simple( $user->email, $subject, 'share.to-new-user', $albumdata );
                        }
                        
                     }
                     
                  } catch( Exception $e ) {}

               }

               $album->save();
               $this->result = true;
               $this->message = 'OK';

               return true;

            } else {

               $this->result = false;
               $this->message = 'The group already has permission to this album!';

               return false;

            }

         } else {

            try {

               $user = false;

               if( $uid > 0  && $uid != Login::userid() ) {
                  $user = new DBUser( $uid );
               }

               if( $user instanceof DBUser ) {

                  if( !DB::query( 'SELECT COUNT(aid) FROM tilgangtilalbum_dedikert WHERE aid = ? AND uid = ? AND groupid=0', $id, $uid )->fetchSingle() > 0 ) {

                     DB::query( 'INSERT INTO tilgangtilalbum_dedikert (aid, uid) VALUES (?,?)', $id, $uid );
                     
                     try {
                        $isuser = new User( $uid );
                        MailSend::Simple( $user->email, $subject, 'share.to-friend', $albumdata );
                     } catch( Exception $e ) {
                        MailSend::Simple( $user->email, $subject, 'share.to-new-user', $albumdata );
                     }
                     
                     $album->save();
                     $this->result = true;
                     $this->message = 'OK';

                     return true;

                  } else {

                     $this->result = false;
                     $this->message = 'The user already has permission to this album!';

                     return true;

                  }

               }

               throw new Exception( 'User not found!' );

            } catch( Exception $e ) {

               $this->result = false;
               $this->message = 'User not found!';

               return false;

            }

         }

      }
      
      /**
       * Disable album sharing (to friend or group)
       * 
       * @api-name album.share.friends.disable
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-post-optional userid Integer The id of the user to share the album with
       * @api-param-optional userid Integer The id of the user to share the album with
       * @api-post-optional groupid Integer The id of the group to share the album with
       * @api-param-optional groupid Integer The id of the group to share the album with
       * @api-result albumid Integer The given album id
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 

      public function Disable( $id = 0, $uid = 0, $gid = 0 ) {

         if( empty( $id ) ) {
            $id = (int) $_POST['albumid'];
         }

         if( empty( $uid ) ) {
            $uid = (int) $_POST['userid'];
         }

         if( empty( $gid ) ) {
            $gid = (int) $_POST['groupid'];
         }

         $this->result = false;
         $this->message = 'Required input parameter missing or invalid (albumid)';
         if( empty( $id ) ) return false;

         try {

            $album = new Album( $id );

         } catch( Exception $e ) {

            $this->result = false;
            $this->message = 'No such album or no access to this album';

            return false;

         }

         $this->result = false;
         $this->message = 'Failed to load album';
         if( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;

         if( $gid > 0 ) {

            if( DB::query( 'SELECT COUNT(gruppid) FROM grupp_tilgangtilalbum_dedikert WHERE aid = ? AND gruppid = ?', $id, $gid )->fetchSingle() > 0 ) {

               DB::query( 'DELETE FROM grupp_tilgangtilalbum_dedikert WHERE gruppid = ? AND aid = ?', $gid, $id );

               foreach( DB::query( 'SELECT friend FROM grouplist WHERE groupid = ?', $gid )->fetchAll() as $row ) {

                  list( $uid ) = $row;

                  if ( $uid > 0 && $uid != Login::userid() ) try {

                     DB::query( 'DELETE FROM tilgangtilalbum_dedikert WHERE aid = ? AND uid = ? AND groupid = ?', $id, $uid, $gid );

                  } catch( Exception $e ) {}

               }

               $album->reloadPermissions();
               $album->save();
               $this->result = true;
               $this->message = 'OK';

               return true;

            } else {

               $this->result = false;
               $this->message = 'The group has no permission to this album!';

               return false;

            }

         } else {

            try {

               $user = false;
               if( $uid > 0 ) $user = new DBUser( $uid );
               if( $user instanceof DBUser ) {

                  if( DB::query( 'SELECT COUNT(aid) FROM tilgangtilalbum_dedikert WHERE aid = ? AND uid = ? AND groupid=0', $id, $uid )->fetchSingle() > 0 ) {

                     DB::query( 'DELETE FROM tilgangtilalbum_dedikert WHERE aid = ? AND uid = ? AND groupid=0', $id, $uid );

                     $album->reloadPermissions();
                     $album->save();
                     $this->result = true;
                     $this->message = 'OK';

                     return true;

                  } else {

                     $this->result = false;
                     $this->message = 'The user has no permission to this album!';

                     return true;

                  }

               }

               throw new Exception( 'User not found!' );

            } catch( Exception $e ) {

               $this->result = false;
               $this->message = 'User not found!';

               return false;

            }

         }

      }

      /**
       * Disable all album sharing (to friends)
       * 
       * @api-name album.share.friends.disableall
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */       
      
      public function disableAll( $id = 0 ) {

         if ( empty( $id ) ) {

            $id = (int) $_POST['albumid'];

         }

         $this->result = false;
         $this->message = 'No album id given';
         if ( empty( $id ) ) return false;

         try {

            $album = new Album( $id );

         } catch ( Exception $e ) {

            $this->result = false;
            $this->message = 'No such album or no access to this album';

            return false;

         }

         $this->result = false;
         $this->message = 'Failed to load album';
         if ( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;

         try {

            if ( DB::query( 'SELECT COUNT(gruppid) FROM grupp_tilgangtilalbum_dedikert WHERE aid=?', $id )->fetchSingle() > 0 ) {

               DB::query( 'DELETE FROM grupp_tilgangtilalbum_dedikert WHERE aid = ?', $id );

            }

            if ( DB::query( 'SELECT COUNT(aid) FROM tilgangtilalbum_dedikert WHERE aid = ?', $id )->fetchSingle() > 0 ) {

               DB::query( 'DELETE FROM tilgangtilalbum_dedikert WHERE aid = ?', $id );

            }

            $album->save();

            $this->result = true;
            $this->message = 'OK';

            return true;

         } catch( Exception $e ) {

            $this->result = false;
            $this->message = 'Failed to remove shares!';

            return false;

         }

      }

   }

?>
