<?php

   /**
    * API to toggle some settings for a user.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'website.smsservice' );

   class APIUserToggle extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            "newsletter" => array(
               "get" => array(
                  "value" => VALIDATE_STRING
               ),
               'post' => array(
                  'value' => VALIDATE_STRING,
               )
            ),
            "sms" => array(
               "fields" => array(
                  VALIDATE_STRING
                  ),
               "get" => array(
                  "value" => VALIDATE_STRING
               ),
               'post' => array(
                  'value' => VALIDATE_STRING,
               )
            )
         );

      }

      /**
       * Toggle newsletter setting
       * 
       * @api-name user.toggle.newsletter
       * @api-auth required
       * @api-get-optional value Boolean Toggle value. 'true' or 'false'.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function newsletter() {

         $toggle = $_POST['value'] ? $_POST['value'] : $_GET[ 'value' ];

         $user = new User( Login::userid() );
         if ( $toggle == 'false' || $toggle == 'true' ) {

            $user->newsletter = $toggle == 'false' ? false : true;

         } else {

            $toggle = $user->newsletter ? false : true;
            $user->newsletter = $toggle;

         }
         $user->save();

         $this->result = $toggle;
         $this->message = 'OK';

      }

      /**
       * Toggle SMS setting
       * 
       * @api-name user.toggle.sms
       * @api-auth required
       * @api-param-optional type String Toggle type. 'sharephoto' or 'ordershipping'.
       * @api-get-optional value Boolean Toggle value. 'true' or 'false'.
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */   
      public function sms( $type = null ) {

         $toggleStr = $_POST['value'] ? $_POST['value'] : $_GET[ 'value' ];

         $toggle = null;
         if ( $toggleStr == 'false' || $toggleStr == 'true' ) {

            $toggle = $toggleStr == 'false' ? false : true;

         } else {

            $this->result = null;
            $this->message = 'Error';

         }

         $smsService = SMSService::fromUser( Login::userid() );

         if ( is_bool( $toggle ) ) {

            if ( !$smsService->isLoaded() ) {

               $smsService->userid = Login::userid();
               $smsService->created = date( 'c' );

            }

            if ( $type == 'sharephoto' ) {

               if ( $toggle === true ) {

                  if ( is_null( $smsService->sharing_notice ) ) {

                     $smsService->subscribeSharing();

                  }

               } else {

                  $smsService->unSubscribeSharing();

               }

               $this->result = $toggle;
               $this->message = 'OK';

            } else if ( $type == 'ordershipping' ) {

               if ( $toggle === true ) {

                  if ( is_null( $smsService->order_sent_notice ) ) {

                     $smsService->subscribeOrder();

                  }

               } else {

                  $smsService->unSubscribeOrder();

               }

               $this->result = $toggle;
               $this->message = 'OK';

            } else {

               $this->result = null;
               $this->message = 'Error';

            }

            $smsService->save();

         }

      }

   }

?>
