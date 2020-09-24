<?php
// +----------------------------------------------------------------------+
// | Helper functions													 |
// +----------------------------------------------------------------------+

// ========================================================================
// xtci_html_decode
//
// Decodes html-encoded string.
//
// Parameters:
//	 $encoded => HTML-encoded string.
// Return:
//	 $string => Decoded string

function xtci_html_decode($encoded)
{
	return strtr($encoded, array_flip(get_html_translation_table(HTML_ENTITIES,ENT_QUOTES)));
}

// ========================================================================
// xtci_microtime
//
// Returns a unix timestamp with microseconds as a floating point number
//
// Parameters: None
// Return: Timestamp with microseconds as float

function xtci_microtime()
{
	list ($usec, $sec)= explode(" ", microtime());
	return $usec + $sec;
}

// +----------------------------------------------------------------------+
// | Low level routines												   |
// +----------------------------------------------------------------------+

// ========================================================================
// xtci_xml_request
//
// Sends XML request to a XTCI server and returns the response
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $data => XTCI data
//	 $command => XTCI command
// Return:
//	 array($response, $errorcode, $errortext) on success:
//		 $response => XTCI response
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_xml_request($server, $data, $command)
{
	global $xtci_debug, $CONF;

	$header= sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"."<!DOCTYPE %s SYSTEM \"%s/%s.dtd\">\n\n", $command, $server['dtd'], $command);
	$ch= curl_init();
	$url= preg_replace(array ('%^(https://[^/]+)(/.*)$%', '%^(https://[^/]+)(/.*)$%'), array ('\1:'.XTCI_HTTPS_PORT.'\2', '\1:'.XTCI_HTTP_PORT.'\2'), $server['controller']);
	$url= preg_replace(array ('%^(http://[^/]+)(/.*)$%', '%^(http://[^/]+)(/.*)$%'), array ('\1:'.XTCI_HTTPS_PORT.'\2', '\1:'.XTCI_HTTP_PORT.'\2'), $server['controller']);

#	if (XTCI_FORCE_HTTP)
#	{
#		$url= preg_replace('%^https://%', 'http://', $url);
#	}

	curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
	curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	#curl_setopt($ch, CURLOPT_MUTE, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: text/xml'));
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

	if (!XTCI_CHECK_SSL)
	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}

	$postfields= $header.$data."\n";
	
	//Util::Debug($postfields);
	//die();

	if (!XTCI_UTF8)
	{
		$postfields= utf8_encode($postfields);
	}
	/*
	if($command == 'pricelist_request'){echo($postfields); exit; }
	*/
	//$GLOBALS['xml'][] = $postfields;

	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

	// Debugging
	if ($xtci_debug)
	{
		echo '<h1>Request</h1><pre>', htmlentities($header.$data."\n"), '</pre>';
	}

	// Do response
	$starttime= xtci_microtime();
	$response= curl_exec($ch);
	$exectime= xtci_microtime() - $starttime;



	// Log request if XTCI_LOG is defined
	if (XTCI_LOG)
	{
		$time= localtime($starttime, true);
		$usec= $starttime -intval($starttime);
		$file= fopen(XTCI_LOG, 'a');
		fwrite($file, sprintf("%04d-%02d-%02d %02d:%02d:%09.6f %s %s (%.2fs, %s)\n", $time['tm_year'] + 1900, $time['tm_mon'] + 1, $time['tm_mday'], $time['tm_hour'], $time['tm_min'], $time['tm_sec'] + $usec, $CONF['site']['keyacc_fs'] ,$command, $exectime, $url));
		fclose($file);
	}



	// Decode UTF8 if needed
	if (!XTCI_UTF8)
	{
		$response= utf8_decode($response);
	}

	// Debugging
	if ($xtci_debug)
	{
		echo '<h1>Response</h1><pre>', htmlentities($response), '</pre>';
	}

	// Check CURL response
	if ($response)
	{
		// Close CURL
		curl_close($ch);

		// Check XTCI errorcode
		if (preg_match('%<errorcode>(.*?)</errorcode>%s', $response, $match))
		{
			$errorcode= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -2;
		}

		if (preg_match('%<errortext>(.*?)</errortext>%s', $response, $match))
		{
			$errortext= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errortext= $response;
		}
	}
	else
	{
		// Handle curl error
		$errorcode= -1;
		$errortext= sprintf('#%d: %s (%s)', curl_errno($ch), curl_error($ch), $url);
		curl_close($ch);
	}

	$response=preg_replace('/&apos;/','/&#39;/',$response);

	// Success. Return all data
	return array ($response, $errorcode, $errortext);
}

function xtci2x_xml_request($server, $data, $command)
{
	global $xtci_debug, $CONF;

	$header= sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	$ch= curl_init();
	$url= preg_replace(array ('%^(https://[^/]+)(/.*)$%', '%^(http://[^/]+)(/.*)$%'), array ('\1:'.XTCI_HTTPS_PORT.'\2', '\1:'.XTCI_HTTP_PORT.'\2'), $server['controller']);
	$url= preg_replace(array ('%^(http://[^/]+)(/.*)$%', '%^(http://[^/]+)(/.*)$%'), array ('\1:'.XTCI_HTTPS_PORT.'\2', '\1:'.XTCI_HTTP_PORT.'\2'), $server['controller']);

#	if (XTCI_FORCE_HTTP)
#	{
#		$url= preg_replace('%^https://%', 'http://', $url);
#	}

	curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
	curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	#curl_setopt($ch, CURLOPT_MUTE, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: text/xml'));
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

	if (!XTCI_CHECK_SSL)
	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}

	$postfields= $header.$data."\n";
	
	//Util::Debug($postfields);
	//die();

	if (!XTCI_UTF8)
	{
		$postfields= utf8_encode($postfields);
	}
	/*
	if($command == 'pricelist_request'){echo($postfields); exit; }
	*/
	//$GLOBALS['xml'][] = $postfields;

	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

	// Debugging
	if ($xtci_debug)
	{
		echo '<h1>Request</h1><pre>', htmlentities($header.$data."\n"), '</pre>';
	}

	// Do response
	$starttime= xtci_microtime();
	$response= curl_exec($ch);
	$exectime= xtci_microtime() - $starttime;

	logTimer( $url );
	logTimer( $postfields );
	logTimer( $response );
	logTimer( curl_getinfo( $ch ) );
	

	// Log request if XTCI_LOG is defined
	if (XTCI_LOG)
	{
		$time= localtime($starttime, true);
		$usec= $starttime -intval($starttime);
		$file= fopen(XTCI_LOG, 'a');
		fwrite($file, sprintf("%04d-%02d-%02d %02d:%02d:%09.6f %s %s (%.2fs, %s)\n", $time['tm_year'] + 1900, $time['tm_mon'] + 1, $time['tm_mday'], $time['tm_hour'], $time['tm_min'], $time['tm_sec'] + $usec, $CONF['site']['keyacc_fs'] ,$command, $exectime, $url));
		fclose($file);
	}



	// Decode UTF8 if needed
	if (!XTCI_UTF8)
	{
		$response= utf8_decode($response);
	}

	// Debugging
	if ($xtci_debug)
	{
		echo '<h1>Response</h1><pre>', htmlentities($response), '</pre>';
	}

	// Check CURL response
	if ($response)
	{
		// Close CURL
		curl_close($ch);

		// Check XTCI errorcode
		if (preg_match('%<errorcode>(.*?)</errorcode>%s', $response, $match))
		{
			$errorcode= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -2;
		}

		if (preg_match('%<errortext>(.*?)</errortext>%s', $response, $match))
		{
			$errortext= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errortext= $response;
		}
	}
	else
	{
		// Handle curl error
		$errorcode= -1;
		$errortext= sprintf('#%d: %s (%s)', curl_errno($ch), curl_error($ch), $url);
		curl_close($ch);
	}

	$response=preg_replace('/&apos;/','/&#39;/',$response);

	// Success. Return all data
	return array ($response, $errorcode, $errortext);
}

// ========================================================================
// xtci_hello_request
//
// Requests the list of XTCI communication servers.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $client_name => The name of the client
//	 $client_version => The version of the client
//	 $server_version => The exprected protocol version of the server
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_xtci2_request($server, $client_name= XTCI_CLIENT_NAME, $client_version= XTCI_CLIENT_VERSION, $server_version= XTCI_SERVER_VERSION,$client_id=XTCI_CLIENT_ID,$key_account_id=KEY_ACCOUNT_ID)
{
	

	$request= sprintf("<hello_request>\n"."   <keyacc_id>%s</keyacc_id>\n"."   <client_id>%s</client_id>\n"."   <client_version>%s</client_version>\n"."  <client_name>%s</client_name>\n"."  <server_protocol>%s</server_protocol>\n"."</hello_request>",
	htmlspecialchars($key_account_id),htmlspecialchars($client_id),htmlspecialchars($client_version), htmlspecialchars($client_name), htmlspecialchars($server_version)
	);

	//echo($request); exit;
	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "hello_request");

	$data= array ();

	if (!$errorcode)
	{
		/*
		**  Fetch the controller url: */
		if (preg_match('%<content.*?>.*?<url.*?>(.*?)</url>%s', $response, $match))
		{
#			if(XTCI_FORCE_HTTP)
#			{
#				$data['server']['controller']= 'http://'.xtci_html_decode(trim($match[1]));
#			}
#			else
#			{
				$data['server']['controller']= 'https://'.xtci_html_decode(trim($match[1]));
#			}
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no controller';
		}
		/*
		**  Fetch the DTD url: */
		if (preg_match('%<content.*?>.*?<dtd_url.*?>(.*?)</dtd_url>%s', $response, $match))
		{
			$data['server']['dtd']= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no DTD';
		}
		/*
		**  Fetch the XTCI Server list: */
		if (preg_match_all('%<xtci_server.*?>(.*?)</xtci_server>%s', $response, $matches))
		{
			foreach ($matches[1] as $tmpmatch)
			{
				if (preg_match('%<url.*?>(.*?)</url>%s', $tmpmatch, $match))
				{
					$controller= 'https://'.xtci_html_decode(trim($match[1]));
				}
				else
				{
					$errorcode= -3;
					$errortext= 'Got no controller for an entry in XTCI server list';
				}
				if (preg_match('%<dtd_url.*?>(.*?)</dtd_url>%s', $tmpmatch, $match))
				{
					$dtd= xtci_html_decode(trim($match[1]));
				}
				else
				{
					$errorcode= -3;
					$errortext= 'Got no DTD for an entry in XTCI server list';
				}
				$data['servers'][]= array ('controller' => $controller, 'dtd' => $dtd);
			}
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no XTCI server list';
		}
	}
	return array ($data, $errorcode, $errortext);

} // xtci_hello_request()

// ========================================================================
// xtci_session_request
//
// Requests a session id.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $keyacc_id => Key-Account id
//	 $client_name => The name of the client
//	 $client_version => The version of the client
//	 $server_version => The exprected protocol version of the server
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_session_request($server, $keyacc_id, $client_name= XTCI_CLIENT_NAME, $client_version= XTCI_CLIENT_VERSION, $client_id= XTCI_CLIENT_ID, $server_version= XTCI_SERVER_VERSION, $ticket_id= FALSE, $invalidate_ticket= FALSE, $ticket_valid_seconds= FALSE)
{
	GLOBAL $SESS;
	if(!$keyacc_id)
	{
		$data = '';
		$errorcode = -3;
		$errortext = 'No valid Keyaccount';
		return array ($data, $errorcode, $errortext);
	}
	$request= sprintf("<session_request%s%s%s>\n"."  <keyacc_id>%s</keyacc_id>\n"."  <client_id>%s</client_id>\n"."  <client_version>%s</client_version>\n"."  <client_name>%s</client_name>\n", ((false) ? ' asp_server_name="'.php_uname('n').'"' : '') , ($invalidate_ticket===TRUE) ? ' invalidate_ticket="true"' : '', ($ticket_valid_seconds!==FALSE && $invalidate_ticket===TRUE) ? ' ticket_valid_seconds="'.$ticket_valid_seconds.'"' : '', htmlspecialchars($keyacc_id),htmlspecialchars($client_id),  htmlspecialchars($client_version),htmlspecialchars($client_name));

	/*if ($server_version >= 2.0)
	{
	$request .= sprintf("  <client_id>%s</client_id>\n", htmlspecialchars($client_id));
	}*/
	$request .= sprintf("  <server_protocol>%s</server_protocol>\n", htmlspecialchars($server_version));
	if (isset($SESS['new_locale']) && $SESS['new_locale'])
	{
		$request .= sprintf("  <locale>%s</locale>\n", htmlspecialchars($SESS['new_locale']));
	}
	if ($ticket_id)
	{
		$request .= sprintf("  <ticket_id>%s</ticket_id>\n", $ticket_id);
	}
	$request .= "</session_request>\n";

	//echo($request); exit;
	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "session_request");

	$data= array ();

	if (!$errorcode)
	{
		/*
		**  Fetch the session id: */
		if (preg_match('%<ocsid.*?>(.*?)</ocsid>%s', $response, $match))
		{
			$data['ocsid']= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no session id';
		}
		/*
		**  Fetch the session expire time: */
		if (preg_match('%<valid_until.*?>(.*?)</valid_until>%s', $response, $match))
		{
			$data['valid_until']= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no session expire time';
		}
		/*
		**  Fetch the server protocol: */
		if (preg_match('%<server_protocol.*?>(.*?)</server_protocol>%s', $response, $match))
		{
			$data['server_protocol']= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no server protocol';
		}
		/*
		**  Fetch the serverversion: */
		if (preg_match('%<server_version.*?>(.*?)</server_version>%s', $response, $match))
		{
			$data['server_version']= xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no server version';
		}
	}
	elseif($keyacc_id == 11339)
	{
		if (preg_match('%<ocsid.*?>(.*?)</ocsid>%s', $response, $match))
		{
			$data['ocsid']= xtci_html_decode(trim($match[1]));
		}
	}

	return array ($data, $errorcode, $errortext);

} // xtci_session_request()

// ========================================================================
// xtci_endsession_request
//
// Closes a session.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => session id
//	 $abort => If true, server aborts all running actions of this session
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_endsession_request($server, $ocsid, $abort= FALSE)
{
	$request= sprintf("<endsession_request mode=\"%s\">\n"."  <ocsid>%s</ocsid>\n"."</endsession_request>", $abort ? 'abort' : 'normal', $ocsid);

	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "endsession_request");

	return array (array (), $errorcode, $errortext);
}

// ========================================================================
// xtci_login_request
//
// Perform login.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => session id
//	 $login => Login name (E-Mail address)
//	 $pwd => Login password
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_login_request($server, $ocsid, $login, $pwd)
{
	$request= sprintf("<login_request>\n"."  <ocsid>%s</ocsid>\n"."  <login>%s</login>\n"."  <pwd>%s</pwd>\n"."</login_request>", htmlspecialchars($ocsid), htmlspecialchars($login), htmlspecialchars($pwd));

	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "login_request");

	return array (array (), $errorcode, $errortext);
}


// ========================================================================
// xtci_useradd_request
//
// Creates a new user.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => session id
//	 $user => user data (Assoc array. See XTCI manual for field names)
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_useradd_request($server, $ocsid, $user, $utilisation=FALSE)
{
	//$salutation='';
	isset ($user['email']) ? htmlspecialchars($email=$user['email']) : $email=null;
	isset ($user['pwd']) ? htmlspecialchars($pwd=$user['pwd']) : $pwd=null;
	isset ($user['salutation']) ? htmlspecialchars($salutation=$user['salutation']) : $salutation='undefined';
	isset ($user['firstname']) ? htmlspecialchars($firstname=$user['firstname']) : $firstname='';
	isset ($user['lastname']) ? htmlspecialchars($lastname=$user['lastname']) : $lastname='';
	isset ($user['title']) ? htmlspecialchars($title=$user['title']) : $title='undefined';
	isset ($user['company']) ? htmlspecialchars($company=$user['company']) : $company='';
	isset ($user['street']) ? htmlspecialchars($street=$user['street']) : $street='';
	isset ($user['state_province']) ? htmlspecialchars($state_province=$user['state_province']) : $state_province='';
	isset ($user['zip']) ? htmlspecialchars($zip=$user['zip']) : $zip='';
	isset ($user['city']) ? htmlspecialchars($city=$user['city']) : $city='';
	isset ($user['city_part']) ? htmlspecialchars($city_part=$user['city_part']) : $city_part='';
	isset ($user['country']) ? htmlspecialchars($country=$user['country']) : $country='';
	isset ($user['iso_country']) ? htmlspecialchars($iso_country=$user['iso_country']) : $iso_country='';
	isset ($user['phone']) ? htmlspecialchars($phone=$user['phone']) : $phone='';
	isset ($user['mobile_phone']) ? htmlspecialchars($mobile_phone=$user['mobile_phone']) : $mobile_phone='';
	isset ($user['fax']) ? htmlspecialchars($fax="57883511") : $fax='';
	
	$request = sprintf("<userinsert_request>
			<ocsid>%s</ocsid>
			<login>%s</login>
			<pwd>%s</pwd>
			<doi strategy='sendhash'/>
			<contact_email>%s</contact_email>
			<addresses>
			  <address no='0' type='billing' status='unknown' 
					   salutation='%s' title='%s' firstname='%s' 
					   lastname='%s' company='%s'
					   street='%s' state_province='%s' 
					   zip='%s' city='%s' city_part='%s'
					   country='%s' iso_country='%s'
					   phone='%s' mobile_phone='%s' fax='%s'/>
			  <address no='1' type='shipping' status='unknown' 
					   salutation='%s' title='%s' firstname='%s' 
					   lastname='%s' company='%s'
					   street='%s' state_province='%s' 
					   zip='%s' city='%s' city_part='%s'
					   country='%s' iso_country='%s'
					   phone='%s' mobile_phone='%s' fax='%s'/>
			</addresses>
		  </userinsert_request>",
			htmlspecialchars($ocsid),
			$email,
			$pwd,
			$email,
			$salutation, $title, $firstname, $lastname, $company, $street, $state_province, $zip, $city, $city_part, $country, $iso_country, $phone, $phone, $fax,
			$salutation, $title, $firstname, $lastname, $company, $street, $state_province, $zip, $city, $city_part, $country, $iso_country, $phone, $phone, $fax
		);
	 
	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "userinsert_request");
	$data = array();
	return array ($data, $errorcode, $errortext);

} // xtci_useradd_request()

// ========================================================================
// xtci_userchange_request
//
// Changes user information.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => session id
//	 $user => user data (Assoc array. See XTCI manual for field names)
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_userchange_request($server, $ocsid, $user, $utilisation=FALSE)
{
	
	
	isset ($user['salutation']) ? htmlspecialchars($salutation=$user['salutation']) : $salutation='';
	isset ($user['firstname']) ? htmlspecialchars($firstname=$user['firstname']) : $firstname='';
	isset ($user['lastname']) ? htmlspecialchars($lastname=$user['lastname']) : $lastname='';
	isset ($user['title']) ? htmlspecialchars($title=$user['title']) : $title='';
	isset ($user['company']) ? htmlspecialchars($company=$user['company']) : $company='';
	isset ($user['street']) ? htmlspecialchars($street=$user['street']) : $street='';
	isset ($user['state_province']) ? htmlspecialchars($state_province=$user['state_province']) : $state_province='';
	isset ($user['zip']) ? htmlspecialchars($zip=$user['zip']) : $zip='';
	isset ($user['city']) ? htmlspecialchars($city=$user['city']) : $city='';
	isset ($user['city_part']) ? htmlspecialchars($city_part=$user['city_part']) : $city_part='';
	isset ($user['country']) ? htmlspecialchars($country=$user['country']) : $country='';
	isset ($user['iso_country']) ? htmlspecialchars($iso_country=$user['iso_country']) : $iso_country='';
	isset ($user['phone']) ? htmlspecialchars($phone=$user['phone']) : $phone='';
	isset ($user['mobile_phone']) ? htmlspecialchars($mobile_phone=$user['mobile_phone']) : $mobile_phone='';
	isset ($user['fax']) ? htmlspecialchars($fax=$user['fax']) : $fax='';
	if(isset ($user['billing_id'])){
		 htmlspecialchars($address_id=$user['billing_id']) ; 
		 htmlspecialchars($address_type='billing') ;  
	}
	if(isset ($user['shipping_id'])){
		 htmlspecialchars($address_id=$user['shipping_id']) ; 
		 htmlspecialchars($address_type='shipping') ;  
	}
	

	$request= "<userupdate_request>\n";
	$request .= sprintf("  <ocsid>%s</ocsid>\n"."  <login>%s</login>\n  <doi strategy='none'></doi>\n", htmlspecialchars($ocsid), isset ($user['email']) ? htmlspecialchars($user['email']) : '');

	if (isset ($user['pwd']) && $user['pwd'])
	{
		$request .= sprintf("  <pwd>%s</pwd>\n", $user['pwd']);
	}

	if($utilisation==TRUE)
	{
		if(!$user['accept_data_utilisation'])
		{
			$accept_data_utilisation = "no";
		}
		elseif($user['accept_data_utilisation']==1)
		{
			$accept_data_utilisation = "yes";
		}
	}

	
	$request .= sprintf("  <sec_question>%s</sec_question>\n"."  <sec_answer>%s</sec_answer>\n"."  <customer_card>%s</customer_card>\n"."  <addresses>\n  <address no='0' action='update' id='".$address_id."' type='billing' status='unknown' salutation='".$salutation."' title='".$title."' firstname='".$firstname."' lastname='".$lastname."' company='".$company."' street='".$street."' state_province='".$state_province."' zip='".$zip."' city='".$city."' city_part='".$city_part."' country='".$country."' iso_country='".$iso_country."' phone='".$phone."' mobile_phone='".$mobile_phone."' fax='".$fax."'></address>\n</addresses>\n"."</userupdate_request>",isset ($user['sec_question']) ? htmlspecialchars($user['sec_question']) : '', isset ($user['sec_answer']) ? htmlspecialchars($user['sec_answer']) : '', isset ($user['customer_card']) ? htmlspecialchars($user['customer_card']) : '');

	list ($response, $errorcode, $errortext)= $response = xtci_xml_request($server, $request, "userupdate_request");

	$data = array();

	return array($data, $errorcode, $errortext);

} // xtci_userchange_request()

// ========================================================================
// xtci_userinfo_request
//
// Get user data.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => session id
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, user-array if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci2x_userinfo_request($server, $ocsid) {
	
	$request = sprintf("<userinfo_request>\n"."  <ocsid>%s</ocsid>\n"."</userinfo_request>", htmlspecialchars($ocsid));
	list($response, $errorcode, $errortext) = $response= xtci2x_xml_request($server, $request, "userinfo_request");
	
	try {
		$data = simplexml_load_string( $response );
	} catch (Exception $e) {
		$errorcode = -1;
		$errortext = $e->getMessage();
		$data = array();
	}
	
	return array ($data, $errorcode, $errortext, $response);
	
}

function xtci_userinfo_request($server, $ocsid, $utilisation=FALSE, $loyality=FALSE)
{
	if($utilisation == TRUE)
	{
	  $request = sprintf("<user_request accept_data_utilisation=\"yes\">\n"."  <ocsid>%s</ocsid>\n"."</user_request>", htmlspecialchars($ocsid));
	}
	else
	{
	  $request = sprintf("<user_request%s%s>\n"."  <ocsid>%s</ocsid>\n"."</user_request>", ($utilisation==TRUE) ? ' accept_data_utilisation="yes"' : '', ($loyality==TRUE) ? ' loyalty_partner_data="yes"' : '', htmlspecialchars($ocsid));
	}

	list($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "user_request");

	$data = array ();

	if (!$errorcode)
	{
		/*
		**  Fetch the user data: */
		if (preg_match('%<login*?>(.*?)</login>%s', $response, $match))
		{
			$data['login'] = xtci_html_decode(trim($match[1]));
		}
		else
		{
			$errorcode = -3;
			$errortext = 'Got no email address';
		}
							if (preg_match('%<customer_card.*?>(.*?)</customer_card>%s', $response, $match))
								{
									$data['customer_card'] = xtci_html_decode(trim($match[1]));
								}
								else
								{
									$data['customer_card']= '';
								}
								if (preg_match('%<last_location_id.*?>(.*?)</last_location_id>%s', $response, $match))
								{
									$data['favorite_loc_id']= xtci_html_decode(trim($match[1]));
								}
								else
								{
									$data['favorite_loc_id']= '';
								}
								if (preg_match('%<accept_news_letter.*?>(.*?)</accept_news_letter>%s', $response, $match))
								{
									$data['accept_news_letter']= xtci_html_decode(trim($match[1])) == '1';
								}
								else
								{
									$data['accept_news_letter']= false;
								}
								
								if (preg_match('%<user_id.*?>(.*?)</user_id>%s', $address_element, $match))
								{
									$data['user_id']= xtci_html_decode(trim($match[1]));
								}
								else
								{
									$data['user_id']= 0;
								}
		if(preg_match('%<addresses>(.*?)</addresses>%s', $response, $matches))
		{
			if (preg_match_all('%<address (.*?)/>%s', $matches[1], $address_elements))
				{
				
						foreach ($address_elements[1] as $address_element)
						{
							
							if (preg_match('%type="(.*?)"%s', $address_element, $match))
								{
									 $type= xtci_html_decode(trim($match[1]));

								}
								if($type=='billing'){
									
									if (preg_match('%id="(.*?)"%s', $address_element, $match))
									{
										$data["id"] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['id'] = '';
									}
									if (preg_match('%status="(.*?)"%s', $address_element, $match))
									{
										$data["status"] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['status'] = '';
									}
									
									if (preg_match('%salutation="(.*?)"%s', $address_element, $match))
									{
										$data['salutation'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['salutation']= '';
									}
									if (preg_match('%firstname="(.*?)"%s', $address_element, $match))
									{
										$data['firstname'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['firstname'] = '';
									}
									if (preg_match('%lastname="(.*?)"%s', $address_element, $match))
									{
										$data['lastname'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['lastname'] = '';
									}
									if (preg_match('%company="(.*?)"%s', $address_element, $match))
									{
										$data['company_name'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['company_name'] = '';
									}
									if (preg_match('%street="(.*?)"%s', $address_element, $match))
									{
										$data['street'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['street'] = '';
									}
									if (preg_match('%zip="(.*?)"%s', $address_element, $match))
									{
										$data['zip'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['zip'] = '';
									}
									if (preg_match('%city="(.*?)"%s', $address_element, $match))
									{
										$data['city'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['city'] = '';
									}
									if (preg_match('%city_part="(.*?)"%s', $address_element, $match))
									{
										$data['city_part'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['city_part'] = '';
									}
									if (preg_match('%country="(.*?)"%s', $address_element, $match))
									{
										$data['country'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['country'] = '';
									}
									if (preg_match('%iso_country="(.*?)"%s', $address_element, $match))
									{
										$data['iso_country'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['iso_country'] = '';
									}
									if (preg_match('%phone="(.*?)"%s', $address_element, $match))
									{
										$data['phone'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['phone'] = '';
									}
									if (preg_match('%mobile_phone="(.*?)"%s', $address_element, $match))
									{
										$data['cellularphone'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['cellularphone'] = '';
									}
									
									if (preg_match('%state_province="(.*?)"%s', $address_element, $match))
									{
										$data['state_province'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['state_province'] = '';
									}
									
							
									if (preg_match('%fax="(.*?)"%s', $address_element, $match))
									{
										$data['fax']= xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['fax']= '';
									}
								}
								
								
								if($type=='billing'){
									
									if (preg_match('%id="(.*?)"%s', $address_element, $match))
									{
										$data['billing']["id"] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['id'] = '';
									}
									if (preg_match('%status="(.*?)"%s', $address_element, $match))
									{
										$data['billing']["status"] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['status'] = '';
									}
									
									if (preg_match('%salutation="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['salutation'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['salutation']= '';
									}
									if (preg_match('%firstname="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['firstname'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['firstname'] = '';
									}
									if (preg_match('%lastname="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['lastname'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['lastname'] = '';
									}
									if (preg_match('%company="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['company_name'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['company_name'] = '';
									}
									if (preg_match('%street="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['street'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['street'] = '';
									}
									if (preg_match('%zip="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['zip'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['zip'] = '';
									}
									if (preg_match('%city="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['city'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['city'] = '';
									}
									if (preg_match('%city_part="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['city_part'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['city_part'] = '';
									}
									if (preg_match('%country="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['country'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['country'] = '';
									}
									if (preg_match('%iso_country="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['iso_country'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['iso_country'] = '';
									}
									if (preg_match('%phone="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['phone'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['phone'] = '';
									}
									if (preg_match('%mobile_phone="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['cellularphone'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['cellularphone'] = '';
									}
									
									if (preg_match('%state_province="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['state_province'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['state_province'] = '';
									}
									
							
									if (preg_match('%fax="(.*?)"%s', $address_element, $match))
									{
										$data['billing']['fax']= xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['billing']['fax']= '';
									}
								}
								
								
								if($type=='shipping'){
									
									if (preg_match('%id="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']["id"] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['id'] = '';
									}
									if (preg_match('%status="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']["status"] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['status'] = '';
									}
									
									if (preg_match('%salutation="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['salutation'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['salutation']= '';
									}
									if (preg_match('%firstname="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['firstname'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['firstname'] = '';
									}
									if (preg_match('%lastname="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['lastname'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['lastname'] = '';
									}
									if (preg_match('%company="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['company_name'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['company_name'] = '';
									}
									if (preg_match('%street="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['street'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['street'] = '';
									}
									if (preg_match('%zip="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['zip'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['zip'] = '';
									}
									if (preg_match('%city="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['city'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['city'] = '';
									}
									if (preg_match('%city_part="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['city_part'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['city_part'] = '';
									}
									if (preg_match('%country="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['country'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['country'] = '';
									}
									if (preg_match('%iso_country="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['iso_country'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['iso_country'] = '';
									}
									if (preg_match('%phone="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['phone'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['phone'] = '';
									}
									if (preg_match('%mobile_phone="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['cellularphone'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['cellularphone'] = '';
									}
									
									if (preg_match('%state_province="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['state_province'] = xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['state_province'] = '';
									}
									
							
									if (preg_match('%fax="(.*?)"%s', $address_element, $match))
									{
										$data['shipping']['fax']= xtci_html_decode(trim($match[1]));
									}
									else
									{
										$data['shipping']['fax']= '';
									}
								}
								
						}
			
				}
		}
		
	}
	return array ($data, $errorcode, $errortext);

} // xtci_userinfo_request()


// =======================================================================
// xtci_addressinfo_request
//
// Returns all available addresses of an registered user.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => session id
//	 $type => The type of the desired address(es)
//			  Possible types are "billing" and "shipping".
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => An array of the following format:
//			  array(
//				  'billing' => array(
//					  #billing_address_information#
//				  ),
//				  'shipping' => array(
//					  0 => array(
//						  #shipping_address_data#
//					  ),
//					  1 => ...
//				  )
//			  )
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text
//
function xtci_addressinfo_request($server, $ocsid, $type)
{
	$request = sprintf('<addressinfo_request'.($type != FALSE ? ' type="'.$type.'"' : '').'>'.
		"\n  <ocsid>%s</ocsid>"."\n</addressinfo_request>",
		htmlspecialchars($ocsid));

	list ($response, $errorcode, $errortext) =
		xtci_xml_request($server, $request, 'addressinfo_request');

	switch ($type) {
		case FALSE:
			$data = array('billing' => FALSE, 'shipping' => array());
			break;
		case 'billing':
			$data = array('billing' => FALSE);
			break;
		case 'shipping':
			$data = array('shipping' => array());
	}
	if (!$errorcode) {
		/*
		**  Fetch the addresses: */
		if (preg_match_all('%<address_data(.*?)/>%s', $response, $matches))
		{
			/*
			**  Fetch the attributes of each address: */
			foreach ($matches[1] as $tmpmatch)
			{
				$tmp = array();

				if (preg_match('%address_id="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['address_id'] = xtci_html_decode(trim($match[1]));

				}
				else
				{
					$errorcode = -3;
					$errortext = 'Got no id for an address in address list.';
				}
				if (preg_match('%type="(.*?)"%s', $tmpmatch, $match))
				{
					$type = xtci_html_decode(trim($match[1]));

				}
				else
				{
					$errorcode = -3;
					$errortext = 'Got no type for an address in address list.';
				}

				if (preg_match('%description="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['description'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%salutation="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['salutation'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%title="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['title'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%firstname="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['firstname'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%lastname="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['lastname'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%street="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['street'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%zip="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['zip'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%city="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['city'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%city_part="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['city_part'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%country="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['country'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%iso_country="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['iso_country'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%phone="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['phone'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%cellularphone="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['cellularphone'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%company_name="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['company_name'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%state_province="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['state_province'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%fax="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['fax'] = xtci_html_decode(trim($match[1]));
				}
				if (preg_match('%imei="(.*?)"%s', $tmpmatch, $match))
				{
					$tmp['imei'] = xtci_html_decode(trim($match[1]));
				}
				switch ($type)
				{
					case 'billing':
						if ($data['billing'] == FALSE)
						{
							$data['billing'] = $tmp;
						}
						else
						{
							$errorcode = -3;
							$errortext = 'Got more than one billing address.';
						}
						break;

					case 'shipping':
						$data['shipping'][] = $tmp;
						break;
				}
			}
			if (isset($data['billing']) && $data['billing'] == FALSE)
			{
				$errorcode= -3;
				$errortext= 'Got no billing address.';
			}
		}
	}
	return array($data, $errorcode, $errortext);

} // xtci_addressinfo_request()

// ========================================================================
// xtci_version_request
//
// Requests download link to client.
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $keyacc_id => Key-Account id
//	 $os => Operating system
//	 $client_name => The name of the client
//	 $client_version => The version of the client
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_version_request($server, $keyacc_id, $os, $client_id= XTCI_CLIENT_ID,$client_name= XTCI_CLIENT_NAME, $client_version= XTCI_CLIENT_VERSION)
{
	//$request= sprintf("<version_request>\n"."  <client_name>%s</client_name>\n"."  <client_version>%s</client_version>\n"."  <keyacc_id>%s</keyacc_id>\n"."  <os>%s</os>\n"."</version_request>", htmlspecialchars($client_name), htmlspecialchars($client_version), htmlspecialchars($keyacc_id), htmlspecialchars($os));
	$request= sprintf("<version_request>\n"."  <client version=\"%s\" id=\"%s\" name=\"%s\"/>\n  <keyacc_id>%s</keyacc_id>\n"."  <os>%s</os>\n"."</version_request>", htmlspecialchars($client_version), htmlspecialchars($client_id), htmlspecialchars($client_name), htmlspecialchars($keyacc_id), htmlspecialchars($os));
//die($request);
	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "version_request");
//var_dump($response);
	$data= array ();

	if (!$errorcode)
	{
		if (preg_match('%<update version="(.*?)"%s', $response, $match))
			$data['version']= xtci_html_decode(trim($match[1]));
		else
		{
			$errorcode= -3;
			$errortext= 'Got no actual version';
		}

		if (preg_match_all('%<update version=".*" link="(.*?)"%s', $response, $matches))
		{
			$data['link']= $matches[1][0];
		}
		else
		{
			$errorcode= -3;
			$errortext= 'Got no links';
		}
	}

	return array ($data, $errorcode, $errortext);
}
// ========================================================================
// xtci_sendpassword_request
//
// Sends new Password
//
// Parameters:
//	 $server => Array with server definition (See XTCI_SERVERS global)
//	 $ocsid => Session ID
//	 $login => E-Mail address
// Return:
//	 array($response, $errorcode, $errortext)
//		 $data => Undefined if error, data if success
//		 $errorcode => XTCI error code (See XTCI documentation)
//		 $errortext => XTCI error text

function xtci_sendpassword_request($server, $ocsid, $login)
{
	$request= sprintf("<sendnewpassword_request>\n"."  <ocsid>%s</ocsid>\n"."  <login>%s</login>\n"."</sendnewpassword_request>", htmlspecialchars($ocsid), htmlspecialchars($login));

	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "sendnewpassword_request");

	$data= array ();

	return array ($data, $errorcode, $errortext);
}


function xtci_orderingdetails_request($server){
	
	$request = sprintf(
		'<orderingdetails_request>
			<keyacc_id>%s</keyacc_id>
			<client_id>%s</client_id>
			<client_version>%s</client_version>
			<client_name>%s</client_name>
		  </orderingdetails_request>',
		  KEY_ACCOUNT_ID,
		  XTCI_CLIENT_ID,
		  XTCI_CLIENT_VERSION,
		  XTCI_CLIENT_NAME
		  );
	
	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "orderingdetails_request");

	
	return array( $data, $errorcode, $errortext );
}



function xtci_pricelist_request( $server, $group = "1" ){
	
	
	$request = sprintf(
		'<pricelist_request timestamp="2013-09-08T14:08:34">
			<keyacc_id>%s</keyacc_id>
			<client_id>%s</client_id>
			<client_version>%s</client_version>
			<client_name>%s</client_name>
			<group>%s</group>
			<input_source>0</input_source>
		  </pricelist_request>',
		  KEY_ACCOUNT_ID,
		  XTCI_CLIENT_ID,
		  XTCI_CLIENT_VERSION,
		  XTCI_CLIENT_NAME,
		  $group
		  );
	
	list ($response, $errorcode, $errortext)= $response= xtci_xml_request($server, $request, "pricelist_request");

	
	return array( $data, $errorcode, $errortext );
	
}


?>
