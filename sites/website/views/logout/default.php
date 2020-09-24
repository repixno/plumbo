<?php
   
   class LogoutDialog extends UserPage implements IView {
      
      protected $template = 'dialogs/logout';
      
      public function Execute() {
         
         // check if we're logged in
         Login::logout();
         
         // refill as logged out
         WebPage::Initialize( true );
         
      }
      
   }
   
?>