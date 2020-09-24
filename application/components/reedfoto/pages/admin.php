<?PHP
   
   import( 'reedfoto.pages.user' );
   import( 'website.helper' );
   
   class AdminPage extends UserPage {
      
      protected $template = 'default';
      
      public function initialize() {
         
         // forward the parent request
         if( !parent::Initialize() ) return false;
         
         // make sure we're logged in
         if( !Login::isAdmin() ) {
            
            // if not, logout...
            Login::logout();
            
            // then redirect to the login page
            relocate( WebsiteHelper::loginBasePath().'?ref='.base64_encode( $_SERVER['REQUEST_URI'] ) );
            
            // stop execution
            die();
            
         }
         
         // define a pagetype variable
         $this->adminpage = true;
         
         // return success
         return true;
         
      }
      
   }
   
?>
