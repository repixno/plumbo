<?php

   /**
    * Checkout complete
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'website.user' );
   import( 'website.cart' );
   import( 'validate.email' );
   config( 'website.countries' );
   config( 'website.stores' );

   class CheckoutConfirm extends WebPage implements IView {
      
      protected $template = 'checkout.confirm';
      private $relocatelink = '/cart';
      
      public function Execute() {
         
         $cart = new Cart();
         if( !count( $cart->enum() ) ) relocate( '/cart' );
         
         $cart = new Cart();
         $cart->recalculateTotals();
         
         // Is this user logged in?
         // Else we need to create one
         if( !Login::isLoggedIn() ) {
          
            $username = $_POST['cemail'];
            $password = $_POST['password'];
            $checkpassword = $_POST['password2'];
         
            if( isset( $_POST['eula'] ) ) {
               $eula = true;
            } else {
               $eula = false;
            }
            
            if( isset( $_POST['newsletter'] ) ) {
               $newsletter = true;
            } else {
               $newsletter = false;
            }
            
            // Check so Eula is accepted
            if( !$eula ) {
               Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Eula not accepted' ) );
               relocate( $this->relocatelink );
               die();
            }
              
            // Check email is set
            if( !isset( $username ) ) {
               Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Missing username' ) );
               relocate( $this->relocatelink );
               die();
            }
            
            // Validate the email as username
            if( !ValidateEmail::validate( $username ) ) {
               Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Username did not validate' ) );               
               relocate( $this->relocatelink );
               die();
            }
            
            if( isset( $_POST['contactemail'] ) ) {
               
               $contactemail = $_POST['contactemail'];
               
               // Validate the email as username
               if( !ValidateEmail::validate( $contactemail ) ) {
                  Session::pipe( 'userregistration', array( 'email' => $contactemail, 'registered' => false, 'error' => 'Contact email did not validate' ) );               
                  relocate( $this->relocatelink );
                  die();
               }
               
            }

            try {
               
               // Check if user already exists
               $refUser = User::fromUsernameAndPortal( $username, Dispatcher::getPortal() );
               if( $refUser instanceof User && $refUser->isLoaded() ) {
                  Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'User already exists' ) );
                  relocate( $this->relocatelink );
                  die();
               }
               
            } catch( Exception $e ) {
               
               Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Failed to check username' ) );
               relocate( $this->relocatelink );
               die();               
            }

            
            try {
               
               if( !isset( $_POST['password-new-user'] ) || !isset( $_POST['repeat-password'] ) ) {
                  Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Missing passwords' ) );
                  relocate( $this->relocatelink );
                  die();
               }
               
               if( $_POST['password-new-user'] != $_POST['repeat-password'] ) {
                  Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'New password missmatch' ) );
                  relocate( $this->relocatelink );
                  die();
               }
               
               $password = $_POST['password-new-user'];
               
               if( !strlen( $password ) > 0 ) {
                  Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Password is empty' ) );
                  relocate( $this->relocatelink );
                  die();
               }
               
               
               if( $_POST['cfirstname'] && $_POST['clastname'] ){
                  $fullname = $_POST['cfirstname'] . " " .  $_POST['clastname'] ;
               }else{
                  $fullname = $_POST['cname'];
               }
               
               $user = new User();
               $user->username = $username;
               $user->password = crypt( $password );
               $user->portal = Dispatcher::getPortal();
               $user->fullname = $fullname;
               $user->zipcode = $_POST["czipcode"];
               $user->city = $_POST["ccity"];
               $user->streetAddress = $_POST["caddress"];
               $user->country = $this->getCountryId( $_POST['ccountry'] );
               $user->created = date( 'Y-m-d H:i:s' );
               $user->newsletter = $newsletter;
               
               if( isset( $contactemail ) ) {
                  $user->contactemail = $contactemail;
               }
               
               if( Dispatcher::getPortal() == 'SKA-001' ){
                  if( isset( $_POST['newsletter'] ) ) {
                     $user->newsletter_repix = true;
                  } else {
                     $user->newsletter_repix = true;
                  }
                  if( isset( $_POST['newsletter-orkla'] )){
                     $user->stabburet_tilpassninger = true;
                  }
                  else{
                     $user->stabburet_tilpassninger = false;
                  }
                  
                  if( isset( $_POST['orklastats'] )){
                     $user->newsletter_orkla = true;
                  }
                  else{
                     $user->newsletter_orkla = false;
                  }
                  
                  
                  if( isset( $_POST['orkla3part'] )){
                     $user->stabburet_annonser = true;
                  }
                  else{
                     $user->stabburet_annonser = false;
                  }
               }
               
               $user->save();
               
               // Login user and update the session data
               Login::byUserObject( $user );
               $this->updatePageSessionData();
               
            } catch( Exception $e ) {
               
               Session::pipe( 'userregistration', array( 'email' => $username, 'registered' => false, 'error' => 'Failed creating new user' ) );
               relocate( $this->relocatelink );
               die();
               
            }
                        
         } else {
            
            $user = new User( Login::userid() );
            if( $user instanceof User && $user->isLoaded() ) {
               
               if( !$this->contactInfo( $user ) ) {
                  
                  if( !$this->updateContactInfo( $user, $_POST ) ) {
                     throw new Exception( 'Failed to update user contact information. '.Login::userid() );
                  }
                  
               }
               
    
               if( isset( $_POST['phone'] ) ){
                  $user->telefon_mobil = $_POST['phone'];
                  $user->save();
               }

               if( isset( $_POST['contactemail'] ) ) {
                  
                  if( !ValidateEmail::validate( $_POST['contactemail'] ) ) {
                     
                     Session::pipe( 'userregistration', array( 'email' => $_POST['contactemail'], 'registered' => false, 'error' => 'Failed to validate contact email' ) );
                     relocate( $this->relocatelink );
                     die();
                     
                  } else {
                     
                     $user->contactemail = $_POST['contactemail'];
                     $user->save();
                     
                  }
                  
               } 
               
            } else {
               
               throw new Exception( 'Failed to initialize user. '.Login::userid() );
               
            }
         }
         
         if( $_POST['cfirstname'] ){
            $_POST['cname']  =  $_POST['cfirstname'] .  " "  .  $_POST['clastname'];
         }
            
         
         if( empty( $_POST['cname']  ) ){
            relocate( $this->relocatelink );
            die();
         }else{
            $this->setAddresses( $_POST, $cart );
         }
          
         // If user is here and there is no weight
         // of cart then there is probably something wrong
         // with a product setup.
         // Set the weight to 1 to ensure user can go through
         // the checkout process.
         if( $cart->getTotalWeight() == 0 && $cart->getTotalItems() > 0 ) {
            $cart->setTotalWeight( 1 );
         }
         
         $cart->save();
         $this->cart = $cart->enum();
         
         $portal = Dispatcher::getPortal();
         if( is_null($portal) || $portal == "" ) {
            $portal = 'EF-997';
         }
         
         // DELIVERY METHOD
         
         // Store delivery stuff
         if( $portal == 'FC-001' ){   
            $fotoclick = Dispatcher::getCustomAttr( 'fotoclick' );
            $orgstoreid = "Fotoclick" . ":" .$fotoclick['id'];
         }
         else{
            $orgstoreid = isset( $_POST['delivertostore'] ) ? $_POST['delivertostore'] : null;
         }
         
         
         if( !is_null( $orgstoreid ) ) {
            $storegroupdata = explode( ':', $orgstoreid );
            $storegroup = reset( $storegroupdata );
            $storeid = end( $storegroupdata );
            $tmpstores = Settings::Get( 'storedelivery', $storegroup );
            $paymentmethodwhitelist = $tmpstores['paymentmethodwhitelist'];
            $prefid = $tmpstores['prefid'];
         }
         
         
         if( isset( $_POST['delivery-method'] ) ) {
            
            $deliverymethod = $_POST['delivery-method'];
            
            if( !$cart->setDeliveryType( $deliverymethod ) ) {
               relocate( $this->relocatelink );
            } else {
               
               $delivertype = $cart->getDeliveryType();
               if( isset( $storeid ) && $delivertype['artnr'] == Settings::Get( 'storedelivery', 'drefid' ) ) {
                  $cart->setStore( $orgstoreid );
               } else {
                  $cart->unsetStore();
               }
               
               $cart->save();
               
            }
            
         } else {
            
            $deliverymethod = $cart->getDeliveryType();
         }
         
         
         if( !isset( $deliverymethod ) && !$deliverymethod > 0 ) {
            
            if( $cart->serviceProductsOnly() ) {
               $cart->removeDeliveryType();
               $cart->recalculateTotals();
               $cart->save();
            
            }
            
            //relocate( '/checkout' );
         }
         
         /* ------------- */
         
         // PAYMENT METHOD
         if( isset( $_POST["payment-method"] ) ) {
            
            $paymentmethod = $_POST["payment-method"];
            
            if( $_POST["payment-method"] == "klarna-invoice" ){
               $paymentmethod = 1;
            }
            
            
            
            if( !$cart->setPaymentType( $paymentmethod ) ) {
               relocate( $this->relocatelink );
            } else {
               $cart->save();
            }
            
         } else {
            $paymentmethod = $cart->getPaymentType();
         }
         
         if( Dispatcher::getPortal() == 'SKA-001' ){
            //creditcar 78
            //$paymentmethod = 1;
            //$deliverymethod = 1;
            
            $cartarray = $cart->asArray() ;
            if( $cartarray['cartprice'] == 0 ){
               $paymentmethod = 78;
               $deliverymethod = 1;
               $price = 0;
            }else{
               //creditcar 78
               //faktura 1
               $paymentmethod = 1;
               $deliverymethod = 1;
               $this->showpayment = 1;
            }
            
            $cart->setDeliveryType( $deliverymethod, $price );
            $cart->setPaymentType( $paymentmethod );
            $cart->save();
         }

         if( !isset( $paymentmethod ) && !$paymentmethod > 0 ) {
            relocate( $this->relocatelink );
         }

         $this->cart = $cart->enum();
         
         
         
      }
      
      /**
       * Get the groups registered with this portal
       *
       * @param string $portal
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getStoreGroups( $portal ) {
         
         $groups = array();
         $tmp = Settings::Get( 'storedelivery', 'storegroup' );
         $groups = $tmp[$portal];
         return $groups;
         
      }
      
      /**
       * Get the delivery refid for the current portal
       *
       * @param string $portal
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getStoreDeliveryRefid() {
         
         return Settings::Get( 'storedelivery', 'drefid' );
         
      }
      
      /**
       * Get the array of stores available
       * for delivery.
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getStores( $groups ) {
         
         $stores = array();
         if( count( $groups ) > 0 ) {
            
            foreach( $groups as $group ) {
               
               $tmp = Settings::Get( 'storedelivery', $group );
               $tmp = $tmp['stores'];
               
               foreach( $tmp as $tmpstore ) {
                  
                  if( !isset( $stores[$group] ) ) $stores[$group] = array();
                  $tmpstore['id'] = $group.":".$tmpstore['id'];
                  $newstore = array( 'chain' => $group, 'store' => $tmpstore );
                  $stores[$group]['chain'] = $group;
                  $stores[$group]['stores'] []= $newstore;
                  
               }
               
            }
            
         }
         
         return $stores;
         
      }
      
      
      /**
       * Check user has a valid profile set
       *
       * @return boolean
       */
      private function contactInfo( User $user ) {

         if( $user instanceof User && $user->isLoaded() ) {
            
            if( !$user->firstname ) return false;
            if( !$user->lastname ) return false;
            if( !$user->streetaddress ) return false;
            if( !$user->zipcode ) return false;
            if( !$user->city ) return false;
            if( !$user->country ) return false;
            
            return true;
            
         }
         
         return false;
         
      }
      
      
      /**
       * Update the user contact profile
       *
       * @param User $user
       * @param array $_POST
       * @return boolean
       */
      private function updateContactInfo( User $user, $post ) {
         
         if( $user instanceof User && $user->isLoaded() ) {
            
            $name = $post['cname'];
            $address = $post['caddress'];
            $zipcode = $post['czipcode'];
            $city = $post['ccity'];
            $country = $this->getCountryId( $post['ccountry'] );
            
            if( isset( $post['contactemail'] ) ) {
               if( !ValidateEmail::validate( $post['contactemail'] ) ) {
                  return false;
               } else {
                  $contactemail = $post['contactemail'];
               }
            }
            
            $user->fullname = $name;
            $user->streetaddress = $address;
            $user->zipcode = $zipcode;
            $user->city = $city;
            $user->country = $country;
            
            if( isset( $contactemail ) ) $user->contactemail = $contactemail;
            
            $user->save();
            
            return true;
         }
         
         return false;
         
      }
      
      
      
      
      /**
       * Get the delivery type with lowest cost as preset
       *
       * @param array $options
       */
      private function setPresetDeliveryOption( $options = array() ) {
         
         if( count( $options ) > 0 ) {
            
            foreach( $options as $id => $data ) {

               if( !isset( $choosenOption ) ) {
                  $choosenOption = $id;
                 
               } else {
                  if( $data["price"] < $options[$choosenOption]["price"] && $data['artnr'] != DELIVERYTYPE_STORE ) {
                     $choosenOption = $id;
                  }
               }
               
            }
            
         }
         
         
         
         if( !empty( $choosenOption ) ) {
            $options[$choosenOption]["isPreset"] = true;
         }
         
         return $options;
         
      }
      
      
      /**
       * Add contact and delivery info to the cart
       *
       * @param array $info
       */
      private function setAddresses( $info, $cart ) {
         
         // Setup contactinfo
         if( isset( $info["cemail"] ) && !Login::isLoggedIn() ) {
            
            $contact["email"] = $info["cemail"];
            
            if( !ValidateEmail::validate( $contact["email"] ) ) {
               
               relocate( $relocatelink );
               
            }
         }
         
         
         $user = new User( Login::userid() );
         $user->setFullname( $info['cname'] );
         $user->setCellPhone( $info["phone"] );
         $user->streetaddress = $info['caddress'];
         $user->zipcode = $info['czipcode'];
         $user->city = $info['ccity'];
         $user->save();
         
         $contact["name"] = $info["cname"];
         $contact["zipcode"] = $info["czipcode"];
         $contact["city"] = $info["ccity"];
         $contact["address"] = $info["caddress"];
         $contact["country"] = $info["ccountry"];
         $contact["mphone"] = $info["phone"];
         $contact["contactperson"] = $info["contactperson"];
         
         // Setup delivery info
         $delivery["name"] = $info["dname"];
         $delivery["zipcode"] = $info["dzipcode"];
         $delivery["city"] = $info["dcity"];
         $delivery["address"] = $info["daddress"];
         $delivery["country"] = $info["dcountry"];

         // Check if users wants order delivered somewhere other than
         // contact address
         $otherDeliveryAddress = $info["other-delivery-address"];
         
         // Setup all items
         $items = explode( ' ', $contact["name"] );
         $firstname = trim( array_shift( $items ) );
         $lastname = trim( array_pop( $items ) );
         $middlename = trim( implode( ' ', $items ) );
         $firstname = $firstname." ".$middlename;
         $contact["firstname"] = $firstname;
         $contact["lastname"] = $lastname;

         
         if( !$contact["country"] ) $contact["country"] = 160;
         
         if( !isset( $contact["name"] ) || !isset( $contact["address"] ) || !isset( $contact["zipcode"] ) || !isset( $contact["city"] ) || !isset( $contact["country"] ) ) {
            relocate( $relocatelink );
         } else {
            $cart->setContactInfo( $contact );
         }
         
        
         // Setup all items
         $items = explode( ' ', $delivery["name"] );
         $firstname = trim( array_shift( $items ) );
         $lastname = trim( array_pop( $items ) );
         $middlename = trim( implode( ' ', $items ) );
         $firstname = $firstname." ".$middlename;
         $delivery["firstname"] = $firstname;
         $delivery["lastname"] = $lastname;
         
         $deliveryno = count( $delivery );
         
         if( isset( $info["other-delivery-address"] ) ) {
            
            if( isset( $delivery["firstname"] ) && isset( $delivery["lastname"] ) && isset( $delivery["address"] ) && isset( $delivery["zipcode"] ) && isset( $delivery["city"] ) && isset( $delivery["country"] ) ) {
               $cart->setDeliveryInfo( $delivery );
            } else {
               relocate( $relocatelink );
            }
         
         } else {
            $cart->setDeliveryInfo( $contact );
         }
         
      }
      
      
      /**
       * Create a new user
       *
       * @param array $userinfo
       */
      public function createUser( $userinfo = array() ) {
         
         // TMP TMP TMP
         $userinfo['eula'] = true;
         $userinfo['password'] = 'hola';
         $userinfo['password2'] = 'hola';
         
         $email = $userinfo["cemail"];
         $password = $userinfo["password"];
         $password2 = $userinfo["password2"];
         
         
         // Setup Eula and newsletter
         if( isset( $userinfo["eula"] ) ) {
            $eula = true;
         } else {
            $eula = false;
         }
         
         if( isset( $userinfo["newsletter"] ) ) {
            $newsletter = true;
         } else {
            $newsletter = false;
         }

         // Check so Eula is accepted
         if( !$eula ) {
            Session::pipe( 'registererror', "You have to accept the terms and conditions to register" );
            relocate( $this->relocatelink );
            die();
         }

         // Check so email and password and eula is set
         if( empty( $email ) || empty( $password ) || empty( $password2 ) ) {
            Session::pipe( 'registererror', 'Missing email or password. Please fill in all fields' );
            relocate( $this->relocatelink );
            die();
         }
         
         // Same password
         if( $password != $password2 ) {
            Session::pipe( 'registererror', "Passwords differ" );
            relocate( $this->relocatelink );
            die();
         }
         
         if( !ValidateEmail::validate( $email ) ) {
            Session::pipe( 'registererror', "Email not valid" );
            relocate( $this->relocatelink );
            die();
         }
         
         if( User::registered( $email ) ) {
            Session::pipe( 'registererror', "Username already registered" );
            relocate( $this->relocatelink );
            die();
         }
         
         try {
            // Fine - passed all checks
            // Let's create a new user
            
            $user = new User();
            $user->username = $email;
            $user->password = crypt( $password );
            $user->portal = Dispatcher::getPortal();
            if( $newsletter ) {
               $user->newsletter = true;
               $user->html = true;
            }
            
            $user->setFullname( $userinfo['cname'] );
            $user->streetaddress = $userinfo['caddress'];
            $user->zipcode = $userinfo['czipcode'];
            
            // Get corresponding country id
            $countries = Settings::getSection( 'countries' );
            $countryId = $countries[$userinfo['ccountry']]['id'];
            $user->country = $countryId;
            $user->city = $userinfo['ccity'];
            $user->created = date( 'Y-m-d H:i:s' );
            
            $user->save();

            // New user is created and saved
            // Continue and list delivery methods
            if( Login::byUserObject( $user ) ) {
               return true;
               die();
            }
            
            throw new CriticalException( 'Unable to login' );
            
         } catch( Exception $e ) {
         
            Session::pipe( 'registererror', "Unknown error trying to create new user" );
            relocate( $this->relocatelink );
            die();
            
         }
         
      }
      
      
      /**
       * Get the corresponding country id from a country 2char ISO code
       *
       * @param string $countryCode
       * @return unknown
       */
      private function getCountryId( $countryCode = 'NO' ) {
         
         $countries = Settings::getSection( 'countries' );
         return $countries[$countryCode]['id'];
         
      }
      
   }


?>