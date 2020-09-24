<?php

   import( 'pages.xml' );
   import( 'math.uuid' );
   import( 'website.user' );
   import( 'website.login' );
   
   config( 'website.countries' );
   
   define( 'LOGINERROR', 11 );
   define( 'LOGINERRORMESSAGE', 'Invalid login' );
   
   define( 'EXCEPTIONERROR', 20 );
   
   define( 'USEREXISTSERROR', 8 );
   define( 'USEREXISTSERRORMESSAGE', 'This Email address already exists' );

   define( 'MISSINGFIELDSERROR', 2 );
   define( 'MISSINGFIELDSERRORMESSAGE', 'Mandatory fields missing (firstname, lastname, email and/or password)' );
   
   /**
    * SSO User add
    *
    * @author Eivind Moland <eivind@eivind.biz>
    */

   class SingleSignOnUserAdd extends XMLPage implements IView {
      
      public function Execute() {

         try {
            
            $this->setRootNode( 'useradd' );
            
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
            $favoritelocid = '';//(string)$xml->favorite_loc_id;
            $acceptnewsletter = (bool)$xml->accept_news_letter;
            $password = (string)$xml->pwd;
            
            $sessiondata = unserialize( sess_read( $sessionid ) );
             
            if ( ! empty( $sessiondata[ 'keyaccountid' ] ) ) {
               
               if ( ( !empty( $email ) ) && ( !empty( $firstname ) ) && ( !empty( $lastname ) ) && ( !empty( $password ) ) ) {
                  
                  $user = User::fromUsername( $email );

                  if ( $user instanceof DBUser ) {
                     
                     $this->head = array(
                        'errorcode' => USEREXISTSERROR,
                        'errortext' => USEREXISTSERRORMESSAGE
                     );
                    
                  } else {
                    
                     $user = new User();

                     $countries = Settings::GetSection( 'efcountries' );
                     
                     foreach ( array_keys( $countries ) as $key ) {
                        $country = $countries[ $key ];
                        
                        if ( ( strtolower( $country['2char'] ) == strtolower( $isocountry ) ) || ( strtolower( $country['name'] ) == strtolower( $country ) ) ) {
                           $countryid = $key; 
                           
                           break;
                        }
                     }
                  
                     $user->username = $email;
                     $user->password = crypt( $password );
      
                     $user->firstname = $firstname;
                     $user->lastname = $lastname;
                     $user->country = $countryid;
                     //$user->contactemail = $email;
                     $user->streetaddress = $street;
                     $user->newsletter = $acceptnewsletter;
                     $user->phone = $phone;
                     $user->zipcode = $zip;
                     $user->city = $city;
                     $user->cellphone = $cellphone;
                     
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
                     
                     // login newly created user
                     
                     if ( Login::byUserId( $user->id ) ) {
                        
                        $newsessiondata = serialize( array( 'keyaccountid' => $sessiondata[ 'keyaccountid' ], 'userid' => Login::userid() ) );
                        sess_write( $sessionid, $newsessiondata );
                        
                     }
                                
                     $this->head = array(
                        'errorcode' => 0,
                        'errortext' => ''
                     );

                  }
                     
               } else {
                  
                  $this->head = array(
                     'errorcode' => MISSINGFIELDSERROR,
                     'errortext' => MISSINGFIELDSERRORMESSAGE
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
