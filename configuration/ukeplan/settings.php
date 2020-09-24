<?PHP
   
   // set the current version
   Settings::set( 'application', 'version', 0.1002 );

   // set the current version
   Settings::set( 'application', 'offline', false );
   
   // allow XML rendering
   Settings::set( 'application', 'allowxml', true );

   // draw the content-type header to ensure its UTF-8
   header( 'Content-type: text/html; charset=UTF-8' );
   
   // setup cookie params for the session
   if( stristr( $_SERVER['HTTP_HOST'], 'ukeplan.no' ) ) {
      
      // make sure the session is valid for all our hosts
      session_set_cookie_params( 0, '/', '.ukeplan.no' );
      
   } else {
      
      // make sure the session is valid for all our hosts
      // session_set_cookie_params( 0, '/', '.eurofoto.no' );
      
   }
   
?>
