<?PHP
   
   if( !Settings::HasSection( 'static' ) ) {
      
      Settings::Set( 'static', 'hosts', array(
         
         'https://static.repix.no',
         
      ) );
      
   }
   
?>