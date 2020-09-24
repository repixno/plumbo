<?php

   
   /**
    * 
    * @author Tor Inge
    * 
    */

   import( 'pages.json' );

   class CreateTopText extends JSONPage implements NoAuthRequired, IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'get' => array(
                  'cemail' => VALIDATE_STRING
               )
            )
         );
      }

      /**
       * Execute
       * 
       * @author Tor Inge Løvland
       */
      public function Execute() {

         // Properties set by user
         $email  = isset( $_GET['cemail'] ) ? $_GET['cemail'] : '';
         $tidspunkt = date( 'Y-m-d H:i:s', strtotime( date( 'Y-m-d H:i:s' ) . '-1 day') );
         $count = DB::query( 'SELECT count(*) FROM kunde k, brukar b WHERE k.uid = b.uid AND k.contactemail ilike ? AND b.registrert >?', json_decode ( $email ),  $tidspunkt )->fetchSingle();

         if( $count >= 5 ){
            return $this->returnSingleValue( false );
         }else{
            return $this->returnSingleValue( true );
         }

      }
      
      

      
      
   }



?>