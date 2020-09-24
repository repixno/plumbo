<?PHP
   
   if( !Settings::HasSection( 'static' ) ) {
      
      Settings::Set( 'static', 'hosts', array(
         
         'http://static.repix.no',
         
      ) );
      
   }
   
?>