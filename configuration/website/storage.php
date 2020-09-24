<?PHP

    Settings::Set( 'storage', 'path', '/data/bildearkiv/' );
   if( Dispatcher::getPortal() == 'RF-001' ){
      Settings::Set( 'storage', 'currentpartition', 'rf001' );
   }
   else{
      Settings::Set( 'storage', 'currentpartition', 'z078' );
   }


?>
