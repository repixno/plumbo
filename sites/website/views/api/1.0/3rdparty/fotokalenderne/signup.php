<?PHP
   
   import( 'pages.json' );
   
   class FotokalenderneAuthSignupAPI extends JSONPage implements IView, NoAuthRequired {
      
      /**
       * Create or update a user based on username and password against 3rdparty backend API
       * 
       * @api-name 3rdparty.fotokalenderne.signup
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
       * @api-result token String Unique login-token
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      
      public function execute( $username = '', $password = '', $firstname = '', $middlename = '', $lastname = '', $address1 = '', $address2 = '', $city = '', $zipcode = '', $phone = '', $fullname = '' ) {
         
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
            
            if( Login::isLoggedIn() ) {
               
               $tmpuser = new User( Login::userid() );
               
               $xmldocument = sprintf( '<?xml version="1.0" encoding="utf-8"?>
                                        <registration>
                                          <mandatory>
                                             <identifier>%s</identifier>
                                             <first_name>%s</first_name>
                                             <last_name>%s</last_name>
                                             <street>%s</street>
                                             <city>%s</city>
                                             <zip>%s</zip>
                                             <phone>%s</phone>
                                          </mandatory>
                                        </registration>',
                                        $tmpuser['contactemail'],
                                        trim( $firstname.' '.$middlename ),
                                        $lastname,
                                        trim( $address1."\n".$address2 ),
                                        $city,
                                        $zipcode,
                                        $phone
                                     );
               
               $url = 'http://www.fotokalenderne.no/api/signup/update.php';
               
            } else {
               
               $xmldocument = sprintf( '<?xml version="1.0" encoding="utf-8"?>
                                        <registration>
                                          <mandatory>
                                             <email>%s</email>
                                             <pwd>%s</pwd>
                                             <first_name>%s</first_name>
                                             <last_name>%s</last_name>
                                             <street>%s</street>
                                             <city>%s</city>
                                             <zip>%s</zip>
                                             <phone>%s</phone>
                                          </mandatory>
                                        </registration>',
                                        $username,
                                        $password,
                                        trim( $firstname.' '.$middlename ),
                                        $lastname,
                                        trim( $address1."\n".$address2 ),
                                        $city,
                                        $zipcode,
                                        $phone
                                     );
               
               $url = 'http://www.fotokalenderne.no/api/signup/sign.php';
               
            }
            
            $clientURL = curl_init();    
            $PHP_Header[] = "";
            
            curl_setopt( $clientURL, CURLOPT_POST, 1 );
            curl_setopt( $clientURL, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $clientURL, CURLOPT_CUSTOMREQUEST, 'POST' );
            curl_setopt( $clientURL, CURLOPT_URL, $url );
            curl_setopt( $clientURL, CURLOPT_REFERER,'http://www.fotokalenderne.no' );
            curl_setopt( $clientURL, CURLOPT_TIMEOUT, 60 );
            curl_setopt( $clientURL, CURLOPT_HTTPHEADER, $PHP_Header );
            curl_setopt( $clientURL, CURLOPT_POSTFIELDS, $xmldocument );
            
            $response = curl_exec( $clientURL );
            curl_close( $clientURL );
            
            $response = simplexml_load_string( $response );
            
            if( (int) $response->error == 0 ) {
               
               $identifier = (string) $response->identifier;
               $redirecturl = (string) $response->redirecturl;
               $token = (string) $response->token;
               
               if( !$identifier && Login::isLoggedIn() ) {
                  $identifier = Login::data('username');
               }
               
               $user = User::fromUsername( $identifier );
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
                  $user->username = $identifier;
                  $user->fullname   = trim( trim( $firstname.' '.$middlename ).' '.$lastname );
                  $user->firstname  = $firstname;
                  $user->middlename = $middlename;
                  $user->lastname   = $lastname;
                  $user->streetaddress = $address1;
                  $user->streetaddress2 = $address2;
                  $user->zipcode    = $zipcode;
                  $user->city       = $city;
                  $user->contactemail = $username;
                  $user->cellphone  = $phone;
                  $user->country = 160; // default to Norway
                  
                  // save the user
                  $user->save();
                  
                  if( !Login::byUserObject( $user ) ) {
                     throw new Exception( 'Login failed. Unable to validate new user!' );
                  }
                  
               }
               
               $user->setPreference( 'fotokalenderne_token', $token );
               $user->setPreference( 'fotokalenderne_redirect', $redirecturl );
               
               $this->result = true;
               $this->message = 'OK';
               
            } else {
               
               $this->result = false;
               $this->message = (string) $response->description;
               
            }
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
      
   }
   
?>