<?PHP
   
   // enable strict error-reporting
   error_reporting( E_ALL ^ E_NOTICE | E_STRICT );
   
   // import default configuration
   config( 'common.defaults' );
   
   // import date/time configuration
   config( 'common.datetime' );
   
   // import the security configuration
   config( 'common.security' );
   
   // import the database configuration
   config( 'database.config' );
   
   // override defaults?
   override( 'local.config' );
   
?>