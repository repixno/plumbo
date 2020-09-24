<?PHP
   
   // import the required modules
   import( 'core.security' );
   
   // to invalidate all auto-logins, simply change this session-key
   SecurityKeys::setKeys( array(
      SECURITY_KEY_ROOTKEY => 'c6812349-644a-4d4b-aa19-cb5f60659214',
      SECURITY_KEY_SEEDKEY => 'fca7cc22-b83d-43c6-9770-c6b40d8c14d3',
      SECURITY_KEY_SESSION => '57735f2b-897d-4ec3-aa11-d7d812fd5929',
   ) );
   
?>