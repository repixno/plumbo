<?php
   /**
    * @author Andr� Nordstrand
    *
    *
    */


   import( 'core.model' );

   class DBUserFriends extends Model {

      static $table = 'friendslist';

      static $fields = array(

         'uid'       => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
         ),
         'friend'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
         ),
         'brukarnamn'=> array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
         ),
         'alias'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'div_info'  => array(
            'type'      => DB_TYPE_DATETIME,
            'null' => true,
            'default' => null
         ),
         'fnamn'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'mnamn'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'enamn'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'mobilnr'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'adress'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'postnr'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'ort'       => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'telefonnr'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
         ),
         'land'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null' => true,
            'default' => null
         ),

      );

      public function getUserId() {

         return $this->uid;

      }

      public function setUserId( $userid ) {

         return $this->uid = (int) $userid;

      }

      public function getFriendId() {

         return $this->friend;

      }

      public function setFriendId( $id ) {

         return $this->friend = (int) $id;

      }

      public function getUserName() {

         return $this->brukarnamn;

      }

      public function setUserName( $username ) {

         return $this->brukarnamn = $username;

      }

      public function getCreationDate() {

         return $this->div_info;

      }


      public function setCreationDate( $date ) {

         return $this->div_info = $date;

      }

      public function getNickName() {

         return $this->alias;

      }

      public function setNickName( $nickname ) {

         return $this->alias = $nickname;

      }

      public function getFirstName() {

         return $this->fnamn;

      }

      public function setFirstName( $firstname ) {

         return $this->fnamn = $firstname;

      }

      public function getMiddleName() {

         return $this->mnamn;

      }

      public function setMiddeName( $middlename ) {

         return $this->mnamn =  $middlename ;

      }

      public function getSurName() {

         return  $this->enamn;

      }

      public function setSurName( $surname ) {

         return $this->enamn = $surname;

      }

      public function getAddress() {

         return  $this->adress;

      }

      public function setAddress( $address ) {

         return $this->adress =  $address;

      }

      public function getPostOffice() {

         return $this->ort;

      }

      public function setPostOffice( $postoffice ) {

         return $this->ort = $postoffice;

      }

      public function getZipcode() {

         return $this->postnr;

      }

      public function setZipcode( $zipcode ) {

         return $this->postnr = $zipcode;

      }

      public function getCellphoneNumber() {

         return $this->mobilnr;

      }

      public function setCellphoneNumber( $number ) {

         return $this->mobilnr = $number;

      }

      public function getPhoneNumber() {

         return $this->telefonnr;

      }

      public function setPhoneNumber( $number ) {

         return $this->telefonnr = $number;

      }

      // get_choices_country_choices() /old/webside/choices.inc
      public function getCountryChoice() {

         return $this->land;

      }

      public function setCountryCode( $country ) {

         return $this->land = $country;

      }


   }

?>