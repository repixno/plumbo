<?php
// import required libraries
include "../../../bootstrap.php";
// configure app
config( 'website.config' );

$newSessionId=0;

    $username = isset( $_POST['username'] ) ? $_POST['username'] : $_GET['username'];
    $password = isset( $_POST['password'] ) ? $_POST['password'] : $_GET['password'];
    $portal   = isset( $_POST['portal'] )   ? $_POST['portal']   : $_GET['portal'];
    
    
    if( !$username || !$password ){
	$response = "error=-1";
    }
    else{
        $loginresponse = -2;
        
        if( $token = Login::byPortalUsernameAndPassword( $portal, $username, $password ) ) {
            
            $newSessionId=session_id();
            
            $response = 	"error=0\n";
            $response .=	"sessionid=$token\n";
            $response .=	"uploadurl=http://www.eurofoto.no/api/1.0/picasa/upload\n";
        } else {
            
           $response = "error=".$loginresponse;
           
        }
    }
    
    mail( 'tor.inge@eurofoto.no', "picasa login", $response . " test" );
    
    echo  $response;
    
    

?>