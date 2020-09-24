<?php


   import( 'website.user' );
   config( 'website.countries' );
   import( 'website.credit' );
   import( 'validate.email' );

   class MyAccountSettings extends UserPage implements IView {

      protected $template = 'myaccount.settings';

      public function Execute() {

         $statusmsg = Session::pipe( 'statusmsg' );
         if ( !empty( $statusmsg ) ) {

            $this->statusmsg = $statusmsg;

         }

         $user = new User( Login::userid() );
         $userdata = array();

         if( !empty( $user ) && $user instanceof User  ) {

            $userdata = array(
               "email"           => $user->username,
               "firstname"       => trim( $user->firstname ),
               "middlename"      => trim( $user->middlename ),
               "lastname"        => trim( $user->lastname ),
               "contactinfo"     => array(
                  "address"         => $user->streetaddress,
                  "address2"        => $user->secondstreetaddress,
                  "zipcode"         => $user->zipcode,
                  "city"            => $user->city,
                  "country"         => $this->getUserCountry( $user->country ),
                  "mobile"          => $user->cellPhone,
                  "phone"           => $user->phone,
                  "phone_night"     => $user->phonenight,
               ),
               "contact"         => array(
                  "newsletter"      => $user->newsletter,
                  "spam"            => $user->newsletter_others,
                  "html"            => $user->html,
               ),
               "gender"             => $user->gender,
               "birthdate"       => date( 'Y-m-d', strtotime( $user->born ) ),
               "avatar"    => array(
                  "id"         => $user->profile_picture,
                  "thumbnailurl"    => $user->getAvatarUrl(),
                  "fullsizeurl"     => $user->getFullsizeAvatarUrl(),
               ),
               "sms" => $user->smsServices(),
               'subscription' => array(
                  'active' => false,
                  'registered' => '',
                  'type_subscription' => '',
                  'valid_to' => '',
               ),
            );

            // Get subscription status.
            $subsActive = $user->subscription();
            if ( $subsActive !== false ) {

               $userdata[ 'subscription' ] = array(
                  'active' => $subsActive->active,
                  'registered' => $subsActive->registered,
                  'type_subscription' => $subsActive->type_subscription,
                  'valid_to' => $subsActive->valid_to
                  );

            }

         }

         $this->user = $userdata;
         $this->countries = $this->getCountries();

         $this->credits = $this->credits();
         $this->subscription = $this->subscription();
         $this->discounts = $user->listDiscounts();

      }


      private function getUserCountry( $country = 160 ) {

         $efCountries = Settings::getSection( 'efcountries' );
         return $efCountries[$country]["2char"];

      }


      private function getCountries() {

         $countries = Settings::getSection( "countries" );
         $countryres = array();

         if( count( $countries ) > 0 ) {

            foreach( $countries as $iso => $data ) {

               $countryres[] = array(
                  "name"   => $data["name"],
                  "iso"    => $data["2char"],
               );

            }

         }

         return $countryres;

      }


      /**
       * Save the user settings
       */
      public function save() {

         $user = new User( Login::userid() );

         if ( !$user->isLoaded() || !($user instanceof User) ) {

            $errorMsg = __('Could not load user data.');

         }

         $retStatus = array();
         switch ( $_POST[ 'actiontype' ] ) {

            case 'login':
               $retStatus = $this->saveLoginData( $user );
               break;
            case 'user':
               $retStatus = $this->saveUserData( $user );
               break;
            case 'discount':
               $retStatus = $this->checkDiscount( $user );
               break;
            case 'smsnewsletter':
               $retStatus = $this->saveSMSNewsLetter( $user );
               break;

         }

         Session::pipe( 'statusmsg', $retStatus );

         relocate( '/myaccount/settings' );

      }

      public function checkDiscount( $user ) {

         $discountCode = $_POST["coupon-code"];

         $resStatus = array();

         // Checking and register discount code.
         if ( !empty( $discountCode ) ) {

            $status = $user->checkDiscount( $discountCode );

            switch ( $status ) {

               case 2:
                  $resStatus[ 'multiplediscountsaved' ] = true;
                  break;
               case 1:
                  $resStatus[ 'singlediscountsaved' ] = true;
                  break;
               case 0:
                  $resStatus[ 'unknowndiscountcode' ] = true;
                  break;

            }

         } else {

            $resStatus[ 'missingcode' ] = true;

         }

         return $resStatus;

      }

      private function saveSMSNewsLetter( $user ) {

         $newsletter = (int) $_POST['newsletter'];
         $newsletter_others = (int) $_POST['newsletter_others'];

         $user->newsletter = $newsletter;
         $user->newsletter_others = $newsletter_others;
         $user->save();

         return array();

      }

      /**
      * Save user login data, password.
      *
      * @access      public
      * @param       object         $user       User object to be modified.
      * @return      array                      Status messages.
      * @author      Svein Arild Bergset <sab@interweb.no>
      * @version     0.2 - 04.01.2010 15:28
      * @since       01.08.2009 09:01
      *
      */
      private function saveLoginData( $user ) {

         // Email.
         $email = $_POST[ 'emailchange' ];
         $oldemail = $_POST[ 'oldemail' ];
         // Password.
         $passwordOld = $_POST[ 'password' ];
         $passwordNew = $_POST[ 'passwordnew' ];
         $passwordNewRep = $_POST[ 'passwordnewrepeat' ];

         $resStatus = array();
         $actionsOK = true;

         // Check email validity.
         if ( ValidateEmail::validate( $email )) {

            $user->email = $email;

         } else {

            $resStatus[ 'invalidemailaddress' ] = $email;
            $actionsOK = false;

         }

         // Check email uniqueness.
         if ( $actionsOK && $email != $oldemail && User::registered( $email ) ) {

            $resStatus[ 'duplicateemailaddress' ] = $email;
            $actionsOK = false;

         }

         // Check and match old and new passwords.
         if ( !empty( $passwordOld ) && !empty( $passwordNew ) ) {

            if ( User::validatePassword( $passwordOld, $user->password ) ) {

               if ( $passwordNew == $passwordNewRep ) {

                  $user->password = crypt( $passwordNew );
                  $resStatus[ 'newpasswordsaved' ] = true;

               } else {

                  $resStatus[ 'nonmatchingpasswords' ] = true;
                  $actionsOK = false;

               }

            } else {

               $resStatus[ 'incorrectoldpassword' ] = true;
               $actionsOK = false;

            }

         }

         if ( $actionsOK ) {

            $user->save();

            // Update session data.
            $sessiondata = Session::get( 'logindata' );
            $sessiondata[ 'username' ] = $user->email;
            Session::set( 'logindata', $sessiondata );

         }

         return $resStatus;

      }

      /**
      * Save new user data from POST variables.
      *
      * @access      public
      * @param       object         $user       User object to be modified.
      * @return      array                      Status messages.
      * @author      Svein Arild Bergset <sab@interweb.no>
      * @version     0.2 - 04.01.2010 11:54
      * @since       01.08.2009 09:01
      *
      */
      private function saveUserData( $user ) {

         // User settings
         $firstName = $_POST[ 'firstname' ];
         $middleName = $_POST[ 'middlename' ];
         $lastName = $_POST[ 'lastname' ];
         $address = $_POST[ 'address' ];
         $address2 = $_POST[ 'address2' ];
         $zipCode = $_POST[ 'zipcode' ];
         $city = $_POST[ 'city' ];
         $country = $_POST[ 'country' ];
         $cellPhone = $_POST[ 'mobile' ];
         $phone = $_POST[ 'phone' ];
         $phoneNight = $_POST[ 'phone_night' ];
         $birthdate = $_POST[ 'birthday' ];
         $gender = $_POST[ 'gender' ];

         $resStatus = array();

         $user->firstname = $firstName;
         $user->middlename = $middleName;
         $user->lastname = $lastName;
         $user->streetAddress = $address;
         $user->secondStreetAddress = $address2;
         $user->zipcode = $zipCode;
         $user->city = $city;
         $user->country = $this->correspondingCountryId( $country );
         $user->cellphone = $cellPhone;
         $user->phone = $phone;
         $user->phonenight = $phoneNight;
         $user->birthdate = $birthdate;
         $user->gender = $gender;

         if ( $user->save() ) {

            $resStatus[ 'userdatasaved' ] = true;

         } else {

            $resStatus[ 'userdatanotsaved' ] = true;

         }

         return $resStatus;

      }

      public function correspondingCountryId( $iso = 'NO' ) {

         $countries = Settings::getSection( "countries" );
         return $countries[$iso]["id"];

      }

      /**
       * Get possible credits for this user
       *
       * @return array
       */
      private function credits() {

         return Credit::enum( Login::userid() );

      }

      /**
       * Get possible subscription for user
       *
       * @return array
       */
      private function subscription() {

         return Subscription::staticAsArray( Login::userid() );

      }

   }


?>