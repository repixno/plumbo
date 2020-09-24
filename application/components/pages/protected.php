<?PHP
   
   import( 'pages.user' );
   import( 'website.helper' );
   
   class ProtectedPage extends UserPage {
      
      public function initialize( $norelocate = false ) {
         
         // forward the parent request
         if( !parent::Initialize( $norelocate ) ) return false;
         
         // allow all admins to see protected pages
         if( Login::isAdmin() ) return true;
         
         // find the current classname
         $classname = strtolower( get_class( $this ) );
         
         // make sure we're logged in
         if( DB::query( 'SELECT created FROM site_protectedpage WHERE classname = ? AND userid = ?', $classname, Login::userid() )->count() == 0 && !$norelocate ) {
            
            // see if this user has some kind of wildcard-setup
            foreach( DB::query( "SELECT classname FROM site_protectedpage WHERE userid = ? AND classname LIKE '%*%'", Login::userid() )->fetchAll() as $row ) {
               
               list( $rulename ) = $row;
               $matchstring = '/^'.str_replace( '*', '(.*?)', $rulename ).'$/';
               if( preg_match( $matchstring, $classname, $matches ) ) return true;
               
            }
            
            // log out the user if already logged in
            Login::logout();
            
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