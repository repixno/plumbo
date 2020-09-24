<?php

   /**
    * get vcard from mailtoken
    *
    */

   import( 'pages.json' );
   import( 'math.zbase32' );

   class APIUserGetVcardByMailtoken extends JSONPage implements IValidatedView {
      
      /**
       * Validate
       *
       * @return Array
       */
      
      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'token' => VALIDATE_STRING
               ),
               'fields' => array(
                  'token' => VALIDATE_STRING
               )
            ),
            
         ); 
      
      }
      
      /**
       * get vcard by mailtoken
       *
       * @api-name user.getvcardbymailtoken
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result token String mailtoken
       */

      public function Execute( $token = '' ) {
         
         $token = $_POST['token'] ? $_POST['token'] : $token;
            
         try {
         
            $token = DB::query( 'SELECT token FROM site_user_mailtokens WHERE token = ?', $token )->fetchSingle();
            
            if ( isset( $token ) ) {
                  
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
               
               $this->message = 'Failed';
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