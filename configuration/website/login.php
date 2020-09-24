<?PHP
   
   // require core.settings
   import( 'core.settings' );
   
   // number of days for new auto-logins to be valid
   Settings::Set( 'login', 'expiredays', 60 );
   
?>