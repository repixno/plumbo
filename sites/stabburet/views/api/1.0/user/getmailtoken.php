<?php

   /**
    * get mailtoken
    *
    */

   import( 'pages.json' );
   import( 'math.zbase32' );

   class APIUserGetMailtoken extends JSONPage implements IValidatedView {
      
      /**
       * Validate
       *
       * @return Array
       */
      
      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'vcard' => VALIDATE_INTEGER
               ),
               'fields' => array(
                  'vcard' => VALIDATE_INTEGER
               )
            ),
            
         ); 
      
      }
      
      /**
       * get mailtoken
       *
       * @api-name user.getmailtoken
       * @api-post-optional vcard Integer returns vcard download (1 or 0)
       * @api-param-optional vcard Integer returns vcard download (1 or 0)
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result token String mailtoken
       * @api-result email String email
       */

      public function Execute( $vcard = 0 ) {
         
         $vcard = $_POST['vcard'] ? $_POST['vcard'] : $vcard;
            
         try {
         
            $token = DB::query( 'SELECT token FROM site_user_mailtokens WHERE userid = ?', Login::userid() )->fetchSingle();

            if ( isset( $token ) ) {
                  
               if ( $vcard ) {
                  
                  $vcardcontent =  sprintf( "BEGIN:VCARD\n".
                                    "VERSION:3.0\n".
                                    "N:%s\n".
                                    "FN:%s\n".
                                    "ORG:%s\n".
                                    "TITLE:%s\n".
                                    "EMAIL;TYPE=PREF,INTERNET:%s\n".
                                    "END:VCARD\n", 'Eurofoto - last opp bilder', 'Eurofoto - last opp bilder', 'Eurofoto AS', 'Eurofoto - last opp bilder', sprintf( '%s@%s', zBase32::encode( $token ), Settings::GET( 'email', 'uploadhost', 'opplasting.eurofoto.no' ) ) );

                  header('Content-type: text/x-vCard'); 
                  header('Content-Disposition: attachment; filename=eurofoto.vcf'); 
                  header('Pragma: public');
                  
                  die( $vcardcontent );
                                    
               } else {

                  $this->token = $token;
                  $this->email = sprintf( '%s@%s', zBase32::encode( $token ), Settings::GET( 'email', 'uploadhost', 'opplasting.eurofoto.no' ) );

                  
               }
               
               $this->result = true;
               $this->message = 'OK';
               
            } else {
               
               $this->message = 'No mailtoken for user';
               $this->result = false;
               
            }
            
         } catch ( Exception $e ) {
         
            $this->message = 'Failed';
            $this->exception = $e->getMessage();
            $this->result = false;
               
         }
         
      }
   } 

?>