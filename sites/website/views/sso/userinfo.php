<?php

   import( 'pages.xml' );
   import( 'math.uuid' );
   import( 'website.user' );
   import( 'website.login' );
   
   config( 'website.countries' );
   
   define( 'LOGINERROR', 11 );
   define( 'LOGINERRORMESSAGE', 'Invalid login' );
   
   define( 'EXCEPTIONERROR', 20 );
   
   /**
    * SSO User info
    *
    * @author Eivind Moland <eivind@eivind.biz>
    */

   class SingleSignOnUserInfo extends XMLPage implements IView {
      
      public function Execute() {
      
         $this->setRootNode( 'userinfo' );
         
         $this->resetFields();
         
         // mandatory blank fields if anything fails
         
         $this->content = array(
            'email' => '',
            'sec_question' => '',
            'sec_answer' => '',
            'salutation' => '',
            'firstname' => '',
            'lastname' => '',
            'street' => '',
            'zip' => '',
            'city' => '',
            'country' => '',
            'iso_country' => '',
            'phone' => '',
            'ceccularphone' => '',
            'company_name' => '',
            'state_province' => '',
            'customer_card' => '',
            'fax' => '',
            'imei' => '',
            'favorite_loc_id' => '',
            'accept_news_letter' => ''
         );

         try {
            
            $xmlstring = file_get_contents('php://input');            
            $xml = simplexml_load_string( $xmlstring );
            
            $sessionid = (string)$xml->ocsid;
            
            $sessiondata = unserialize( sess_read( $sessionid ) );
                             
            if ( Login::byUserId( $sessiondata[ 'userid' ] ) ) {
               
               $user = new User ( Login::userid() );
               
               if ( $user->isLoaded() && $user instanceof User ) {
               
                  $countries = Settings::GetSection( 'efcountries' );
                  
                  $country = $countries[ $user->country ]; 
   
                  $this->content = array(
                     'email' => $user->username,
                     'sec_question' => $user->getPreference( 'secquestion' ),
                     'sec_answer' => $user->getPreference( 'secanswer' ),
                     'salutation' => $user->getPreference( 'salutation' ),
                     'firstname' =>  $user->firstname,
                     'lastname' => $user->lastname,
                     'street' => $user->streetaddress,
                     'zip' => $user->zipcode,
                     'city' => $user->city,
                     'country' => $country['name'],
                     'iso_country' => $country['2char'],
                     'phone' => $user->phone,
                     'cellularphone' => $user->cellphone,
                     'company_name' => $user->getPreference( 'companyname' ),
                     'state_province' => $user->getPreference( 'stateprovince' ),
                     'customer_card' => $user->getPreference( 'customercard' ),
                     'fax' => $user->getPreference( 'fax' ),
                     'imei' => $user->getPreference( 'imei' ),
                     'favorite_loc_id' => '',//$user->getPreference( 'favoritelocid' ),
                     'accept_news_letter' => $user->newsletter ? 'yes' : 'no'
                  );
                              
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

          } catch ( Exception $e ) {
               
            $this->head = array(
               'errorcode' => EXCEPTIONERROR,
               'errortext' => $e->getMessage()
            );
            
         }
         
      }
      
   }
?>
