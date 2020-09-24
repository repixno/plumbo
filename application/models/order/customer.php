<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'core.model' );
   
   class DBOrderCustomer extends Model {
      
      static $table = 'historie_kunde';
      
      static $fields = array(
         'ordrenr'   => array(
            'primary' => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => null,
         ),
         'uid'       => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => true,
            'default'   => null,
         ),
         'epost'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'navn'      => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'adresse1'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'adresse2'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'postnr'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'sted'      => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'land'      => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'telefon'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'mphone'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'depost'    => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dnavn'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dadresse1' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dadresse2' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dpostnr'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dsted'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dland'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dtelefon'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'dmphone'   => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
      );

      
      
      /**
       * Set the orderid of this order.
       *
       * @param integer $orderid
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setOrderId( $orderid ) {
         
         $this->ordrenr = $orderid;
         
      }
      
      
      
      /**
       * Get the orderid of this order
       *
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getOrderId() {
         
         return $this->ordrenr;
         
      }
      
      
      /**
       * Set the user id of this order
       *
       * @param integer $userid
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setUserId( $userid ) {
         
         $this->uid = $userid;
         
      }
      
      
      
      /**
       * Get the user id of this order
       *
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getUserId() {
         
         return $this->uid;
         
      }
      
      
      
      /**
       * Set the contact's email
       *
       * @param string $email address to contact
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactEmail( $email ) {
         
         $this->epost = $email;
         
      }
      
      
      
      /**
       * Get the email address of contact
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactEmail() {
         
         return $this->epost;
         
      }
      
      
      
      /**
       * Set the contact name
       *
       * @param string $name
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactName( $name ) {
         
         $this->navn = $name;
         
      }
      
      
      
      /**
       * Get the contact's name
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactName() {
         
         return $this->navn;
         
      }
      
      
      
      /**
       * Set the first address og contact
       *
       * @param string $address
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactFirstAddress( $address ) {
         
         $this->adresse1 = $address;
         
      }
      
      
      
      /**
       * Get the first address of contact
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactFirstAddress() {
         
         return $this->adresse1;
         
      }
      
      
      
      /**
       * Set the seconf address og contact
       *
       * @param string $address
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactSecondAddress( $address ) {
         
         $this->adresse2 = $address;
         
      }
      
      
      
      /**
       * Get the second address of contact
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactSecondAddress() {
         
         return $this->adresse1;
         
      }
      
      
      
      /**
       * Set the contact zip code
       *
       * @param string $zipcode
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactZipCode( $zipcode ) {
         
         $this->postnr = $zipcode;
         
      }
      
      
      
      /**
       * Get the contact zip code
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactZipCode() {
         
         return $this->postnr;
         
      }
      
      
      
      /**
       * Set the contact city
       *
       * @param string $city
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactCity( $city ) {
         
         $this->sted = $city;
         
      }
      
      
      
      /**
       * Get the contact city
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactCity() {
         
         return $this->city;
         
      }
      
      
      
      /**
       * Set the contact's country
       *
       * @param string $country
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactCountry( $country ) {
         
         $this->land = $country;
         
      }
      
      
      
      /**
       * Get the contact's country
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactCountry() {
         
         return $this->land;
         
      }
      
      
      
      /**
       * Set the contact's phone nr
       *
       * @param string $phonenr
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactPhoneNr( $phonenr ) {
         
         $this->telefon = $phonenr;
         
      }
      
      
      
      /**
       * Get the contact's phone nr
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactPhoneNr() {
         
         return $this->telefon;
         
      }
      
      
      
      /**
       * Set the contact's cell nr
       *
       * @param string $cellnr
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setContactCellNr( $cellnr ) {
         
         $this->mphone = $cellnr;
         
      }
      
      
      
      /**
       * Get the contact's cell nr
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactCellNr() {
         
         return $this->mphone;
         
      }
      
      
      
      /**
       * Set the delivery contact's email
       *
       * @param string $email address to contact
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryEmail( $email ) {
         
         $this->depost = $email;
         
      }
      
      
      
      /**
       * Get the delivery email address of contact
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryEmail() {
         
         return $this->depost;
         
      }
      
      
      
      /**
       * Set the delivery contact name
       *
       * @param string $name
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryName( $name ) {
         
         $this->dnavn = $name;
         
      }
      
      
      
      /**
       * Get the delivery contact's name
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryName() {
         
         return $this->dnavn;
         
      }
      
      
      
      /**
       * Set the first address of delivery contact
       *
       * @param string $address
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryFirstAddress( $address ) {
         
         $this->dadresse1 = $address;
         
      }
      
      
      
      /**
       * Get the first address of delivery contact
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryFirstAddress() {
         
         return $this->dadresse1;
         
      }
      
      
      
      /**
       * Set the second address of delivery contact
       *
       * @param string $address
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliverySecondAddress( $address ) {
         
         $this->dadresse2 = $address;
         
      }
      
      
      
      /**
       * Get the second address of delivery contact
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliverySecondAddress() {
         
         return $this->dadresse2;
         
      }
      
      
      
      /**
       * Set the delivery contact zip code
       *
       * @param string $zipcode
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryZipCode( $zipcode ) {
         
         $this->dpostnr = $zipcode;
         
      }
      
      
      
      /**
       * Get the delivery contact zip code
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryZipCode() {
         
         return $this->dpostnr;
         
      }
      
      
      
      /**
       * Set the delivery contact city
       *
       * @param string $city
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryCity( $city ) {
         
         $this->dsted = $city;
         
      }
      
      
      
      /**
       * Get the delivery contact city
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryCity() {
         
         return $this->dcity;
         
      }
      
      
      
      /**
       * Set the delivery contact's country
       *
       * @param string $country
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryCountry( $country ) {
         
         $this->dland = $country;
         
      }
      
      
      
      /**
       * Get the delivery contact's country
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryCountry() {
         
         return $this->dland;
         
      }
      
      
      
      /**
       * Set the delivery contact's phone nr
       *
       * @param string $phonenr
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryPhoneNr( $phonenr ) {
         
         $this->dtelefon = $phonenr;
         
      }
      
      
      
      /**
       * Get the delivery contact's phone nr
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryPhoneNr() {
         
         return $this->dtelefon;
         
      }
      
      
      
      /**
       * Set the delivery contact's cell nr
       *
       * @param string $cellnr
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryCellNr( $cellnr ) {
         
         $this->dmphone = $cellnr;
         
      }
      
      
      
      /**
       * Get the delivery contact's cell nr
       *
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryCellNr() {
         
         return $this->dmphone;
         
      }
      
   }


?>