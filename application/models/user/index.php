<?PHP

   import( 'core.model' );

   class DBUser extends Model {

      static $table = 'brukar';

      static $fields = array(
         'uid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'brukarnamn' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'passord' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'kode' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'registrert'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'deleted'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'aliasfor'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'exempted_subscription'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'logingroup' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'trackingcode' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
      );

      public function save() {
         if( strlen( $this->brukarnamn ) < 4 ){
            mail( 'adele@repix.no', 'DBUser Bug', "Feil med oppretting av bruker pga. manglende brukernavn\n" . print_r( debug_backtrace(), true ));
            throw new SecurityException( 'Invalid username!' );
         }
         return parent::save();
      }
      
      public function __setup() {

         $return = parent::__setup();
         
         $this->registrert = date( 'Y-m-d H:i:s' );
         if( class_exists('Dispatcher') ) {
            $this->logingroup = Dispatcher::getLoginGroup();
            $this->trackingcode = Dispatcher::getTrackingCode();
         }
         
         return $return;

      }

      public function isLoaded() {

         if( !empty( $this->deleted ) ) return false;
         return parent::isLoaded();

      }

      public function delete() {

         $this->deleted = date( 'Y-m-d H:i:s' );
         $this->save();

      }

      public function getUserId() {

         return $this->uid;

      }

      public function setUserId( $userid ) {

         return $this->uid = $userid;

      }

      public function getPassword() {

         return $this->passord;

      }

      public function setPassword( $password ) {

         return $this->passord = $password;

      }

      public function getUsername() {
         
         //if( strlen( $this->brukarnamn ) < 4 ) throw new SecurityException( 'Invalid username!' );
         return strtolower( $this->brukarnamn );

      }

      public function setUsername( $username ) {
         
         if( strlen( $username ) < 4 ) throw new SecurityException( 'Invalid username!' );
         return $this->brukarnamn = strtolower( $username );

      }

      public function getEmail() {

         return $this->getUsername();

      }

      public function setEmail( $email ) {

         return $this->setUsername( $email );

      }

      public function getCreated() {

         return $this->registrert;

      }

      public function setCreated( $date ) {

         return $this->registrert = $date;

      }


      public function setPortal( $portal = '' ) {

         $this->kode = strtoupper( $portal );

      }


      public function getPortal() {

         return $this->kode;

      }


      static function fromUsername( $username ) {
         
         if( class_exists('Dispatcher') ) {
            $logingroup = Dispatcher::getLoginGroup();
         } else {
            $logingroup = '';
         }
         
         if( !$logingroup ) {
            $logingroup = '';
            $queryString = "SELECT uid FROM brukar WHERE brukarnamn = ? AND (logingroup = ? OR logingroup IS NULL)";
         } else {
            $queryString = "SELECT uid FROM brukar WHERE brukarnamn = ? AND logingroup = ?";
         }
         
         $uid = (int) DB::query( $queryString, $username, $logingroup )->fetchSingle();
         
         //$find = new DBUser();
         //$find->collection( array( 'uid' ), array( 'brukarnamn' => $username ), null, 1 )->fetchSingle();

         if ( $uid > 0 ) {

            $object = new DBUser( $uid );
            if( !$object->isLoaded() ) return null;
            return $object;

         } else {

            return null;

         }

      }

      public function isCustomer() {

         if ( DB::query( 'SELECT COUNT(uid) FROM kunde WHERE uid=?', $this->userid )->fetchSingle() > 0 ) {

            return true;

         } else {

            return false;

         }

      }

      public function isAvailableUsername( $username, $notCheckId = null ) {

         if( class_exists('Dispatcher') ) {
            $logingroup = Dispatcher::getLoginGroup();
         } else {
            $logingroup = '';
         }
         
         if( !$logingroup ) {
            $logingroup = '';
            $queryString = "SELECT COUNT(brukarnamn) FROM brukar WHERE brukarnamn = ? AND (logingroup = ? OR logingroup IS NULL)";
         } else {
            $queryString = "SELECT COUNT(brukarnamn) FROM brukar WHERE brukarnamn = ? AND logingroup = ?";
         }
         
         if ( isset( $notCheckId ) && is_numeric( $notCheckId ) ) {

            $queryString .= ' AND uid!=' . $notCheckId;

         }

         if ( DB::query( $queryString, $username, $logingroup )->fetchSingle() == 0 ) {

            return true;

         } else {

            return false;

         }

      }

   }

?>
