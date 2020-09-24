<?PHP
   
   // set the current version
   Settings::set( 'application', 'version', 0.01 );
   
   // set the current version
   Settings::set( 'application', 'offline', false );
   
   // allow XML rendering
   Settings::set( 'application', 'allowxml', true );
   
   // application specific basepaths for Website::helper
   Settings::Set( 'default', 'loginbasepath', '/' );
   Settings::Set( 'default', 'adminbasepath', '/admin/' );
   
   // choose UTF-8 encoding for all content
   header( 'Content-type: text/html; charset=UTF-8' );
   
   // setup cookie params for the session
   if( stristr( $_SERVER['HTTP_HOST'], 'reedfoto.no' ) ) {
      
      // make sure the session is valid for all our hosts
      session_set_cookie_params( 0, '/', '.reedfoto.no' );
      
   }
   
   //set contactemail for approved corrections
   Settings::Set( 'reedfoto', 'correctionemail', 'post@reedfoto.no' );
   
?>
