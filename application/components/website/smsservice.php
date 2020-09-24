<?php

   model( 'user.smsservices' );

   class SMSService extends DBSmsServices {

      static function fromUser( $uid ) {

         $sId = DB::query( "SELECT id FROM user_sms_services WHERE uid=?", $uid )->fetchSingle();
         return new SMSService( $sId );

      }

      public function validated() {

         if( isset( $this->validated ) ) {

            return true;

         }

         return false;

      }


   }


?>