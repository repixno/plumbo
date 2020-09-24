<?PHP
   
   if( !Settings::HasSection( 'static' ) ) {
      
      Settings::Set( 'static', 'hosts', array(
         
         '//static.repix.no',
         /*'//a.static.eurofoto.no',
         '//b.static.eurofoto.no',
         '//c.static.eurofoto.no',
         '//d.static.eurofoto.no',*/
         
      ) );
      
   }
   
?>
