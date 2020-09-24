<?php

   import( 'website.userfriend' );
   import( 'website.usergroup' );
   
   import( 'pages.protected' );
   
   class AccountFriendsImportFacebook extends ProtectedPage implements IValidatedView {

      protected $template = 'account.friends.index';
      
      /**
       * Validator
       * 
       * @return array array of fields
       *
       */

		public function Validate() {

         return array(
            'execute' => array( 
               'get' => array(
                  'access_token' => VALIDATE_STRING,
               )
            )
         );
         
		}
		
		/**
		 * Execute
		 * https://graph.facebook.com/oauth/authorize?type=user_agent&client_id=123026291052567&redirect_uri=http://mercedes.eurofoto.no/account/friends/importfacebook&scope=friends_about_me,offline_access
		 */
		
      function Execute( ) {
         
         $token = $_GET[ 'access_token' ];
         
         if ( empty( $token ) ) {
            
            die(  '<script type="text/javascript">' .
                  'var pathArray = window.location.href.split( "#" );' .
                  'window.location.href = "/account/friends/importfacebook/?" + (pathArray[1]);' .
                  '</script>' );
                  
         } else {
            
            $friends = array();
            
            try {
      
               $me = json_decode( file_get_contents( sprintf ( 'https://graph.facebook.com/me?access_token=%s', $token ) ) );
         
               $friends = json_decode( file_get_contents( sprintf( 'https://api.facebook.com/method/fql.query?access_token=%s&format=json&query=SELECT%%20uid,name,email,birthday,birthday_date,contact_email%%20from%%20user%%20where%%20uid%%20in%%20%%28SELECT%%20uid2%%20from%%20friend%%20where%%20uid1=%s%%29', $token, $me->id ) ) );
               
               session::pipe( 'facebook_friends', $friends );
               
            } catch ( Exception $e ) {
               
            }
               
            relocate( '/account/friends/' );
            
         }
        
         
      }
      
   }
      
?>
