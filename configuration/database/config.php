<?php
   	
   // import the required modules
   import( 'core.settings' );
   import( 'core.security' );
   
   // define the database config
   $dbconfig = array(
      
      // configuration for the read-write connection
      'readwrite' => array(
      
         // to switch to a different DBMS, set it here
         'dsn' => 'pgsql:host=localhost;dbname=eurofoto',
         
         // configure username/password
         'user'  => 'ef',
         'pass'  => '',
      ),
      
      // configuration for the read-only connection
      'readonly' => array(
      
         // to switch to a different DBMS, set it here
         'dsn' => 'pgsql:host=localhost;dbname=eurofoto',
         
         // configure username/password
         'user'  => 'ef',
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
