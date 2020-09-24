<?php
   
   /**
    * Register new user
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'validate.email' );
   import( 'website.user' );

   class RegisterDialog extends WebPage implements IView {
      
      protected $template = 'dialogs/register';
      
      public function Execute() {
         
         if( Login::isLoggedIn() ){
            relocate( '/myaccount/ ');
         }
         
         $error = Session::pipe( 'registererror' );
         $this->nextStep = Session::get( 'loginredirecturl' );

         if( !empty( $error ) ) {
            
            $this->error = $error;
            
         }
         
      }
      
      
      public function Newsletter() {
         
         
         $this->template = 'dialogs/newsletter';
         
         /*if( Login::isLoggedIn() ){
            relocate( '/myaccount/ ');
         }*/
         
         $error = Session::pipe( 'registererror' );
         $this->nextStep = Session::get( 'loginredirecturl' );

         if( !empty( $error ) ) {
            
            $this->error = $error;
            
         }
         
      }
      
      public function Thanks( $email ) {
         
         $this->template = 'dialogs/thanks';
         $this->registeredemail = $email;
         
      }
      
   }
      
?>