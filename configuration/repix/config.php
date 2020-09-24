<?PHP
   
   // import website spesific configs
   config( 'website.modules' );
   config( 'website.redirects' );
   config( 'website.settings' );
   config( 'website.aliases' );
   
   // import other config files
   config( 'common.cache' );
   config( 'common.config' );

   // setup the domainmap
   config( 'repix.domainmap' );
   
   // import intelesms specific stuff
   config( 'repix.cms' );
   config( 'repix.static' );
   
?>