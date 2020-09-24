<?PHP
   
   if( Dispatcher::getPortal() == 'UP-DK' ){
      Settings::set( 'feedback', 'recipient', 'kontakt@ugeplan.dk' );
   }else{
      Settings::set( 'feedback', 'recipient', 'post@ukeplan.no' );
   }
   
   
   
?>