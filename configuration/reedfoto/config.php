<?PHP
   
   // import website spesific configs
   config( 'reedfoto.modules' );
   config( 'reedfoto.domainmap' );
   
   // reed foto specific configs
   config( 'reedfoto.storage' );
   config( 'reedfoto.conversion' );
   
   // import other config files
   config( 'common.cache' );
   config( 'common.config' );
   
   // settings goes last here
   config( 'reedfoto.settings' );
   
?>