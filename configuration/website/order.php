<?PHP
/**
    * Settings used in order process 
    * when placing an order.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   
   // Production paths
   Settings::Set( 'paths', 'edi', '/data/global/edi' );
   Settings::Set( 'paths', 'download', '/data/print_download' );
   Settings::Set( 'paths', 'images', '/data/bildearkiv' );
   Settings::Set( 'paths', 'oldroot', '/home/httpd/www.eurofoto.no/' );
   Settings::Set( 'paths', 'originaltemplates', '/data/global/maler/orginal' );
   Settings::Set( 'paths', 'clipart', '/data/global/clipart' );
   Settings::Set( 'paths', 'fonts', '/home/httpd/www.repix.no/webside/font' );
   
   // The EF 2.5 refid's
   Settings::Set( 'refid', 'shipping', 127 );
   Settings::Set( 'refid', 'creditcard', 472 );
   
   // Dont really know what this is used for in order process
   Settings::Set( 'coworker', 'accessories', 5 );
   Settings::Set( 'coworker', 'noaccessories', 4 );
   
   
   //Secure code for downloading bougth digital files
   Settings::Set( 'order', 'securecode', 'Dg3OTVmNTBmZmM0MjIy' );




?>
