<?PHP
   
   /**
    * WEB HOSTING SETUP
    */
   Settings::set( 'default', 'hostname', 'repix.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::Set( 'domainMap', 'frida.repix.no', array(
         'siteroot'      => 'website',
         'template'      => 'eurofoto2',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'redirect'      => false,
         'https'         => false,
   ) );

	
   Settings::Set( 'domainMap', 'www2.eurofoto.no', array(
         'siteroot'      => 'website',
         'template'      => 'eurofoto2',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'redirect'      => false,
         'https'         => false,
   ) );

   Settings::Set( 'domainMap', 'beta.eurofoto.no', array(
         'siteroot'      => 'website',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'redirect'      => false,
         'https'         => false,
   ) );

   
   /**
    * MEDIACLIP SETUP
    */
   Settings::Set( 'mediaclip', 'server', 'jasmin.repix.no' );
   //Settings::Set( 'mediaclip', 'server', 'bente.eurofoto.no' );
   Settings::set( 'mediaclip', 'folder', 'ECommerceBridge' );   
   /**
    * CACHING SETUP
    */
   Settings::Set( 'cache', 'session.hosts', array(
     	'frida.eurofoto.no' => 11211,
	'mercedes.eurofoto.no'  => 11211,
   ) );

   Settings::Set( 'cache', 'memcache.hosts', array(
	  'mercedes.eurofoto.no'  => 11211,
     	'frida.eurofoto.no' => 11211,
   ) );   

   /**
    * DATABASE SETUP
    */
   $dbconfig = array(
      
      // configuration for the read-write connection
      'readwrite' => array(
      
         // to switch to a different DBMS, set it here
         'dsn' => 'pgsql:host=sidsel;dbname=eurofoto',
         
         // configure username/password
         'user'  => 'www',
         'pass'  => '',
      ),
      
      // configuration for the read-only connection
      'readonly' => array(
      
         // to switch to a different DBMS, set it here
         'dsn' => 'pgsql:host=sidsel;dbname=eurofoto',
         
         // configure username/password
         'user'  => 'www',
         'pass'  => '',
      ),
      
   );

   // setup the database configuration
   Settings::SetSection( 'database', $dbconfig );
   
   // register all this as secret information
   SecretInformation::register( $dbconfig['readwrite']['dsn'] );
   SecretInformation::register( $dbconfig['readwrite']['user'] );
   SecretInformation::register( $dbconfig['readwrite']['pass'] );
   SecretInformation::register( $dbconfig['readonly']['dsn'] );
   SecretInformation::register( $dbconfig['readonly']['user'] );
   SecretInformation::register( $dbconfig['readonly']['pass'] );
   
   // clear the local namespace
   unset( $dbconfig );
  
?>
