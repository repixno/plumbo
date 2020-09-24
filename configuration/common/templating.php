<?PHP
   
   // import the required modules
   import( 'core.settings' );
   
   // define temporary templating path
   Settings::set( 'templating', 'tmp', sprintf( '%s/tmp/', getRootPath() ) );
   
   // whether to reparse cached templates or not
   Settings::set( 'templating', 'reparse', false );
   
   // whether to automatically strip comments
   Settings::set( 'templating', 'stripcomments', false );
   
   // define the default encoding
   Settings::set( 'templating', 'encoding', 'UTF-8' );
   
?>