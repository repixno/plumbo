<?PHP
   
   import( 'pages.json' );
   import( 'xtci.config');
   import( 'xtci.xtci');
   import( 'xtci.xtci_sso');
   
   class TakkekortAuthLoginAPI extends JSONPage implements IView, NoAuthRequired {
      
      /**
       * Authenticate by username and password against 3rdparty backend API
       * 
       * @api-name 3rdparty.takkekort.login
       * @api-post-optional username String Username
       * @api-post-optional password String Password
       * @api-get-optional username String Username
       * @api-get-optional password String Password
       * @api-result token String Unique login-token
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      public function execute( $username = '', $password = '' ) {
         
         $username = isset( $_POST['username'] ) ? $_POST['username'] : $username;
         $password = isset( $_POST['password'] ) ? $_POST['password'] : $password;
         
         try {
            
            $XTCiForSSO = new XTCiForSSO();
            $response = $XTCiForSSO->xtci_login( $username, $password );
            
            
            if( $response[1] == 49  ){
               $XTCiForSSO->clear();
               $response = $XTCiForSSO->xtci_login( $username, $password );
            }

            try{
               
               if( $response[1] == 0 || $response[1] == 49 ) {
                  
                  $userinfo =   $XTCiForSSO->xtci_userinfo();
                  
                  if( class_exists('Dispatcher') ) {
                     $logingroup = Dispatcher::getLoginGroup();
                  } else {
                     $logingroup = '';
                  }
                  
                  $identifier = (string) $userinfo[0]['id'];
                  $uid = DB::query( "SELECT b.uid FROM kunde k, brukar b WHERE b.uid = k.uid AND contactemail = ?  AND b.logingroup = ? AND  b.deleted is null ORDER BY uid desc LIMIT 1", $userinfo[0]['login'], $logingroup  )->fetchSingle();
                  
                  if( !empty( $uid ) ){
                      $user = new DBUser( $uid );
                  }
                  
                  if( !$user instanceof DBUser || !$user->isLoaded() ) {
                                          
                     //if( preg_match("/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/", $identifier ) > 0 ){
                     if( strlen ( $identifier ) > 3 ) {
                        
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
                        
                        // save the user
                        $user->save();
                        
                     }
                     else{
                        throw new Exception( 'Login failed. Invalid user!' );
                     }
                     //}
                     //throw new Exception( 'Login failed. Local user not found!' );
                  }
                  
                  $user = new User( $user->uid );
                  if( !$user instanceof User || !$user->isLoaded() ) {
                     throw new Exception( 'Login failed. Local user not valid!' );
                  }                  
                  if( !Login::byUserObject( $user ) ) {
                     throw new Exception( 'Login failed. Local user not valid!' );
                  }
                  
                  //$user->setPreference( 'takkekort_token', $token );
                  //$user->setPreference( 'takkekort_redirect', $redirecturl );
                  
                  //$this->token = $token;
                  $this->result = true;
                  $this->message = 'OK';
                  
               } else {
                  
                  $this->result = false;
                  $this->message = "Feil brukernavn og/eller passord. Vennligst prøv igjen.";
                  
               }
            }catch( Exeption $e){
               throw new Exception( 'Login is not available!' );
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
   }
   
?>