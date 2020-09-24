<?php

   import( 'pages.xml' );
   import( 'math.uuid' );
   import( 'website.user' );
   import( 'website.login' );
   
   config( 'website.countries' );
   
   define( 'LOGINERROR', 11 );
   define( 'LOGINERRORMESSAGE', 'Invalid login' );
   
   define( 'EXCEPTIONERROR', 20 );
   
   define( 'USEREXISTERROR', 8 );
   define( 'USEREXISTSERRORMESSAGE', 'User already exists' );
   
   define( 'MISSINGFIELDSERROR', 2 );
   define( 'MISSINGFIELDSERRORMESSAGE', 'Mandatory fields missing (firstname, lastname and/or email)' );
   
   /**
    * SSO User change
    *
    * @author Eivind Moland <eivind@eivind.biz>
    */

   class SingleSignOnUserChange extends XMLPage implements IView {
      
      public function Execute() {

         try {
            
            $this->setRootNode( 'userchange' );
            
            $this->resetFields();
            
            $xmlstring = file_get_contents('php://input');            
            $xml = simplexml_load_string( $xmlstring );
            
            $sessionid = (string)$xml->ocsid;
            $email = (string)$xml->email;
            $secquestion = (string)$xml->sec_question;
            $secanswer = (string)$xml->sec_answer;
            $salutaction = (string)$xml->salutation;
            $firstname = (string)$xml->firstname;
            $lastname = (string)$xml->lastname;
            $street = (string)$xml->street;
            $zip = (string)$xml->zip;
            $city = (string)$xml->city;
            $country = (string)$xml->country;
            $isocountry = (string)$xml->iso_country;
            $phone = (string)$xml->phone;
            $cellphone = (string)$xml->cellularphone;
            $companyname = (string)$xml->company_name;
            $stateprovince = (string)$xml->state_province;
            $customercard = (string)$xml->customer_card;
            $fax = (string)$xml->fax;
            $imei = (string)$xml->imei;
            $favoritelocid = '';
            $acceptnewsletter = (bool)$xml->accept_news_letter;
            $password = (string)$xml->pwd;
            
            $sessiondata = unserialize( sess_read( $sessionid ) );
               
            if ( !empty( $sessiondata[ 'keyaccountid' ] ) && ( ! empty( $sessiondata['userid'] ) ) ) {
               
               if ( Login::byUserId( $sessiondata[ 'userid' ] ) ) {
                  
                  $user = new User( Login::userid() );
                  
                  if ( $user->isLoaded() && $user instanceof User ) {
                     
                     $countries = Settings::GetSection( 'efcountries' );
                        
                     try{
                        foreach ( array_keys( $countries ) as $key ) {
                           $country = $countries[ $key ];
                              
                           if ( strtolower( $country['2char'] ) == strtolower( $isocountry ) ) {
                              $countryid = $key; 
                                 
                              break;
                           }
                        }
                     }catch ( Exception $e ){
                        
                        mail( 'tor.inge@eurofoto.no', "SSO bug" , $e->getMessage() . serialize( $isocountry )  . serialize( $country ) );
                        
                     }
                     
                     // should you be able to change username ?
                     if ( !empty( $email ) ) $user->username = $email;
                     
                     if ( !empty( $password ) ) $user->password = crypt( $password );
         
                     if ( !empty( $firstname ) ) $user->firstname = $firstname;
                     if ( !empty( $lastname ) ) $user->lastname = $lastname;
                     if ( !empty( $isocountry ) ) $user->country = $countryid;
                     //if ( !empty( $email ) ) $user->contactemail = $email;
                     if ( !empty( $street ) ) $user->streetaddress = $street;
                     if ( !empty( $xml->accept_news_letter ) ) $user->newsletter = $acceptnewsletter;
                     if ( !empty( $phone ) ) $user->phone = $phone;
                     if ( !empty( $zip ) ) $user->zipcode = $zip;
                     if ( !empty( $city ) ) $user->city = $city;
                     if ( !empty( $cellphone ) ) $user->cellphone = $cellphone;
                        
                     $user->save();
                     
                     // store all other values in user preferences
                        
                     if ( !empty( $favoritelocid ) ) $user->setPreference( 'favoritelocid', $favoritelocid );
                     if ( !empty( $imei ) ) $user->setPreference( 'imei', $imei );
                     if ( !empty( $fax ) ) $user->setPreference( 'fax', $fax );
                     if ( !empty( $stateprovince ) ) $user->setPreference( 'stateprovince', $stateprovince );
                     if ( !empty( $secanswer ) ) $user->setPreference( 'secanswer', $secanswer );
                     if ( !empty( $companyname ) ) $user->setPreference( 'companyname', $companyname );
                     if ( !empty( $secquestion ) ) $user->setPreference( 'secquestion', $secquestion );
                     if ( !empty( $customercard ) ) $user->setPreference( 'customercard', $customercard );
                     if ( !empty( $sessiondata[ 'keyaccountid' ] ) ) $user->setPreference( 'keyaccountid', $sessiondata[ 'keyaccountid' ] );
                                   
                     $this->head = array(
                        'errorcode' => 0,
                        'errortext' => ''
                     );
                        
                  } else {

                     $this->head = array(
                        'errorcode' => LOGINERROR,
                        'errortext' => LOGINERRORMESSAGE
                     );
                  }
                  
               } else {

                  $this->head = array(
                     'errorcode' => LOGINERROR,
                     'errortext' => LOGINERRORMESSAGE
                  );
               }
               
            } else {

               $this->head = array(
                  'errorcode' => LOGINERROR,
                  'errortext' => LOGINERRORMESSAGE
               );

            }
               
         } catch ( Exception $e ) {
               
           $this->head = array(
              'errorcode' => EXCEPTIONERROR,
              'errortext' => $e->getMessage()
           );
            
         }
      }
   }
?>
