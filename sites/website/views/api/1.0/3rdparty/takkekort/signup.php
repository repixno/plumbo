<?PHP
   
   import( 'pages.json' );
   import( 'xtci.config');
   import( 'xtci.xtci');
   import( 'xtci.xtci_sso');
   
   class TakkekortAuthSignupAPI extends JSONPage implements IView, NoAuthRequired {
      
      /**
       * Create or update a user based on username and password against 3rdparty backend API
       * 
       * @api-name 3rdparty.takkekort.signup
       * @api-post-optional username String Username
       * @api-post-optional password String Password
       * @api-post-optional firstname String The users firstname
       * @api-post-optional middlename String The users middlename
       * @api-post-optional lastname String The users lastname
       * @api-post-optional fullname String If set, is parsed into tokens and overwrites first-, middle- and lastname.
       * @api-post-optional address1 String The users address, line 1
       * @api-post-optional address2 String The users address, line 2
       * @api-post-optional city String The users city
       * @api-post-optional zipcode String The users zipcode
       * @api-post-optional phone String The users phone
       * @api-param-optional username String Username
       * @api-param-optional password String Password
       * @api-param-optional firstname String The users firstname
       * @api-param-optional middlename String The users middlename
       * @api-param-optional lastname String The users lastname
       * @api-param-optional address1 String The users address, line 1
       * @api-param-optional address2 String The users address, line 2
       * @api-param-optional city String The users city
       * @api-param-optional zipcode String The users zipcode
       * @api-param-optional phone String The users phone
       * @api-param-optional fullname String If set, is parsed into tokens and overwrites first-, middle- and lastname.
       * @api-param-optional newsletter boolen true/false.
       * @api-result token String Unique login-token
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      
      public function execute( $username = '', $password = '', $firstname = '', $middlename = '', $lastname = '', $address1 = '', $address2 = '', $city = '', $zipcode = '', $phone = '', $fullname = '', $newsletter = '' ) {
         
         $username   = trim( isset( $_POST['username'] )   ? $_POST['username']   : $username );
         $password   = trim( isset( $_POST['password'] )   ? $_POST['password']   : $password );
         $firstname  = trim( isset( $_POST['firstname'] )  ? $_POST['firstname']  : $firstname );
         $middlename = trim( isset( $_POST['middlename'] ) ? $_POST['middlename'] : $middlename );
         $lastname   = trim( isset( $_POST['lastname'] )   ? $_POST['lastname']   : $lastname );
         $address1   = trim( isset( $_POST['address1'] )   ? $_POST['address1']   : $address1 );
         $address2   = trim( isset( $_POST['address2'] )   ? $_POST['address2']   : $address2 );
         $city       = trim( isset( $_POST['city'] )       ? $_POST['city']       : $city );
         $zipcode    = trim( isset( $_POST['zipcode'] )    ? $_POST['zipcode']    : $zipcode );
         $phone      = trim( isset( $_POST['phone'] )      ? $_POST['phone']      : $phone );
         $fullname   = trim( isset( $_POST['fullname'] )   ? $_POST['fullname']   : $fullname );
         
         if( $_POST['newsletter'] == 'true' ){
            $newsletter = true;
         }else{
            $newsletter = false;
         }
         
         if( $fullname ) {
            
            $names = explode( ' ', $fullname );
            if( count( $names ) > 2 ) {
               
               $firstname = array_shift($names);
               $middlename = array_shift($names);
               $lastname = implode( ' ', $names );
               
            } else if( count( $names ) > 1 ) {
               
               $firstname = array_shift($names);
               $middlename = '';
               $lastname = implode( ' ', $names );
               
            } else {
               
               $lastname = $fullname;
               $middlename = '';
               $firstname = '';
               
            }
            
         }
         
         try {
            
            $XTCiForSSO = new XTCiForSSO();
            
            if( Login::isLoggedIn() ) {
               
               $user = array(
                     'email' => Login::data('username'),
                     'salutation' => '',
                     'firstname'    => $firstname . ' ' . $middlename,
                     'lastname'     => $lastname,
                     'title'        => '',
                     'street'       => trim( $address1."\n".$address2 ),
                     'zip'          => $zipcode,
                     'city'         => $city,
                     'country'      => 'Norway',
                     'iso_country'  => 'no',
                     'phone' => $phone,
                     'fax'          => ''
               );
               
               $this->result = true;
               $this->message = 'OK';
               die();
               /*$XTCiForSSO = new XTCiForSSO();
               $response = $XTCiForSSO->xtci_login( $username, $password );*/
               
            } else {
               
               $user = array(
                     'email' => $username,
                     'pwd' => $password,
                     'salutation' => '',
                     'firstname'    => $firstname . ' ' . $middlename,
                     'lastname'     => $lastname,
                     'title'        => '',
                     'street'       => trim( $address1."\n".$address2 ),
                     'zip'          => $zipcode,
                     'city'         => $city,
                     'country'      => 'Norway',
                     'iso_country'  => 'no',
                     'phone' => $phone,
                     'fax'          => ''
               );
               
                          
               $response = $XTCiForSSO->xtci_useradd( $user );

            }
            
            
            if( $response[1] == 0 ) {
               $XTCiForSSO->xtci_login( $username, $password );
               $userinfo =   $XTCiForSSO->xtci_userinfo();
               
               if( class_exists('Dispatcher') ) {
                  $logingroup = Dispatcher::getLoginGroup();
               } else {
                  $logingroup = '';
               }
               
               $identifier = (string) $userinfo[0]['id'];
               $uid = DB::query( "SELECT b.uid FROM kunde k, brukar b WHERE b.uid = k.uid AND contactemail = ?  AND b.logingroup = ? and b.deleted is  null ORDER BY uid desc LIMIT 1", $userinfo[0]['login'], $logingroup  )->fetchSingle();
               
               if( !empty( $uid ) ){
                   $user = new DBUser( $uid );
               }
               
               if( $user instanceof DBUser && $user->isLoaded() ) {
                  
                  $user = new User( $user->uid );
                  if( $user instanceof User && $user->isLoaded() ) {
                     
                     if( !Login::byUserObject( $user ) ) {
                        throw new Exception( 'Login failed. Local user not valid!' );
                     }
                     
                  }
                  
               } else {
                  
                  $user = new User();
                  $user->portal = Dispatcher::getPortal();
                  $user->logingroup = Dispatcher::getLoginGroup();
                  $user->username = $userinfo[0]['login'];
                  $user->fullname   = trim( trim( $userinfo[0]['firstname']  ).' '.$userinfo[0]['lastname'] );
                  $user->firstname  = $userinfo[0]['firstname'] ;
                  $user->lastname   = $userinfo[0]['lastname'];
                  $user->streetaddress = $userinfo[0]['street'];
                  $user->streetaddress2 = '';
                  $user->zipcode    = $userinfo[0]['zip'];
                  $user->city       = $userinfo[0]['city'];
                  $user->contactemail = $userinfo[0]['login'];
                  $user->cellphone  = $userinfo[0]['phone'];
                  $user->country = 160; // default to Norway
                  $user->newsletter = $newsletter;
                  
                  // save the user
                  $user->save();
                  
                  if( !Login::byUserObject( $user ) ) {
                     throw new Exception( 'Login failed. Unable to validate new user!' );
                  }
                  
               }
               
               //$user->setPreference( 'takkekort_token', $token );
               //$user->setPreference( 'takkekort_redirect', $redirecturl );
               
               $this->result = true;
               $this->message = 'OK';
               
            } else {
               
               $this->result = false;
               $this->message = (string) $response[2];
               
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
   }
   
?>