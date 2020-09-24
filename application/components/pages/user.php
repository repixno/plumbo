<?PHP
   
   import( 'pages.web' );
   import( 'website.helper' );
   
   class UserPage extends WebPage {
      
      public function initialize( $norelocate = false ) {
         Util::setSSL();
         // forward the parent request
         if( !parent::Initialize() ) return false;
         
         // make sure we're logged in
         if( !Login::isLoggedIn() && !$norelocate ) {
            
            // if not, relocate to the login-page
            relocate( WebsiteHelper::loginBasePath().'?ref='.base64_encode( $_SERVER['REQUEST_URI'] ) );
            
            // stop execution
            die();
            
         }
         
         // return success
         return true;
         
      }
      
   }
   
?>