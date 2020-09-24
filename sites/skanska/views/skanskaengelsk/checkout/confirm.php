<?php

   /**
    * Checkout complete for stabburet campaign
    *
    */
   
   import( 'website.user' );
   import( 'website.cart' );
   import( 'validate.email' );
   config( 'website.countries' );
   config( 'website.stores' );

   class CheckoutConfirm extends WebPage implements IView {
      
      protected $template = 'checkout.confirm';
      
      public function Execute() {

         $userinfo = $_POST;
         
         $cart = new Cart();
         if( !count( $cart->enum() ) ) relocate( '/cart' );
         
         $cart = new Cart();
         $cart->recalculateTotals();
         
         $email = $_POST['cemail'];
         
        //$newsletter=nyhetsbrev Repix
         $newsletter = $_POST['newsletter']?true:false;
         
         //$newsletterorkla = Tilpassninger
         $newsletterorkla = $_POST['newsletter-orkla']?true:false;
         
         // Nyhetsbrev Orkla
         $orklastats = $_POST['orklastats']?true:false;
         //Annonser Orkla 
         $orkla3part = $_POST['orkla3part']?true:false;
         
         $kjonn = $_POST['kjonn'];
         
        
         
         $user = User::fromUsernameAndPortal($email, 'SKA-001');
         
         Util::Debug($userinfo);
         
         if( $user ){
             $user = new User( $user->uid );
             $user->setFullname( $userinfo['cname'] );
             $user->setCellPhone( $userinfo["phone"] );
             $user->streetaddress = $userinfo['caddress'];
             $user->zipcode = $userinfo['czipcode'];
             $user->city = $userinfo['ccity'];
             $user->kjonn = $kjonn;
             if( $newsletter ) {
                 $user->newsletter_repix = true;
                 $user->html = true;
             }
             if( $newsletterorkla ){
                 $user->stabburet_tilpassninger = true;
             }
             if( $orklastats ){
                 $user->newsletter_orkla = true;
             }
             
             if( $orkla3part ){
                 $user->stabburet_annonser = true;
             }
             $user->save();
             
             if( !Login::isLoggedIn() ){
                 if( !Login::byUserObject( $user ) ) {
                     throw new CriticalException( 'Unable to login' );
                 }
             }
             
         }else{
             
             try {
                 // Fine - passed all checks
                 // Let's create a new user
                 
                 $user = new User();
                 $user->username = $email;
                 $user->password = 'nopass';
                 $user->portal = Dispatcher::getPortal();
                 
                 if( $newsletter ) {
                    $user->newsletter_repix = true;
                    $user->html = true;
                 }
                 if( $newsletterorkla ){
                     $user->stabburet_tilpassninger = true;
                 }
                 
                 if( $orklastats ){
                     $user->newsletter_orkla = true;
                 }
                 
                 if( $orkla3part ){
                     $user->stabburet_annonser = true;
                 }
                 
                 $user->setFullname( $userinfo['cname'] );
                 $user->streetaddress = $userinfo['caddress'];
                 $user->zipcode = $userinfo['czipcode'];
                 $user->kjonn = $kjonn;
                 $user->setCellPhone( $userinfo["phone"] );
                 
                 $user->country = 203;
                 $user->city = $userinfo['ccity'];
                 $user->created = date( 'Y-m-d H:i:s' );
                 $user->save();
                 // New user is created and saved
                 // Continue and list delivery methods
                 if( !Login::byUserObject( $user ) ) {
                     throw new CriticalException( 'Unable to login' );
                 }
                 
             } catch( Exception $e ) {
               
                  Util::Debug($e->getMessage());
                  exit;
               
                 Session::pipe( 'registererror', "Unknown error trying to create new user" );
                 relocate( '/skanskaengelsk/checkout/' );
                 die();  
             }
             
         }
         
         if( empty( $_POST['cname'] ) ){
            relocate( "/skanskaengelsk/checkout" );
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

         
         if( Dispatcher::getPortal() == 'ST-001' || Dispatcher::getPortal() == 'SKA-001' ){
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
               $paymentmethod = 78;
               $deliverymethod = 1;
               $this->showpayment = 1;
            }
            
            $cart->setDeliveryType( $deliverymethod, $price );
            $cart->setPaymentType( $paymentmethod );
            $cart->save();
         }

         if( !isset( $paymentmethod ) && !$paymentmethod > 0 ) {
            relocate( '/checkout' );
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
               
               relocate( "/checkout" );
               
            }
         }
         
         $contact["name"] = $info["cname"];
         $contact["zipcode"] = $info["czipcode"];
         $contact["city"] = $info["ccity"];
         $contact["address"] = $info["caddress"];
         $contact["country"] = $info["ccountry"];
         $contact["mphone"] = $info["phone"];
         
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
         
         if( !isset( $contact["firstname"] ) || !isset( $contact["lastname"] ) || !isset( $contact["address"] ) || !isset( $contact["zipcode"] ) || !isset( $contact["city"] ) || !isset( $contact["country"] ) ) {
            relocate( '/checkout' );
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
               relocate( "/checkout" );
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
            relocate( '/checkout' );
            die();
         }

         // Check so email and password and eula is set
         if( empty( $email ) || empty( $password ) || empty( $password2 ) ) {
            Session::pipe( 'registererror', 'Missing email or password. Please fill in all fields' );
            relocate( '/checkout' );
            die();
         }
         
         // Same password
         if( $password != $password2 ) {
            Session::pipe( 'registererror', "Passwords differ" );
            relocate( '/checkout' );
            die();
         }
         
         if( !ValidateEmail::validate( $email ) ) {
            Session::pipe( 'registererror', "Email not valid" );
            relocate( '/checkout' );
            die();
         }
         
         if( User::registered( $email ) ) {
            Session::pipe( 'registererror', "Username already registered" );
            relocate( '/checkout' );
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
            relocate( '/checkout' );
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