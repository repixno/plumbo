<?PHP
   
   import( 'pages.user' );
   import( 'website.admin' );
   import( 'website.helper' );
   model( 'i18n.language' );
   config( 'admin.settings' );
   
   class AdminPage extends UserPage {
      
      protected $template = 'default';
      
      public function parenint(){
         if( !parent::Initialize() ) return false;
      }
      
      public function initialize() {
         
         // forward the parent request
         $this->parenint();
         
         /* TODO: Re-enable this code
         $adminhost = WebsiteHelper::adminBaseHost();
         if( $_SERVER['HTTP_HOST'] != $adminhost ) {
            relocate( 'http://%s%s, $adminhost );
         }
         */
         
         // make sure we're logged in
         if( !Login::isAdmin() ) {
            
            try {
               
               $admin = new Admin( Login::userid() );
               if( !$admin->isLoaded() && $admin->isAdmin() ) {
                  
                  throw new SecurityException( 'Admin access not granted!' );
                  
               }
               
               Login::setAdmin( true );
               
            } catch ( Exception $e ) {
               
               // if not, logout...
               Login::logout();
               
               // then redirect to the login page
               relocate( WebsiteHelper::loginBasePath().'?ref='.base64_encode( $_SERVER['REQUEST_URI'] ) );
               
               // stop execution
               die();
               
            }
            
         }
         
         // define a pagetype variable
         $this->adminpage = true;
         
         $this->countries = $this->getActiveLanguages();
         
         // fetch available sites
         $siteid = Session::get( 'adminsiteid', 0 );
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', 1 );
         }else{
            Session::set( 'adminsiteid', $siteid );
            Session::set( 'siteid', $siteid );
         }
         
         $sites = Settings::get( 'application', 'sites', array() );
         
         $sites[$siteid]['selected'] = true;
         $this->sites = array_values( $sites );
         
         // return success
         return true;
         
      }
      
      protected function getActiveLanguages() {
         
         $collection = new Language();
         
         foreach( $collection->collection( array( 'languageid' ), null, 'languageid' )->fetchAllAs( 'Language' ) as $language ) {
         
            if ($language->active) {
               $record = array(
                  'name'      => $language->elementname,
                  'segment'   => $language->code,
                  'country'   => $language->country,
                  'short'     => $language->short,
               );
               
               $languages[] = $record;
               
            }
         }
         
         return $languages;
      }
   }
   
   
   
   class limitedAdminPage extends Adminpage {
      
      protected $template = 'default';
      public function initialize() {
         
         // forward the parent request
         parent::parenint();
         
               
         // make sure we're logged in
         if( !Login::islimitedAdmin() ) {
            
            
            try {
               
               $admin = new limitedAdmin();
               if( !$admin->islimitedAdmin() ) {
                  if( !parent::Initialize() ) return false;
               }
               Login::setlimitedAdmin( true );
            } catch ( Exception $e ) {
               
               // if not, logout...
               Login::logout();
               
               // then redirect to the login page
               relocate( WebsiteHelper::loginBasePath().'?ref='.base64_encode( $_SERVER['REQUEST_URI'] ) );
               
               // stop execution
               die();
               
            }
            
         }
         
         // define a pagetype variable
         $this->limitedadminpage = true;
         $this->countries = parent::getActiveLanguages();
         // fetch available sites
         
         $limitedadmins  =  Settings::get( 'admin', 'portaladmins' );
         
         //$siteid = Session::get( 'adminsiteid', 0 );
         
         $siteid = $limitedadmins[Login::userid()]['siteid'];
         
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', $thisuserid );
         }else{
            Session::set( 'adminsiteid', $siteid );
            Session::set( 'siteid', $siteid );
         }
         
         $sites = Settings::get( 'application', 'sites', array() );
         
         
         $sites[$siteid]['selected'] = true;
         
         
         $selectedsite = $sites[$siteid];

         
         $this->sites = array(  $selectedsite  );
         
         // return success
         return true;
         
      }
      
   }
   
?>
