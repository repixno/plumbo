<?php

   /**
    * EDI writing wrapper class
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   config( 'website.order' );
   
   class OrderEDI {
   
      // Properties
      private $orderid              = null;
      private $deliveryinfo         = null;
      private $weight               = null;
      private $package              = 1;
      private $userid               = null;
      private $name                 = null;
      private $address1             = null;
      private $address2             = null;
      private $zipcode              = null;
      private $city                 = null;
      private $phonenr              = null;
      private $packagedescription   = 'Pakkebeskrivelse';
      private $email                = null;
   
      //Put this in a config file
      //$edikatalog="/data/global/edi";
      
      
      /**
       * Save the to a EDI file for later use
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function save() {

         $edipath = Settings::Get( 'paths', 'edi' );
         $edirow = "EUF$this->orderid,$this->deliveryinfo,$this->weight,$this->package,$this->userid,\"$this->name\",\"$this->address1\",\"$this->address2\",\"$this->zipcode\",\"$this->city\",\"$this->phonenr\",\"$this->packagedescription\",\"$this->email\"\n";
         
         try {
            
            $fp = @fopen( $edipath."/".$this->orderid.".txt", "w" );
            fwrite( $fp, $edirow, strlen( $edirow ) );
            fclose( $fp );
            
         } catch( Exception $e ) {

            // Something needs to be done here. Unclear what
            
         }

      }
      
      /**
       * Set the order id
       *
       * @param integer $orderid
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setOrderId( $orderid ) {
   
         $this->orderid = $orderid;
   
      }
   
   
      /**
       * Set the delivery info
       *
       * @param integer $deliveryinfo
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setDeliveryInfo( $deliveryinfo ) {
   
         $this->deliveryinfo = $deliveryinfo;
   
      }
   
   
      /**
       * Set the package weight
       *
       * @param float $weight
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setWeight( $weight ) {
   
         $this->weight = $weight;
   
      }
   
   
      /**
       * Set number of packages
       *
       * @param integer $package
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setPackage( $package ) {
   
         $this->package = $package;
   
      }
   
   
      /**
       * Set the user id
       *
       * @param integer $userid
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setUserId( $userid ) {
   
         $this->userid = $userid;
   
      }
   
   
      /**
       * Set the user name
       *
       * @param string $name
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setName( $name ) {
   
         $this->name = $name;
   
      }
   
   
      /**
       * Set the first address
       *
       * @param string $address1
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setAddress1( $address1 ) {
   
         $this->address1 = $address1;
   
      }
   
   
      /**
       * Set the second address
       *
       * @param string $address2
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setAddress2( $address2 ) {
   
         $this->address2 = $address2;
   
      }
   
   
      /**
       * Set the zip code
       *
       * @param string $zipcode
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setZipCode( $zipcode ) {
   
         $this->zipcode = $zipcode;
   
      }
   
   
      /**
       * Set the city
       *
       * @param string $city
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setCity( $city ) {
   
         $this->city = $city;
   
      }
   
   
      /**
       * Set the phone nr
       *
       * @param string $phonenr
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setPhoneNr( $phonenr ) {
   
         $this->phonenr = $phonenr;
   
      }
   
   
      /**
       * Set the package description
       *
       * @param string $packagedescription
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setPackageDescription( $packagedescription ) {
   
         $this->packagedescription = $packagedescription;
   
      }
      
      /**
       * Set the email address
       *
       * @param string $email
       */      
      public function setEmail( $email ){
         $this->email = $email;
      }
   }


?>