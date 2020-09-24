<?PHP

   model( 'user.index' );

   class DBCustomer extends DBUser implements ModelCaching {

      static $table = 'kunde';

      static $fields = array(
         'rubid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'namn' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 30,
            'null'    => true,
            'default' => null,
         ),
         'adresse1' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 30,
            'null'    => true,
            'default' => null,
         ),
         'adresse2' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 30,
            'null'    => true,
            'default' => null,
         ),
         'postnr' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 10,
            'null'    => true,
            'default' => null,
            'alias'   => 'zipcode',
         ),
         'stad' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 30,
            'null'    => true,
            'default' => null,
         ),
         'telefon' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 30,
            'default' => null,
            'null'    => true,
            'alias'   => 'telephone',
         ),
         'rabatt' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'kjonn' => array(
            'type'    => DB_TYPE_ENUM,
            'size'    => 11,
            'null'    => true,
            'default' => null,
            'constraints' => array(null,'M','K'),
            'alias'   => 'gender',
         ),
         'alder' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 10,
            'default' => null,
         ),
         'fodd' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 10,
            'null'    => true,
            'default' => null,
            'alias'   => 'birtdate',
         ),
         'newsletter' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
         'html' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
         'language' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 5,
            'null'    => true,
            'default' => null,
         ),
         'born'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'contactemail' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 256,
            'default' => '',
         ),
         'newsletter_others' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
          'newsletter_others_2' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
         'newsletter_others_3' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
           'newsletter_others_4' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
              'newsletter_repix' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
                 'newsletter_orkla' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
               'stabburet_annonser' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
            'stabburet_tilpassninger' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
           
         'newsletter_blacklist' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
         'telefon_kveld' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 15,
            'null'    => true,
            'default' => null,
            'alias'   => 'telephone_night',
         ),
         'telefon_mobil' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 15,
            'null'    => true,
            'default' => null,
            'alias'   => 'mobile',
         ),
         'country' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => 160,
         ),
         'fnavn'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => null,
         ),
         'mnavn'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => null,
         ),
         'enavn'    => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'null'    => true,
            'default' => null,
         ),
         'albumlist_style'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'profile_picture'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
            'alias'   => 'avatar',
         ),
         'show_profile_picture'  => array(
            'type'    => DB_TYPE_BOOLEAN,
            'default' => false,
         ),
         'first_buy'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'full_profile_at'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'apiid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'sort_asc' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'null'    => true,
            'default' => true,
         ),
         'sort_type' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 1,
         ),
         'krid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
      );

      public function getFullname() {

         return trim(trim($this->fnavn.' '.$this->mnavn).' '.$this->enavn);

      }

      public function setFullname( $fullname ) {

         $this->namn = $fullname;

         $names = explode( ' ', $fullname );
         if( count( $names ) > 2 ) {

            $this->firstname = array_shift($names);
            $this->middlename = array_shift($names);
            $this->lastname = implode( ' ', $names );

         } else if( count( $names ) > 1 ) {

            $this->firstname = array_shift($names);
            $this->middlename = '';
            $this->lastname = implode( ' ', $names );

         } else {

            $this->lastname = $fullname;
            $this->middlename = '';
            $this->firstname = '';

         }

      }


      public function getFirstName() {

         return $this->fnavn;

      }

      public function getMiddleName() {

         return $this->mnavn;

      }

      public function getLastName() {

         return $this->enavn;

      }


      public function getCity() {

         return $this->stad;

      }


      public function getStreetAddress() {

         return $this->adresse1;

      }

      public function getStreetAddress2() {

         return $this->adresse2;

      }


      /**
       * Set the firstname of customer
       *
       * @param string $firstName
       */
      public function setFirstName( $firstName ) {

         $this->fnavn = $firstName;

      }


      public function setMiddleName( $middleName ) {

         $this->mnavn = $middleName;

      }


      /**
       * Set the last name of customer
       *
       * @param string $lastName
       */
      public function setLastName( $lastName ) {

         $this->enavn = $lastName;

      }


      /**
       * Set the city of customer
       *
       * @param string $city
       */
      public function setCity( $city ) {

         $this->stad = $city;

      }


      /**
       * Set the street address of the customer
       *
       * @param string $streetaddress
       */
      public function setStreetAddress( $streetaddress ) {

         $this->adresse1 = $streetaddress;

      }

      /**
       * Set the street address of the customer
       *
       * @param string $streetaddress
       */
      public function setStreetAddress2( $streetaddress ) {

         $this->adresse2 = $streetaddress;

      }

      /**
       * Get the second street address
       *
       */
      public function getSecondStreetAddress() {

         return $this->adresse2;

      }


      /**
       * Set the second street address
       *
       * @param string $streetAddress
       */
      public function setSecondStreetAddress( $streetAddress ) {

         $this->adresse2 = $streetAddress;

      }


      /**
       * Set the zip code of the customer
       *
       * @param string $zipCode
       */
      public function setZipCode( $zipCode ) {

         $this->postnr = $zipCode;

      }


      /**
       * Get customer's zip code
       *
       * @return string
       */
      public function getZipCode() {

         return $this->postnr;

      }


      /**
       * Get user's cell nr
       *
       * @return string
       */
      public function getCellPhone() {

         return $this->telefon_mobil;

      }


      /**
       * Set customer's cell nr
       *
       * @param string $cellNr
       */
      public function setCellPhone( $cellNr ) {

         $this->telefon_mobil = $cellNr;

      }


      /**
       * Get customer's phonenr
       *
       * @return unknown
       */
      public function getPhone() {

         return $this->telefon;

      }


      /**
       * Set customer's phone nr
       *
       * @param unknown_type $phonenr
       */
      public function setPhone( $phonenr ) {

         $this->telefon = $phonenr;

      }


      /**
       * Get the night phone nr
       *
       * @return string
       */
      public function getPhoneNight() {

         return $this->telefon_kveld;

      }


      /**
       * Set the night phone nr
       *
       * @param string $phonenr
       */
      public function setPhoneNight( $phonenr ) {

         $this->telefon_kveld = $phonenr;

      }


      /**
       * Get user's birthdate
       *
       * @return unknown
       */
      public function getBirthDate() {

         return $this->born;

      }


      /**
       * Set user's birthdate
       *
       * @param string $birthdate
       */
      public function setBirthDate( $birthdate ) {

         $this->born = $birthdate;

      }

      public function getGender() {

         $gender = $this->fieldGet( 'kjonn' );

         switch( strtoupper( $gender ) ) {

            case 'K': return 'f'; break;
            case 'M': return 'm'; break;
            default: return null; break;

         }

      }

      public function setGender( $genderStr ) {

         switch( strtolower( $genderStr ) ) {

            case 'm':
            case 'male':
               $gender = 'M';
               break;
            case 'f':
            case 'female':
               $gender = 'K';
               break;
            default:
               $gender = null;
               break;
         }

         return $this->fieldSet( 'kjonn', $gender );

      }

      public function setAlbumSortOrder( $asc ) {

         $sortOrder = $asc;
         if ( is_string( $asc ) && in_array( $asc, Album::$validAlbumSortOrders ) ) {

            switch ( $asc ) {

               case 'asc':
                  $sortOrder = true; break;
               case 'desc':
                  $sortOrder = false; break;
               default:
                  $sortOrder = false; break;

            }

         }

         if ( is_bool( $sortOrder ) ) {

            return $this->fieldSet( 'sort_asc', $sortOrder );

         } else {

            return null;

         }

      }

      public function getAlbumSortOrder() {

         return $this->fieldGet( 'sort_asc' ) == 't' ? 'asc' : 'desc';

      }

      public function setAlbumSortType( $type ) {

         $sortType = $type;
         if ( is_string( $type ) && in_array( $type, Album::$validAlbumSortTypes ) ) {

            $sortType = array_search( $type, Album::$validAlbumSortTypes );

         } else if ( is_int( $type ) && $type > 0 && $type < sizeof( Album::$validAlbumSortTypes ) ) {

            $sortType = $type;

         }

         if ( is_int( $sortType ) ) {

            return $this->fieldSet( 'sort_type', $sortType );

         } else {

            return null;

         }

      }

      public function getAlbumSortType() {

         return Album::$validAlbumSortTypes[ $this->fieldGet( 'sort_type' ) ];

      }

   }

?>