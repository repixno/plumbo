
	$serviceurl = "http://www.picnik.com/service";
	
	$apikey = "a77f917f0058eb066a87af4d8a540960";
	
	$params['_apikey'] = $apikey;
	$params['_export'] = 'http://eurofoto.no/picnic';
	$params['_export_title'] = "Bilde fra eurofoto";
	$params['_close_target'] = 'http://eurofoto.no/account';
	$params['_import'] = 'http://eurofoto.no/image';
	$params['_redirect'] = 'http://eurofoto.no/account, ;
	$params['_host_name'] = 'eurofoto';
	$params['_exclude'] = 'out';
	
	if ( isset( $_FILES['file'] ) ) {
	
		$imagetmpfilename = $_FILES['file']['tmp_name'];	
		$imagedata = file_get_contents( $imagetmpfilename );
		
		file_put_contents( "img/blabla.jpg", $image_data );	
		
	}
	
	if (isset($_POST['address'])) {
	
		$address = htmlentities( $_POST['address'] );
		file_put_contents( "img/address.txt", $address );
		
	} else {
	
		$address = @file_get_contents( "img/address.txt" );
		
	}
