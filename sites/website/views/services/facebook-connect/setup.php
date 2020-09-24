<?php
   
   library( 'facebook.facebook' );
   
   class FacebookConnectSetup extends WebPage implements IView {
      
      public function Execute() {
         
         try {
            
            $facebook = new Facebook( 'dbdea9cdd4dd106a6375c56f73eabafc', 'f5051e6c8ab9e5294b0fd68fd2a1d327' );
            #$facebook->
            
            $uid = $facebook->api_client->users_getLoggedInUser();
   
            $user_details = $facebook->api_client->users_getInfo( $uid, array( 'last_name','first_name', 'name', 'timezone', 'birthday', 'sex', 'locale', 'profile_url', 'proxied_email', 'email' ) );
            
            util::Debug( $user_details );
            
         } catch( Exception $e ) {
            
            relocate( '/tests/facebook' );
            
         }
         
         die();
         
      }
      
   }
   
?>