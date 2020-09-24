<?php

   /**
    * API for friends related stuff.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );

   class APIUserFriends extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'check' => array(
               'fields' => array(
                  VALIDATE_INTEGER
               ),
               'post' => array(
                  'code' => VALIDATE_STRING
                  )
            )
         );

      }

      /**
       * Check discount code
       * 
       * @api-name user.discount
       * @api-auth required
       * @api-param-optional code String Discount code
       * @api-post-optional code String Discount code
       * @api-result response Array 'multiplediscountsaved' (true/false), 'singlediscountsaved' (true/false), 'unknowndiscountcode' (true/false). 'missingcode' (true/false)
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function check( $code ) {

         $discountCode = isset( $code ) ? $code : $_POST[ 'code' ];

         $user = new User( Login::userid() );

         $resStatus = array();

         // Checking and register discount code.
         if ( !empty( $discountCode ) ) {

            $status = $user->checkDiscount( $discountCode );

            switch ( $status ) {

               case 2:
                  $resStatus[ 'multiplediscountsaved' ] = true;
                  break;
               case 1:
                  $resStatus[ 'singlediscountsaved' ] = true;
                  break;
               case 0:
                  $resStatus[ 'unknowndiscountcode' ] = true;
                  break;

            }

         } else {

            $resStatus[ 'missingcode' ] = true;
            
            $this->result = false;

         }

         $this->response = $resStatus;
         $this->result = true;
		   $this->message = 'OK';

      }

   }

?>
