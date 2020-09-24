<?PHP
   
   class TestsSMS extends WebPage implements IView {
      
      protected $template = 'tests.sms';
      
      public function Execute() {
        
         if( Login::isLoggedIn() ) {
            
            try {
               $user = new User( Login::userid() );
               if( $user instanceof User && $user->isLoaded() ) {
                  
                  $this->smservices = $user->smsServices();
                  
               }
               
            } catch( Exception $e ) {}
            
         }
         
      }
      
   }
   
?>