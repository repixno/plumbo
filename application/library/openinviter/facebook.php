<?php

include('openinviter.php');

$inviter = new OpenInviter( );

//print_r( $inviter->getPlugins() );

$inviter->startPlugin( 'facebook' );

if ( $inviter->login( 'eivindam@gmail.com', 'd0rkside' ) ) {

   print_r( $inviter->getMyContacts() );

} else {
   echo $inviter->getInternalError();
}

?>
