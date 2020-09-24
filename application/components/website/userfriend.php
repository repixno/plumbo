<?php

   model( 'user.userfriend' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'mail.send' );
   import( 'website.album' );
   import( 'math.zbase32' );

   class UserFriend extends DBUserFriend {
      
      /**
       * Get friends
       *
       * @return Array
       */
      
      public function getFriends( ) {
         
         $friends = array();
         
         foreach ( DB::query( "SELECT friendid FROM user_friend WHERE userid = ?", Login::userid() )->fetchAll() as $friend ) {

            $friendObj = new DBUserFriend( $friend[ 0 ] );
            $friends[] = $friendObj->asArray();
         }
         
         return $friends;
         
      }
      
       /**
       * Validate friend hash and return friendid
       *
       * @param Integer $albumid
       * @param String $friendhash
       * @param String $signature
       * @return Integer
       */
      
      public static function getFriendFromHash( $friendhash, $signature ) {
         
         $friendid = zBase32::decode( $friendhash );
      
         if ( zBase32::encode( crc32( $friendid ) + 0x100000000 ) == $signature ) {
            
            return new UserFriend( $friendid );
            
         } else {

            return false;
            
         }
         
      }
      
      /**
       * Share album to friend
       *
       * @param Integer $albumid
       * @return Boolean
       */
      
      public function shareAlbum( $albumid ) {
         
         try {
            
            $album = new Album( $albumid );
            
            Album::clearSharingStatus( $album->aid, $this->friendid );
            Album::setSharingStatus( $album->aid, $this->friendid );

            $friendhash = zbase32::encode( $this->friendid );
            $signature = zbase32::encode( crc32( $this->friendid ) + 0x100000000 );
            
            $maildata['friend'] = $this->asArray();
            $maildata['user'] = new User( Login::userid() );

            $maildata['albums'][0]['object'] = $album;
            $maildata['albums'][0]['link'] = sprintf( '%s/shared/album/friend/%s/%s/%s/', WebsiteHelper::rootBaseUrl(), $album->aid, $friendhash, $signature ); 

            MailSend::Simple( $maildata['friend']['email'], __('An album has been shared with you'), 'share.to-friend', $maildata );
            
            return true;
            
         } catch ( Exception $e ) {
            
            return false;
         }
            
      }
      
      /**
       * Share album to friends
       *
       * @param Array $albums
       * @return Boolean
       */
      
      public function shareAlbums( $albums ) {
         
         try {
            
            $count = 0;
            
            $friendhash = zbase32::encode( $this->friendid );
            $signature = zbase32::encode( crc32( $this->friendid ) + 0x100000000 );
            
            
            foreach( $albums as $albumid ) {
            
               $album = new Album( $albumid );
               
               Album::clearSharingStatus( $album->aid, $this->friendid );
               Album::setSharingStatus( $album->aid, $this->friendid );
               
               $maildata['albums'][$count]['object'] = $album;
               $maildata['albums'][$count]['link'] = sprintf( '%s/shared/album/friend/%s/%s/%s/', WebsiteHelper::rootBaseUrl(), $album->aid, $friendhash, $signature );
               
               $count++;
            
            }
            
            $maildata['friend'] = $this->asArray();
            $maildata['user'] = new User( Login::userid() );
            
            $maildata['count'] = $count;
            
            if ( $count > 1 ) {
               
               MailSend::Simple( $maildata['friend']['email'], __( '%s albums has been shared with you', $count ), 'share.to-friend', $maildata );
               
            } else {
               
               MailSend::Simple( $maildata['friend']['email'], __( 'An album has been shared with you' ), 'share.to-friend', $maildata );
               
            }
            
            return true;
            
         } catch ( Exception $e ) {
            
            die($e);
            return false;
         }
            
      }

      /**
       * Feturn friend as array
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
      
      
   }
   
?>