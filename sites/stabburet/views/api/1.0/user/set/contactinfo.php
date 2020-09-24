<?php


   /**
    * 
    * Save user's contact info
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );
   config( 'website.countries' );
   model( 'user.smsservices' );

   class APISetContactInfo extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'fullname'  => VALIDATE_STRING,
                  'address'   => VALIDATE_STRING,
                  'zipcode'   => VALIDATE_STRING,
                  'city'      => VALIDATE_STRING,
                  'country'   => VALIDATE_STRING,
                  'phone'     => VALIDATE_STRING,
                  'cellphone' => VALIDATE_STRING,
               ),
               'get' => array(
                  'fullname'  => VALIDATE_STRING,
                  'address'   => VALIDATE_STRING,
                  'zipcode'   => VALIDATE_STRING,
                  'city'      => VALIDATE_STRING,
                  'country'   => VALIDATE_STRING,
                  'phone'     => VALIDATE_STRING,
                  'cellphone' => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      public function Execute() {
         
         $fullname   = $_POST['fullname'] ? $_POST['fullname'] : $_GET['fullname'];
         $address    = $_POST['address'] ? $_POST['address'] : $_GET['address'];
         $zipcode    = $_POST['zipcode'] ? $_POST['zipcode'] : $_GET['zipcode'];
         $city       = $_POST['city'] ? $_POST['city'] : $_GET['city'];
         $country    = $_POST['country'] ? $_POST['country'] : $_GET['country'];
         $country    = $this->getCountryId( $country );
         $phone      = $_POST['phone'] ? $_POST['phone'] : $_GET['phone'];
         $cellphone  = $_POST['cellphone'] ? $_POST['cellphone'] : $_GET['cellphone'];
         
         try {
            
            $user = new User( Login::userid() );
            $user->fullname      = $fullname;
            $user->streetaddress = $address;
            $user->zipcode       = $zipcode;
            $user->city          = $city;
            $user->country       = $country;
            $user->phone         = $phone;
            $user->cellphone     = $this->checkValidatedCellNr( $cellphone );
            $user->save();
            
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed to init user';
            return false;
            
         }
         
         
      }
      
      
      private function checkValidatedCellNr( $nr ) {
         
         try {
            
            if( strlen( $nr ) > 0 ) {
               $service = DBSmsServices::fromUserid( Login::userid() );
               if( $service->cellnr != $nr ) {
                  $service->validated = null;
                  $service->order_sent_notice = null;
                  $service->sharing_notice = null;
                  $service->save();
               }
            }
            
         } catch( Exception $e ) {
            
            return $nr;
            
         }
         
         return $nr;
         
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