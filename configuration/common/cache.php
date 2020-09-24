<?PHP
   
   // import the required modules
   import( 'core.settings' );

   // set the diskcache's cachedir parameter  
   Settings::set( 'cache', 'diskcache.cachedir', sprintf( '%s/tmp/', getRootPath() ) );
   
   // to disable caching alltogether, uncomment this line
   Settings::set( 'cache', 'disabled', false );
   
   // session memcache configuration
   Settings::Set( 'cache', 'session.hosts', array(
      'localhost' => 11211,
   ) );
   
   // cache memcache configuration
   Settings::Set( 'cache', 'memcache.hosts', array(
      'localhost' => 11211,
   ) );
   
?>