<?php
   
   class LogoutDialog extends UserPage implements IView {
      
      protected $template = 'dialogs/logout';
      
      public function Execute() {
         
         // check if we're logged in
         Login::logout();
         
         $logindata = Dispatcher::getCustomAttr('login');
         if( isset( $logindata['logouturl'] ) ) {
            relocate( $logindata['logouturl'] );
         }
         
         // refill as logged out
         WebPage::Initialize( true );
         
      }
      
   }
   
?>