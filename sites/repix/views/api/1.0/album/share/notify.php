<?php

   /**
    * Shares a single album to an email address
    *
    * @author Oyvind Selbek <oyvind@selbek.com>
    *
    */

   import( 'pages.json' );
   import( 'mail.send' );
   import( 'website.user' );
   import( 'website.album' );
   model( 'user.friends' );

   class APIAlbumShareNotification extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'userid' => VALIDATE_INTEGER,
                  'groupid' => VALIDATE_INTEGER,
                  'email' => VALIDATE_STRING,
                  'message' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               )
            )
         );

      }

      /**
       * Enable album sharing (by email)
       * 
       * @api-name album.share.notify
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-post-optional userid Integer The id of the user to share the album with
       * @api-param-optional userid Integer The id of the user to share the album with
       * @api-post-optional groupid Integer The id of the group to share the album with
       * @api-param-optional groupid Integer The id of the group to share the album with
       * @api-post-optional email Integer The email of the user to share the album with
       * @api-param-optional email Integer The email of the user to share the album with
       * @api-post-optional message Integer The message to send to the user
       * @api-param-optional message Integer The message to send to the user
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */     
      
      public function Execute( $id = 0, $uid = 0, $gid = 0, $email = '', $message = '' ) {

         if( empty( $id ) ) {
            $id = (int) $_POST['albumid'];
         }

         if( empty( $uid ) ) {
            $uid = (int) $_POST['userid'];
         }

         if( empty( $gid ) ) {
            $gid = (int) $_POST['groupid'];
         }

         if( empty( $email ) ) {
            $email = (string) $_POST['email'];
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
            'link' => $album->getSharingUrl(),
            'message' => $message,
         );

         $subject = __( '%s has shared an album with you', Login::data('fullname') );

         // is this album password protected?
         if( $album->password ) {

            $albumdata['password'] = $album->password;

         }

         if( $gid > 0 ) {

            if( DB::query( 'SELECT COUNT(gruppid) FROM grupp_tilgangtilalbum_dedikert WHERE aid = ? AND gruppid = ?', $id, $gid )->fetchSingle() > 0 ) {

               foreach( DB::query( 'SELECT friend FROM grouplist WHERE groupid = ?', $gid )->fetchAll() as $row ) {

                  list( $uid ) = $row;

                  if( $uid > 0 ) try {

                     if ( $useEmail = $this->retrieveEmail( $uid ) ) {

                        $albumdata['link'] = $album->getAlbumURL();
                        MailSend::Simple( $useEmail, $subject, 'share.to-group', $albumdata );

                     }

                  } catch( Exception $e ) {}

               }

               $this->result = true;
               $this->message = 'OK';

               return true;

            } else {

               $this->result = false;
               $this->message = __( 'The group does not have permission to this album!' );

               return false;

            }

         } else {

            try {

               $user = false;

               if( $uid > 0 ) {

                  if( DB::query( 'SELECT COUNT(uid) FROM tilgangtilalbum_dedikert WHERE aid=? AND uid=? AND groupid=0', $id, $uid )->fetchSingle() > 0 ) {

                     if ( $useEmail = $this->retrieveEmail( $uid ) ) {

                        $albumdata['link'] = $album->getAlbumURL();
                        MailSend::Simple( $useEmail, $subject, 'share.to-friend', $albumdata );

                        $this->result = true;
                        $this->message = 'OK';

                        return true;

                     }

                  } else {

                     $this->result = false;
                     $this->message = __( 'The user does not have permission to this album!' );

                     return false;

                  }


               } elseif( !empty( $email ) ) {

                  $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );

                  if( $user instanceof User ) {

                     MailSend::Simple( $user->email, $subject, 'share.to-existing-user', $albumdata );

                     $this->result = true;
                     $this->message = 'OK';

                     return true;

                  } elseif( !empty( $email ) ) {

                     MailSend::Simple( $email, $subject, 'share.to-new-user', $albumdata );

                     $this->result = true;
                     $this->message = 'OK';

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

      private function retrieveEmail( $friend ) {

         $email = null;

         $queryString = "
            SELECT
               brukarnamn,
               friend
            FROM
               friendslist
            WHERE
               uid=? AND
               friend=?
            ";
         if ( $res = DB::query( $queryString, Login::userid(), $friend )->fetchAll() ) {

            list( $email, $friendid ) = $res[0];

            if ( empty( $email ) ) {

               $user = new DBUser( $friendid );
               if ( $user->isLoaded() && $user instanceof DBUser ) {

                  $email = $user->email;

               }

            }

         }

         return $email;

      }

   }

?>