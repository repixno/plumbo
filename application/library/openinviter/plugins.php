<?php

include('openinviter.php');

$inviter = new OpenInviter( );

print_r( $inviter->getPlugins() );

/*$inviter->startPlugin( 'gmail' );

if ( $inviter->login( 'eivindam@gmail.com', 'Kingk0ng4' ) ) {

   print_r( $inviter->getMyContacts() );

} else {
   echo $inviter->getInternalError();
}*/

?>
