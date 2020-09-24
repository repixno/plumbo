<?PHP

   model( 'reedfoto.user' );
   Import( 'reedfoto.correction' );

   class RFUser extends DBRFUser implements ModelCaching {

      static $fullnamecache = array();

      static function UserIDfromUsername( $username ) {

         // prepare the data for database query
         $username = str_replace( '%', '', strtolower( $username ) );
         $username = str_replace( '_', '\\_', $username );

         // execute the query and retreive the userid
         $userid = (int) DB::query( 'SELECT id FROM reedfoto_user WHERE username ILIKE ?', $username )->fetchSingle();

         // return the userid
         return $userid;

      }

      static function fromUsernameAndPassword( $username, $password ) {

         try {

            // first, find the userid
            $userid = RFUser::UserIDfromUsername( $username );

            // now, try to load the user
            $user = new RFUser( $userid );

            // finally, validate the password
            if( !RFUser::validatePassword( $password, $user->password ) ) return false;

            return $user;

         } catch ( Exception $e ) {

            return false;

         }

      }

      static function validatePassword( $a, $b ) {

         return md5( $a ) == $b ? true : false;

      }

      static function getNameFromUid( $userid ) {

         // attempt to retreive and store the user data from cache
         if( isset( RFUser::$fullnamecache[$userid] ) ) {

            return RFUser::$fullnamecache[$userid];

         } else {

            // use fullname from session data for own content
            if( $userid > 0 && $userid == Login::userid() ) {
               return Login::data('fullname');
            }

            // attempt to retreive and store the user data from db
            try {
               $user = new RFUser( $userid );
               return RFUser::$fullnamecache[$userid] = $user->fullname;
            } catch ( Exception $e ) {
               return RFUser::$fullnamecache[$userid] = __( 'Unknown' );
            }

         }

      }

      static function fromRFUsername( $username, $portal = '' ) {

         try {

            // first, find the userid
            $userid = RFUser::UserIDfromUsername( $username );

            // now, try to load the user
            $user = new RFUser( $userid );

            return $user;

         } catch ( Exception $e ) {

            return false;

         }

      }

      static function enum( $admins = false ) {

         $users = new RFUser();

         $ret = array();
         foreach ( $users->collection( array( 'id' ), array( 'type' => $admins ? 'admin' : 'user' ) )->fetchAllAs( 'RFUser' ) as $user ) {

            $ret[] = $user;

         }

         return $ret;

      }

      public function asArray() {

         $ret = array(
            'id' => $this->id,
            'fullname' => $this->fullname,
            'username' => $this->username,
            'type' => $this->type
         );

         return $ret;

      }
      
      public function delete() {
         
         $corrections = new RFCorrection();

         foreach ( $corrections->collection( array( 'id' ), array( 'userid' => $this->id ) )->fetchAllAs( 'RFCorrection' ) as $correction ) {

            $correction->delete();

         }
         
         parent::delete();
         
         return true;
         
      }
      
   }

?>