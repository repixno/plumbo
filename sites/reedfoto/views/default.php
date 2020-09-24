<?PHP
   
   class ReedFotoHome extends WebPage implements IView {
      
      protected $template = 'default';
      
      public function Execute() {
         
         // if we're logged in, go somewhere...
         if( Login::isLoggedIn() ) {
            
            // if we are an admin...
            if( Login::isAdmin() ) {
               
               // relocate to the admin page
               relocate( WebsiteHelper::adminBasePath() );
               
            } else {
               
               // otherwise, to the list of corrections
               relocate( '/corrections/' );
               
            }
            
         }
         
      }
      
   }
   
?>