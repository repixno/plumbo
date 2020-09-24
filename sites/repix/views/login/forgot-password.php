<?PHP
   
   class LoginForgotPassword extends WebPage implements IValidatedView {
      
      protected $template = 'dialogs.forgot-password';
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
               ),
            ),
            'send' => array(
               'post' => array(
                  'email' => VALIDATE_STRING,
               )
            ),
            'reset' => array(
               'post' => array(
                  'email' => VALIDATE_STRING,
                  'code'  => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
            ),
            'verify' => array(
               'post' => array(
                  'email'    => VALIDATE_STRING,
                  'code'     => VALIDATE_STRING,
                  'password' => VALIDATE_STRING,
                  'repeated' => VALIDATE_STRING,
               ),
            ),
         );
         
      }
      
      public function Execute( $email = '' ) {
         
         $this->email = $email;
         
      }
      
      public function Send() {
         
         import( 'math.signer' );
         import( 'mail.send' );
         
         $email = $_POST['email'];
         
         $fromemail = Dispatcher::getCustomAttr( 'portalemail' );
         
         if( !$fromemail ){
            $fromemail = 'post@repix.no';
         }
         
         if (!empty($email)) {
            
            $email = trim( $email );
            
            try {
               $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );
            } catch( Exception $e ) { 
               $user = false;
            }
            
            if( $user instanceof User && $user->isLoaded() ) {
               
               $this->sentToEmail = $user->email;
               
               $code = Signer::sign( $user->uid, 'reset-password' );
               $link = sprintf( '%s/login/forgot-password/reset/%s/%s', WebsiteHelper::rootBaseUrl(), $email, $code );
               
               MailSend::Simple(
                  $user->email,
                  __( 'Your requested password reset link' ),
                  'login.forgot-password',
                  array(
                     'name' => $user->fullname,
                     'email'=> $user->email,
                     'code' => $code,
                     'link' => $link,
                  ),
                  null,
                  $fromemail
               );
               
            } else {
               
               $this->notFoundWarning = true;
               
            }
         } else {
            
            $this->notFoundWarning = true;
            
         }
         
      }
      
      public function Reset( $email = '', $code = '' ) {
         
         $this->setTemplate( 'dialogs.reset-password' );
         
         if( !$email ) $email = $_POST['email'];
         if( !$code ) $code = $_POST['code'];
         
         if( $email ) $this->email = $email;
         if( $code ) $this->code = $code;
         
         $this->error = Session::pipe( 'forgot-password-verify-error', null, '' );
         
      }
      
      public function Verify() {
         
         import( 'math.signer' );
         
         $email = $_POST['email'];
         $code = $_POST['code'];
         $password = $_POST['password'];
         $repeated = $_POST['repeated'];
         
         $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );
         
         if( $user instanceof User && $user->isLoaded() ) {
            
            if( $code != Signer::sign( $user->uid, 'reset-password' ) ) {
               
               Session::pipe( 'forgot-password-verify-error', __( 'The supplied verification code is invalid!' ) );
               relocate( '/login/forgot-password/reset/%s', $email );
               
            } else {
               
               if( $password != '' && $password == $repeated ) {
                  
                  $user->password = crypt( $password );
                  $user->save();
                  
                  Login::byUserObject( $user );
                  relocate( '/myaccount/' );
                  
               } else {
                  
                  Session::pipe( 'forgot-password-verify-error', __( 'The passwords supplied are invalid or does not match!' ) );
                  relocate( '/login/forgot-password/reset/%s/%s', $email, $code );
                  
               }
               
            }
            
         } else {
            
            Session::pipe( 'forgot-password-verify-error', __( 'The email address was not found in our user database!' ) );
            relocate( '/login/forgot-password/reset/' );
            
         }
         
         die();
         
      }
      
   }
   
?>