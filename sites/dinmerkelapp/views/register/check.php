<?php

   import( 'validate.email' );
   import( 'website.user' );
   import( 'website.credit' );

   class RegisterCheck extends WebPage implements IValidatedView {
      
      protected $template = false;
      
        /**
       * Validate the user input
       *
       * @return array
       */
      public function Validate() {

         return array(
            "execute" => array(
               "post" => array(
                  "newEmail"     => VALIDATE_STRING,
                  "cemail"     => VALIDATE_STRING,
                  "password"     => VALIDATE_STRING,
                  "password2"    => VALIDATE_STRING,
                  "eula"         => VALIDATE_BOOLEAN,
                  "newsletter"   => VALIDATE_BOOLEAN,
               ),
            ),
            "newsletter" => array(
               "post" => array(
                  "newEmail"     => VALIDATE_STRING,
                  "cemail"     => VALIDATE_STRING,
                  "password"     => VALIDATE_STRING,
                  "password2"    => VALIDATE_STRING,
                  "eula"         => VALIDATE_BOOLEAN,
                  "newsletter"   => VALIDATE_BOOLEAN,
               ),
            )
         );
         
      }
      
      
      public function Execute() {
         
         
         if( $_POST["newEmail"] ){
             $email = $_POST["newEmail"];
         }
         else if( $_POST['cemail']){
            $email = $_POST['cemail'];
         }
         
        
         $password = $_POST["password"];
         $password2 = $_POST["password2"];
         $nopass = false;
         
         
         
         
         // Setup Eula and newsletter
         if( isset( $_POST["eula"] ) ) {
            $eula = true;
         } else {
            $eula = false;
         }
         
         if( isset( $_POST["newsletter"] ) ) {
            $newsletter = true;
         } else {
            $newsletter = false;
         }

         // Check so Eula is accepted
         if( !$eula ) {
            Session::pipe( 'registererror', "You have to accept the terms and conditions to register" );
            relocate( '/register/' );
            die();
         }

         // Check so email and password and eula is set
         if( empty( $email ) || empty( $password ) || empty( $password2 ) ) {
            Session::pipe( 'registererror', 'Missing email or password. Please fill in all fields' );
            relocate( '/register/' );
            die();
         }
         
         // Same password
         if( $password != $password2 ) {
            Session::pipe( 'registererror', "Passwords differ" );
            relocate( '/register/' );
            die();
         }
         
         if( !ValidateEmail::validate( $email ) ) {
            Session::pipe( 'registererror', "Email not valid" );
            relocate( '/register/' );
            die();
         }
         
         if( User::hasNoPass( $email ) ) {
            $nopass = true;
         }
         
         if( $nopass == false ) {
            if( User::registered( $email ) ) {
               Session::pipe( 'registererror', "Username already registered" );
               relocate( '/register/' );
               die();
            }
         }
         
         
         if( $nopass == false ) {
            
            try {
               // Fine - passed all checks
               // Let's create a new user
               $user = new User();
               $user->username = $email;
               $user->password = crypt( $password );
               $user->portal = Dispatcher::getPortal();
               
               if( $user->portal == 'VP-001' ){
                  $user->country = 203;
               }
               else if( $user->portal == 'UP-DK' ){
                  $user->country = 59;
               }
               
               if( $newsletter ) {
                  $user->newsletter = true;
                  $user->html = true;
               }
               $user->created = date( 'Y-m-d H:i:s' );
               $user->save();
               
               
               if( $user->portal == 'SK-001' && $user->newsletter = true ){
                  $credit = new Credit();
                  $credit->uid = $user->uid;
                  $credit->artikkelnr = 419;
                  $credit->antall = 100;
                  $credit->tekst = "100 Gratisbilder, ny kunde";
                  $credit->tidspunkt = date( 'Y-m-d H:i:s' );
                  $credit->save();
               }
                  
               if( Login::byUserObject( $user ) ) {
                  
                     $redirecturl = Session::get( 'loginredirecturl' );
                     
                     /*if( empty( $redirecturl ) ){
                        $redirecturl  = Session::pipe( 'loginredirecturl' );
                     }*/
                     
                     if ( !empty( $redirecturl ) ) {
                        
                        relocate( $redirecturl );
                        die();
                        
                     } else {
                        relocate( '/myaccount/welcome/' );
                        die();
                     }
                     
               }
               
               throw new CriticalException( 'Unable to login' );
               
            } catch( Exception $e ) {
            
               Session::pipe( 'registererror', "Unknown error trying to create new user" );
               relocate( '/register/' );
               die();
               
            }
            
         } else {
            
            try {
            
               $userid = User::UserIDfromUsernameAndPortal( $email, Dispatcher::getPortal() );
               
               if( $userid > 0 ) {
   
                  $res = DB::query( "SELECT uid FROM kunde WHERE uid = ?", $userid );
                  if( $res->count() == 0 ) {
                     DB::query( "INSERT INTO kunde( uid ) VALUES( ? )", $userid );
                  }
                  
                  $user = new User( $userid );
                  $user->username = $email;
                  $user->password = crypt( $password );
   
                  if( $newsletter ) {
                     $user->newsletter = true;
                     $user->html = true;
                  }
   
                  $user->created = date( 'Y-m-d H:i:s' );
                  $user->save();
   
                  if( Login::byUserObject( $user ) ) {
                     
                     $redirecturl = Session::get( 'loginredirecturl' );
                     /*if( empty( $redirecturl ) ){
                        $redirecturl  = Session::pipe( 'loginredirecturl' );
                     }*/
               
                     if ( !empty( $redirecturl ) ) {
                        
                        Session::delete( 'loginredirecturl' );
                        
                        relocate( $redirecturl );
                        die();
                        
                     } else {

                        relocate( '/myaccount/welcome/' );
                        die();
                     }
                     
                  }
                  
               }
            
            } catch( Exception $e ) {
               
               Session::pipe( 'registererror', 'Unknown error trying to create new user with nopass' );
               relocate( '/register' );
               die();
               
            }
            
         }
         
      }
     
   
   
      public function Newsletter(){
         
         $email = $_POST["newEmail"];
         
         try{
            $userid = User::UserIDfromUsernameAndPortal( $email, Dispatcher::getPortal() );
         }catch( Exception $e ){
            
            //Util::Debug( $e->getMessage() );
            
         }
         
         if( $userid  > 0 ){
            $user = new User( $userid );
            $user->newsletter = true;
            $user->save();
            
            //util::Debug( $user );
            
         }else{
            
            $user = new User();
            $user->username = $email;
            $user->newsletter = true;
            $user->passord = 'nopass';
            $user->portal = Dispatcher::getPortal();
            $user->save();
                        
            //Util::Debug( "user dont existe" . $userid );
            
         }
         
         relocate( '/register/thanks/' . $email );
         
         
         //Util::Debug( "Takk for at du melde deg på nyhetsbrev" );
         
         
      }
   
   }

?>