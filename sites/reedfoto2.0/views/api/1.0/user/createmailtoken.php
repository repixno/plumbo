<?php

   /**
    * Create mailtoken
    *
    */

   import( 'pages.json' );
   import( 'math.zbase32' );

   class APIUserCreateMailtoken extends JSONPage implements IValidatedView {
      
      /**
       * Validate
       *
       * @return Array
       */
      
      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'clear' => VALIDATE_INTEGER,
                  'vcard' => VALIDATE_INTEGER
               ),
               'fields' => array(
                  'clear' => VALIDATE_INTEGER,
                  'vcard' => VALIDATE_INTEGER
               )
            ),
            
         ); 
      
      }
      
      /**
       * Create mailtoken
       *
       * @api-name user.createmailtoken
       * @api-post-optional clear Integer clears existing tokens for user (1 or 0)
       * @api-param-optional clear Integer clears existing tokens for user (1 or 0)
       * @api-post-optional vcard Integer returns vcard download (1 or 0)
       * @api-param-optional vcard Integer returns vcard download (1 or 0)
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result token String mailtoken
       * @api-result email String email
       */

      public function Execute( $clear = 0, $vcard = 0 ) {
         
         $clear = $_POST['clear'] ? $_POST['clear'] : $clear;
         $vcard = $_POST['vcard'] ? $_POST['vcard'] : $vcard;
         
         try {
             
            if ( $clear ) {
               
               DB::query( 'DELETE FROM site_user_mailtokens WHERE userid = ?', Login::userid() );
               
            }
            
            $loop = 10;
            
            $i = 0;
            
            while ( $i < $loop ) {
         
               $token = rand( 111111111, 999999999 ).rand( 111111111, 999999999 );
               
               $userid = (int) DB::query( 'SELECT userid FROM site_user_mailtokens WHERE token = ?', $token )->fetchSingle();
               
               if ( $userid <= 0 ) {
                  
                  $i = $loop;
                  
               } else {
                  
                  unset( $token );
                  
                  $i++;
                  
               }
               
            }
            
            if ( isset( $token ) ) {
                  
               DB::query( 'INSERT INTO site_user_mailtokens (userid, token, created) VALUES (?, ?, ?)', Login::userid(), $token, date( 'Y-m-d H:i:s' ) );
               
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
