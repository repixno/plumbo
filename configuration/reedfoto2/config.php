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
   config( 'reedfoto2.domainmap' );
   
   // import intelesms specific stuff
   config( 'reedfoto2.cms' );
   config( 'reedfoto2.static' );
   
?>