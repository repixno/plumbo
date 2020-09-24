<?php
   
   class LogoutDialog extends UserPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
         
         // check if we're logged in
         Login::logout();
         
         // redirect us home
         relocate( '/' );
         
      }
      
   }
   
?>