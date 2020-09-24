<?PHP
   
   if( !Settings::HasSection( 'streams' ) ) {
      
      Settings::Set( 'streams', 'hosts', array(
         
         'http://a.stream.eurofoto.no',
         'http://b.stream.eurofoto.no',
         'http://c.stream.eurofoto.no',
         'http://d.stream.eurofoto.no',
         
      ) );
      
   }
   
?>