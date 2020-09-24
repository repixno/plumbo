<?php
   
   class LoginDialog extends WebPage implements IView {
      
      protected $template = 'dialogs/login';
      
      public function Execute() {
         
         // check if we're logged in
         if( Login::isLoggedIn() ) {
            
            if( isset( $_POST['referer'] ) && $_POST['referer'] != '' ) {
               
               relocate( $_POST['referer'] );
               
            } else if( isset( $_GET['ref'] ) && $_GET['ref'] != '' ) {
               
               relocate( base64_decode( $_GET['ref'] ) );
               
            } else {
               
               // if so, redirect us home
               relocate( '/' );
               
            }
            
            // finally, die
            die();
            
         }
         
         // allow for our template to show a given 
         $this->email = isset( $_POST['email'] ) ? $_POST['email'] : '';
         $this->password = isset( $_POST['password'] ) ? $_POST['password'] : '';
         
         // pass on the referer, if any
         if( isset( $_GET['ref'] ) ) {
            
            $this->referer = base64_decode( $_GET['ref'] );
            
         } elseif( isset( $_POST['referer'] ) ) {
            
            $this->referer = $_POST['referer'];
            
         } else {
            
            $this->referer = '';
            
         }
         
      }
      
   }
   
?>