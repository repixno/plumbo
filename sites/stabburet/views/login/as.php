<?php
   
   import( 'pages.admin' );
   import( 'website.cart' );
   
   class LoginAs extends AdminPage implements IView {
      
      public function Execute( $userid = 0, $returnurlbase64 = false ) {
         
         $cart = new Cart();
         $cart->clear();
         
         if( is_numeric( $userid ) && $userid > 0 ) {
            
            Login::byUserId( $userid );
            
         } elseif( !empty( $userid ) ) {
            
            $user = User::fromUsernameAndPortal( $userid, Dispatcher::getPortal() );
            if( $user instanceof User && $user->isLoaded() ) {
               
               Login::byUserObject( $user );
               
            }
            
         }
         
         $logindata = Dispatcher::getCustomAttr('login');
         $returnurl = trim( @base64_decode( $returnurlbase64 ) );
         if( $returnurlbase64 && $returnurl ) {
            
            relocate( $returnurl );
            
         } else if( isset( $logindata['default'] ) ) {
            
            relocate( $logindata['default'] );
            
         } else {
            
            // if not, redirect us home
            relocate( '/myaccount/' );
            
         }
         
      }
      
   }
   
?>