<?PHP

   model( 'user.customer' );
   import( 'website.project' );
   import( 'website.discounthistory' );
   import( 'website.album' );
   import( 'website.group' );

   class User extends DBCustomer {


      static function getUserFromMailtoken( $token ) {
         
         $userid = (int) DB::query( 'SELECT userid FROM site_user_mailtokens where token = ?', $token )->fetchSingle();

         return new User( $userid ); 

      } 

      static function UserIDfromUsernameAndPortal( $username, $portal = '' ) {
         
         // fail on empty username
         if( !$username ) return 0;
         
         // find the users logingroup
         if( class_exists('Dispatcher') ) {
            $logingroup = Dispatcher::getLoginGroup();
         } else {
            $logingroup = '';
         }
         
         // prepare the data for database query
         $username = str_replace( '%', '', trim( strtolower( $username ) ) );
         $username = str_replace( '_', '\\_', $username );
         
         // execute the query and retreive the userid
         if( !$logingroup ) {
            $logingroup = '';
            $userid = (int) DB::query( 'SELECT uid FROM brukar WHERE brukarnamn ILIKE ? AND (logingroup = ? OR logingroup IS NULL)', $username, $logingroup )->fetchSingle();
         } else {
            $userid = (int) DB::query( 'SELECT uid FROM brukar WHERE brukarnamn ILIKE ? AND logingroup = ?', $username, $logingroup )->fetchSingle();
         }
         
         // since EF 2.x doesn't invalidate the user cache after switching passwords
         User::deleteFromObjectCacheByClassAndId( 'User', $userid );

         // return the userid
         return $userid;

      }
      
      static function fromUsernameAndPassword( $username, $password, $portal = '' ) {

         try {

            // first, find the userid
            $userid = User::UserIDfromUsernameAndPortal( $username, $portal );

            // now, try to load the user
            $user = new User( $userid );

            // finally, validate the password
            if( !$userid || !User::validatePassword( $password, $user->password ) ) {
               return array(
                  'reason' => __( 'Incorrect username or password' ),
                  'result' => false
               );
            }

            return $user;

         } catch ( Exception $e ) {
            
            return array(
               'reason' => __( 'User not registered' ),
               'result' => false,
            );

         }

      }

      static $fullnamecache = array();

      static function getNameFromUid( $uid ) {

         // attempt to retreive and store the user data from cache
         if( isset( User::$fullnamecache[$uid] ) ) {

            return User::$fullnamecache[$uid];

         } else {

            // use fullname from session data for own content
            if( $uid > 0 && $uid == Login::userid() ) {
               return Login::data('fullname');
            }

            // attempt to retreive and store the user data from db
            try {
               $user = new User( $uid );
               return User::$fullnamecache[$uid] = $user->fullname;
            } catch ( Exception $e ) {
               return User::$fullnamecache[$uid] = __( 'Unknown' );
            }

         }

      }

      static function fromUsernameAndPortal( $username, $portal = '' ) {

         try {

            // first, find the userid
            $userid = User::UserIDfromUsernameAndPortal( $username, $portal );

            // make sure we found someone
            if( !$userid ) return false;
            
            // now, try to load the user
            $user = new User( $userid );

            return $user;

         } catch ( Exception $e ) {
            
            return false;
            
         }

      }


      static function validatePassword( $password, $crypthash ) {

         $password = trim( $password );
         
         switch( strlen( $crypthash ) ) {

            case 34:  # MD5 Password
               $split_pwd = explode("$", $crypthash );
               $my_salt = "\$1\$" . $split_pwd[2];
               break;
            case 98:  # MD5 Password
               $split_pwd = explode("$", $crypthash );
               $my_salt = "\$6\$" . $split_pwd[2];
               break;
            case 13: # Standard DES Password
               $my_salt = substr( $crypthash, 0, 2);
               break;

            default:
               $my_salt = "";
               throw new SecurityException( 'Password encryption mismatch.' );
               break;
         }

         $crypt_passwd = crypt( $password, $my_salt );

         return $crypt_passwd == $crypthash ? true : false;

      }


      public function getAvatarURL() {

         return sprintf( '%s/images/stream/thumbnail/%d',
            WebsiteHelper::rootBaseUrl(),
            $this->profile_picture
         );

      }

      public function setPreference( $key, $value ) {
         
         if( $key ) {
         
            DB::query( 'DELETE FROM site_user_preferences WHERE userid = ? AND key = ?' , $this->uid, $key );
            DB::query( 'INSERT INTO site_user_preferences (userid, key, value) VALUES (?, ?, ?)', $this->uid, $key, $value );
            CacheEngine::erase( sprintf( 'userpreferences_%s', $this->uid ) );
            
         }
         
      }

      public function getPreference( $key ) {

         $userpreferences = $this->getUserPreferences();

         CacheEngine::write( sprintf( 'userpreferences_%s', Login::userid() ), $userpreferences );

         return $userpreferences[ $key ];
            
      }

      public function getUserPreferences() {

         $userpreferences = array();

         foreach ( DB::query( 'select key, value from site_user_preferences where userid = ?', Login::userid() )->fetchAll() as $preference ) {

            $userpreferences[ $preference[ 0 ] ] = $preference[ 1 ];
            
         }

         return $userpreferences;

      }
      
      public function getFullsizeAvatarURL( $xres = 0, $yres = 0 ) {

         if( $xres > 0 && $yres > 0 ) {

            return sprintf('%s/images/stream/image/%d&dx=%d&dy=%d',
               WebsiteHelper::rootBaseUrl(),
               $this->profile_picture,
               $xres,
               $yres
            );

         } else {

            return sprintf('%s/images/stream/image/%d',
               WebsiteHelper::rootBaseUrl(),
               $this->profile_picture
            );

         }

      }

      public function asArray() {

         return array(
            "rubid"           => $this->rubid,
            "userid"          => $this->uid,
            "fullname"        => $this->getFullname(),
            "firstname"       => $this->getFirstName(),
            "middlename"      => $this->getMiddleName(),
            "lastname"        => $this->getLastName(),
            "address"         => $this->getStreetAddress(),
            "address2"         => $this->getStreetAddress2(),
            "zipcode"         => $this->zipcode,
            "city"            => $this->city,
            "country"         => $this->country,
            "mobile"          => $this->cellPhone,
            'email'           => $this->getEmailAddress(),
         );

      }
      
      public function getEmailAddress() {
         
         $email = $this->contactemail;
         if( !$email ) $email = $this->username;
         return $email;
         
      }
      
      public function listFriends( $group = null, $sort = 'ala' ) {

         // Params for query, as array.
         $queryParams = array(
            $this->uid
            );

         // Extra query data when group filtering is active.
         $groupFrom = $groupWhere = '';
         if ( isset( $group ) ) {

            $groupFrom = 'JOIN grouplist AS b ON a.friend=b.friend ';
            $groupWhere = 'AND b.uid=? AND b.groupid=?';
            array_push( $queryParams, $this->uid, $group );

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
            if( strlen( $username) != '' ){ 
            $ret[] = array(
               'userid' => $friend[ 'uid' ],
               'id' => $friend[ 'friend' ],
               'username' => $username,
               'title' => $fullName,
               'namefirst' => $nameFirst,
               'namelast' => $nameLast,
               'name' => $fullName
            );
            }

         }

         return $ret;

      }

      public function getFriend( $id ) {

         // Build query.
         $queryString = "
            SELECT
               *
            FROM
               friendslist
            WHERE
               uid=? AND
               friend=?
            ";
         if ( $ret = DB::query( $queryString, Login::userid(), $id )->fetchAll( DB::FETCH_ASSOC ) ) {

            list( $friendData ) = $ret;
            if ( empty( $friendData[ 'enamn' ] ) && empty( $friendData[ 'fnamn' ] ) ) {

               if ( !empty( $friendData[ 'alias' ] ) ) {

                  $friendData[ 'fnamn' ] = $friendData[ 'alias' ];
                  $friendData[ 'enamn' ] = '';

               } else {

                  try {

                     $user = new User( $id );
                     if ( $user->isLoaded() && $user instanceof User ) {

                        $friendData[ 'fnamn' ] = trim( sprintf( '%s %s', $user->firstname, $user->middlename ) );
                        $friendData[ 'enamn' ] = $user->lastname;

                        // TODO? Maybe update fnamn/enamn in friendslist with this information.

                     }

                  } catch ( Exception $e ) {}

               }

            }

            return $friendData;

         } else {

            return false;

         }

      }

      public function addFriend( $email, $firstname, $lastname ) {

         // Check for friend with existing email.
         $queryString = "
            SELECT
               friend
            FROM
               friendslist
            WHERE
               uid=? AND
               brukarnamn=?
            ";
         if ( count( DB::query( $queryString, Login::userid(), $email ) ) ) {

            return false;

         }

         // Check for existing user.
         $user = DBUser::fromUsername( $email );

         if ( !isset( $user ) ) {

            $user = new DBUser();
            $user->username = $email;
            $user->password = 'nopass';
            $user->created = 'now';
            $user->save();

         }

         if ( !isset( $user ) ) {

            return false;

         }

         // Add friend.
         $queryString = "
            INSERT INTO
               friendslist
               (uid, friend, brukarnamn, fnamn, enamn, div_info)
            VALUES
               (?, ?, ?, ?, ?, ?)
            ";
         DB::query( $queryString, Login::userid(), $user->userid, $user->username, $firstname, $lastname, date( 'Y-m-d H:i:s' ) );

      }

      public function removeFriend( $friend ) {

         // Delete friend's access to albums.
         $albums = new Album();

         foreach ( $albums->collection( array( 'aid' ), array( 'uid' => Login::userid(), 'deleted_at' => null ) )->fetchAll() as $albumrow ) {

            list( $albumid ) = $albumrow;
            try {

               $album = new Album( $albumid );
               //$album->removeUserAccess( $friend );

            } catch ( Exception $e ) {


            }

         }

         // Delete friend from any groups.
         $groups = new Group();

         foreach ( $groups->collection( array( 'groupid' ), array( 'uid' => Login::userid() ) )->fetchAll() as $grouprow ) {

            list( $groupid ) = $grouprow;
            try {

               $group = new Group( $groupid );
               $group->removeMember( $friend );

            } catch ( Exception $e ) {



            }


         }

         // Delete friend from list.
         $queryString = "
            DELETE FROM
               friendslist
            WHERE
               uid=? AND
               friend=?
            ";
         DB::query( $queryString, Login::userid(), $friend );

      }

      public function editFriend( $friend, $data ) {

         $user = new DBUser( $friend );

         if ( $user->isLoaded() && !$user->isCustomer() ) {

            if ( $user->isAvailableUsername( $data[ 'email' ], $friend ) ) {

               $user->username = $data[ 'email' ];
               $user->save();

            } else {

               return false;

            }

         } else if ( $user->isLoaded() && !$user->isAvailableUsername( $data[ 'email' ], $friend ) ) {

            return false;

         }

         $queryString = "
            UPDATE
               friendslist
            SET
               brukarnamn=?,
               fnamn=?,
               mnamn=?,
               enamn=?
            WHERE
               uid=? AND
               friend=?
            ";
         DB::query( $queryString, $data[ 'email' ], $data[ 'namefirst' ], '', $data[ 'namelast' ], Login::userid(), $friend );

         return true;

      }

      public function addFriendToGroup( $friend, $group, $autoAddAlbumShares = true ) {

         // Check if group connection already exists.
         $queryString = "
            SELECT
               COUNT(*)
            FROM
               grouplist
            WHERE
               uid=? AND
               friend=? AND
               groupid=?
            ";
         if ( DB::query( $queryString, Login::userid(), $friend, $group )->fetchSingle() > 0 ) {

            return false;

         }

         // Add permissions for friend to any photos with group access.
         if ( $autoAddAlbumShares ) {

            $queryString = "
               SELECT
                  aid
               FROM
                  grupp_tilgangtilalbum_dedikert
               WHERE
                  gruppid=?
               ";
            $sharedAlbums = array();
            foreach ( DB::query( $queryString, $group )->fetchAll() as $album ) {

               list( $albumid ) = $album;
               $sharedAlbums[] = $albumid;

            }

            foreach ( $sharedAlbums as $aid ) {

               $queryString = "
                  INSERT INTO
                     tilgangtilalbum_dedikert
                     (uid, aid, groupid)
                  VALUES
                     (?,?,?)
                  ";
               DB::query( $queryString, $friend, $aid, $group );

               $album = new Album( $aid );
               $album->reloadPermissions();

            }



         }

         $queryString = "
            INSERT INTO
               grouplist
               (uid,friend,groupid)
            VALUES
               (?,?,?)
            ";
         DB::query( $queryString, Login::userid(), $friend, $group );

         return true;

      }

      public function removeFriendFromGroup( $friend, $group ) {

         // Remove permissions for friend to any photos with group access.
         $queryString = "
            SELECT
               aid
            FROM
               grupp_tilgangtilalbum_dedikert
            WHERE
               gruppid=?
            ";
         $sharedAlbums = array();
         foreach ( DB::query( $queryString, $group )->fetchAll() as $album ) {

            list( $albumid ) = $album;
            $sharedAlbums[] = $albumid;

         }

         foreach ( $sharedAlbums as $aid ) {

            $queryString = "
               DELETE FROM
                  tilgangtilalbum_dedikert
               WHERE
                  uid=? AND
                  aid=? AND
                  groupid=?
               ";
            DB::query( $queryString, $friend, $aid, $group );

            $album = new Album( $aid );
            $album->reloadPermissions();

         }

         $queryString = "
            DELETE FROM
               grouplist
            WHERE
               uid=? AND
               friend=? AND
               groupid=?
            ";
         DB::query( $queryString, Login::userid(), $friend, $group );

      }


      /**
       * Is the user with this email and portal already registered?
       *
       * @param string $username
       * @param string $portal
       * @return boolean
       */
      static function registered( $username, $portal = '' ) {
         
         $userid = User::UserIDfromUsernameAndPortal( $username, $portal );
         if( empty( $userid ) ) {
            return false;
         }

         return true;

      }
      
      
       /**
       * Is the user with this email and portal already registered with nopass
       *
       * @param string $username
       * @param string $portal
       * @return boolean
       */
      static function hasNoPass( $username, $portal = '' ) {

         $userid = User::UserIDfromUsernameAndPortal( $username, $portal );
         $userid = (int) DB::query( "SELECT uid FROM brukar WHERE uid = ? AND passord = 'nopass'", $userid )->fetchSingle();
         if( empty( $userid ) ) {
            return false;
         }

         return true;

      }

      public function listProjects( $limit = 0 ) {

         $projects = new Project();

         $ret = array();
         foreach ( $projects->collection( array( 'id' ), array( 'user_id' => $this->uid ), 'saved DESC NULLS LAST, created DESC', $limit )->fetchAllAs( 'Project' ) as $project ) {

            $ret[] = $project;

         }

         return $ret;

      }

      /**
       * Get the sms services that user has registered.
       *
       * @return array
       */
      public function smsServices() {

         $notices = array();

         $res = DB::query( "
            SELECT
               cellnr,
               validated,
               order_sent_notice,
               sharing_notice
            FROM
               user_sms_services
            WHERE uid = ?
         ", Login::userid() );

         if( $res->count() ) {

            list( $cellnr, $validated, $orderSentNotice, $sharingNotice ) = $res->fetchRow();

            if( !empty( $validated ) ) {
               $notices['validated'] = true;
            }

            if( !empty( $orderSentNotice ) ) {

               $notices["order"] = $orderSentNotice;

            }

            if( !empty( $sharingNotice ) ) {
               $notices["sharing"] = $sharingNotice;
            }

         }

         return $notices;

      }



      /**
       * Check if user has subscribed to storage.
       *
       * @return array
       */
      public function subscription() {

         import( 'website.subscription' );
         return Subscription::fromUserId( Login::userid() );

      }

      public function checkDiscount( $code ) {

         /*$userid = $this->userid;

         // Must be logged in.
         if ( empty( $userid ) ) return false;

         $discount = new DiscountHistory();

         $ret = null;
         if ( $res = $discount->isActiveMultiple( $code ) ) {
            if( $res > 0 ) {
               $passed = $discount->activateMultiple( $code, $userid );
               return array(
                  'code' => 2,
                  'id' => $res,
               );
            }


         } else if ( $res = $discount->isActiveUnique( $code, $userid ) ) {

            if( $res > 0 ) {
               $passed = $discount->activateUnique( $code, $userid );
               return array( 'code' => 1, 'id' => $res );
            }

         } else {

            return array( 'code' => 0 );

         }*/

         //public function checkDiscount( $code ) {

         $userid = $this->userid;

         // Must be logged in.
         if ( empty( $userid ) ) return false;

         $discount = new DiscountHistory();

         $ret = null;
         if ( $res = $discount->isActiveMultiple( $code ) ) {
            if( $res > 0 ) {

               $passed = $discount->activateMultiple( $code, $userid );
               return array(
                  'code' => 2,
                  'id' => $res,
               );
            }

         } else if( $res = $discount->isUniqueActive( $code, Login::userId() ) ) {

            if( $res > 0 ) {
               $passed = $discount->activateUnique( $code, Login::userId(), $res );
               if( $passed ) {
                  return array(
                     'code' => 1,
                     'id' => $res,
                  );

               }

               //util::debug( $passed );

            }

         }

      }

      public function listDiscounts() {

         $discounts = new DiscountHistory();
         $ret = array();
         foreach ( $discounts->collection( array( 'id' ), array( 'user_id' => $this->userid ) )->fetchAllAs( 'DiscountHistory' ) as $discount ) {

            $ret[] = array(
               'id' => $discount->id,
               'used' => $discount->used,
               'code' => $discount->code,
               'title' => $discount->campaign->title
               );

         }

         return $ret;

      }

      public function setNewsletter( $value ) {

         import( 'services.campaignmonitor.list' );
         import( 'services.campaignmonitor.client' );

         $email = $this->email;
         $name = utf8_decode( $this->fullname );
         $portal = $this->portal;
         
         if( empty( $portal ) ){
            $portal = Dispatcher::getPortal();
         }
         
         if( !$portal ) $portal = 'EF-997';
         $fields = array(
            'firstname1' => utf8_decode( $this->firstname ),
         );

         $client = new CampaignMonitorClient();
         foreach( $client->getClientLists() as $listid => $match ) {
            if( preg_match( '/(.*)\('.$portal.'\)/', $match ) ) {
               $list = new CampaignMonitorList( $listid );
               if( $value ) {
                  $list->subscriberAddWithCustomFields( $email, $name, $fields );
               } else {
                  $list->subscriberUnsubscribe( $email );
               }
               break;
            }
         }

         $this->fieldSet( 'newsletter', $value );

      }
      
      public function setEmail( $email ) {
         
         // make sure it has actually changed
         if( $email == $this->username ) return true;
         
         // is newsletter enabled for this user?
         if( $this->fieldGet( 'newsletter' ) ) {
            
            // temporarily turn off newsletter
            $this->setNewsletter( false );
            
            // set the email address in the object
            $return = parent::setEmail( $email );
            
            // turn the newsletter back on
            $this->setNewsletter( true );
            
            // return the returned value
            return $return;
            
         } else {
            
            // return the parented value
            return parent::setEmail( $email );
            
         }

      }
      
      /**
       * Check if user is in vip table
       * Could be either an admin or
       * a regular vip customer
       *
       * @param unknown_type $userid
       */
      static function isVipUser( $userid ) {
         
         $res = DB::query( "
            SELECT 
               uid  
            FROM
               vip_customers 
            WHERE 
               uid = ? 
         ", $userid );
         
         if( $res->count() > 0 ) {
            return true;
         }
         
         return false;
         
      }
      
   }

?>
