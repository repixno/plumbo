<?php
   
   class LoginDialog extends WebPage implements IView {
      
      protected $template = 'dialogs/login';
      
      public function Execute() {
         
		 
		 if( Dispatcher::getPortal() == "TK-001" || Dispatcher::getPortal() == "TK-SV"){
			$logindata = Dispatcher::getCustomAttr('login');
			relocate( $logindata['default'] );
			die();
		 }
		 
		 
         // check if we're logged in
         if( Login::isLoggedIn() ) {
            
            if( isset( $_POST['referer'] ) && $_POST['referer'] != '' ) {
               
               header( 'location: '.$_POST['referer'] );
               
            } else if( isset( $_GET['ref'] ) && $_GET['ref'] != '' ) {
               
               header( 'location: '.base64_decode( $_GET['ref'] ) );
               
            }
	    else {
               
               $redirecturl = Session::get( 'loginredirecturl' );
	       
	       if( empty( $redirecturl ) ){
		  $redirecturl  = Session::pipe( 'loginredirecturl' );
	       }
               
               if ( !empty( $redirecturl ) && $redirecturl != '/logout/') {
                  
                  relocate( $redirecturl );
                  
               } else {
                  
                  $logindata = Dispatcher::getCustomAttr('login');
                  if( isset( $logindata['default'] ) ) {
                     
                     relocate( $logindata['default'] );
                     
                  } else {
                     
                     // if not, redirect us home
                     relocate( '/myaccount/settings' );
                     
                  }
                  
               }
               
            }
            
            // finally, die
            die();
            
         }
         
         // login failure?
         if( Login::LoginFailed() ) {
	       $this->error = true;
	       $this->errorreason = Login::$LoginFailureReason;
         }
         

         // allow for our template to show a given 
         $this->email = isset( $_REQUEST['email'] ) ? $_REQUEST['email'] : '';
         $this->password = isset( $_POST['password'] ) ? $_POST['password'] : '';
         
         // pass on the referer, if any
         if( isset( $_GET['ref'] ) ) {
            
	       $this->referer = base64_decode( $_GET['ref'] );
	       session::set( 'loginredirecturl',  base64_decode( $_GET['ref'] ));
            
         } elseif( isset( $_POST['referer'] ) ) {
            
            $this->referer = $_POST['referer'];
            
         } else {
            
            $this->referer = '';
            
         }
         
      }
      
   }
   
?>
