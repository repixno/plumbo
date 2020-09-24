<?php


if( Dispatcher::getPortal() == 'XXL-NO'){
    define('SITE_ROOT', dirname(dirname(__FILE__)));
    define('INCLUDE_DIR', SITE_ROOT . '/include/');
    define('XTCI_CLIENT_NAME', 'JapanphotoXXL');
    define('XTCI_CLIENT_ID', '58');	
    define('KEY_ACCOUNT_ID', '18631');	
    define('XTCI_CLIENT_KEYACCOUNT', '18631');	
    define('XTCI_CLIENT_VERSION', '0.7');
    if (!defined('XTCI_SERVER_VERSION')) 
    define('XTCI_SERVER_VERSION', '2.3');
    define('XTCI_SERVER_VERSION', '2.3');
}
else if( Dispatcher::getPortal() == 'XXL-SE'){
    define('SITE_ROOT', dirname(dirname(__FILE__)));
    define('INCLUDE_DIR', SITE_ROOT . '/include/');
    define('XTCI_CLIENT_NAME', 'JapanphotoXXL');
    define('XTCI_CLIENT_ID', '58');	
    define('KEY_ACCOUNT_ID', '18643');	
    define('XTCI_CLIENT_KEYACCOUNT', '18643');	
    define('XTCI_CLIENT_VERSION', '0.7');
    if (!defined('XTCI_SERVER_VERSION')) 
    define('XTCI_SERVER_VERSION', '2.3');
    define('XTCI_SERVER_VERSION', '2.3');
}
else if( Dispatcher::getPortal() == "TK-SV" ){
    //alternative id 21452
    define('SITE_ROOT', dirname(dirname(__FILE__)));
    define('INCLUDE_DIR', SITE_ROOT . '/include/');
    define('XTCI_CLIENT_NAME', 'Japan Photo XXL SE');
    define('XTCI_CLIENT_ID', '58');	
    define('KEY_ACCOUNT_ID', '21449');	
    define('XTCI_CLIENT_KEYACCOUNT', '21449');	
    define('XTCI_CLIENT_VERSION', '0.7');
    if (!defined('XTCI_SERVER_VERSION')) 
    define('XTCI_SERVER_VERSION', '2.3');
    define('XTCI_SERVER_VERSION', '2.3');

}else{
    define('SITE_ROOT', dirname(dirname(__FILE__)));
    define('INCLUDE_DIR', SITE_ROOT . '/include/');
    define('XTCI_CLIENT_NAME', 'JapanphotoXXL');
    define('XTCI_CLIENT_ID', '58');	
    define('KEY_ACCOUNT_ID', '21473');	
    define('XTCI_CLIENT_KEYACCOUNT', '21473');	
    define('XTCI_CLIENT_VERSION', '0.7');
    if (!defined('XTCI_SERVER_VERSION')) 
    define('XTCI_SERVER_VERSION', '2.3');
    define('XTCI_SERVER_VERSION', '2.3');
    
}


// Debugging, set to true to get detailed XML logging
if (!isset($xtci_debug)) $xtci_debug = false;

// XTCI-Logging
if (!defined('XTCI_LOG')) define('XTCI_LOG', false);

// Override with true if your site speaks utf8. Defaults to false because
// of historic reasons.
if (!defined('XTCI_UTF8')) define('XTCI_UTF8', true );
if (!defined('XTCI_CACHE_TIMEOUT')) define('XTCI_CACHE_TIMEOUT', 43200);

// Define Ports
if (!defined('XTCI_HTTP_PORT')) define('XTCI_HTTP_PORT', 80);
if (!defined('XTCI_HTTPS_PORT')) define('XTCI_HTTPS_PORT', 443);

// Default XTCI Servers




?>