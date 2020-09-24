<?php
class XTCiForSSO{

	public $XTCI_SERVERS;
	var $session;
	var $keyaccount;
	var $now;

	function XTCiForSSO( ){

		if (!isset($XTCI_SERVERS)){
			$XTCI_SERVERS = array(
					array(
						'controller' => 'http://orderport01.photoprintit.de/xtci'.XTCI_SERVER_VERSION.'/controller',
						'dtd' => 'http://orderport01.photoprintit.de/xtci'.XTCI_SERVER_VERSION.'/dtd'
					),
					array(
						'controller' => 'http://orderport01.photoprintit.de/xtci'.XTCI_SERVER_VERSION.'/controller',
						'dtd' => 'http://orderport01.photoprintit.de/xtci'.XTCI_SERVER_VERSION.'/dtd'
					)
				);
		}
		
		$this->now = time();
		$this->XTCI_SERVERS = $XTCI_SERVERS[0];
		$this->keyaccount = KEY_ACCOUNT_ID;
		$this->session = $_SESSION['ses_xtciGlobal'][$this->keyaccount]; // I am keeping track the XTCI Session id and other data in local session variable. You can do according to your enviornment
	}

	function xtci_server(){

		if(!$this->session['servers']){
			$response = xtci_xtci2_request( $this->XTCI_SERVERS );
			if( !($this->valid_response($response)) ){
				return false;
			}
			$this->session['servers'] = $response[0]['server'];
			$this->update_session();
		}
		return $this->session['servers'];
	}

	function xtci_session( $forceUpdate = false ){
		if( !$this->session['ocsid'] || ($this->session['ocsid']['valid_until'] < $this->now) || $forceUpdate ){
			$response =  xtci_session_request( $this->xtci_server() , $this->keyaccount );
			if($response[1]){
				if($response[1] != -3){
					$this->valid_response( $response );
					return false;
				}
			}

			$this->session['ocsid']['id'] = $response[0]['ocsid'];
			if( !($this->session['ocsid']['valid_until'] = $this->oscid_timestamp($response[0]['valid_until'])) ){
				return false;
			}
			$this->session['ocsid']['server_protocol'] = $response[0]['server_protocol'];
			$this->session['ocsid']['server_version'] = $response[0]['server_version'];
			$this->update_session();

		}
		return $this->session['ocsid']['id'];
	}
	
	function set_xtci_session( $osci ){
		
		$response =  xtci_session_request( $this->xtci_server() , $this->keyaccount );
		
		Util::Debug( $response );
		
		$this->session['ocsid']['id'] = $osci;
		$this->session['ocsid']['valid_until'] = $this->oscid_timestamp( $response[0]['valid_until'] );		
	
	}
	
	function xtci_endsession($abort = false){

		$response = xtci_endsession_request( $this->xtci_server() , $this->xtci_session() , $abort );
		if( !($this->valid_response($response)) ){
			if( $response[1] != '4' ){		//4 = invalid session ID
				return false;
			}
		}
		$this->session['ocsid'] = NULL;
		return true;
	}

	function xtci_login( $email,$pass){
		// EF needs to check if user already logedin
		$response = xtci_login_request( $this->xtci_server() , $this->xtci_session() , $email , $pass ) ;
		return $response;
	}

	
	
	function xtci_userinfo(){
		$response =  xtci_userinfo_request( $this->xtci_server() , $this->xtci_session() );
		return $response;
	}

	function xtci_useradd( $user ){
		$response =  xtci_useradd_request( $this->xtci_server() , $this->xtci_session() , $user );
		return $response;
	}

	function xtci_userchange($userdata){
		$response =  xtci_userchange_request( $this->xtci_server() , $this->xtci_session() , $userdata );
		return $response;
	}


	function xtci_sendpassword($email){
		$response = xtci_sendpassword_request( $this->xtci_server() , $this->xtci_session() , $email );
		if( !($this->valid_response( $response )) ){
			return $response;
		}
		return $response;
	}



	function valid_response($response , $file = false , $line = false ){

		if($response[1]){
			if($response[1] != '53' && $response[1] != '2' && $response[1] != '81' ){		// 53 = all data uploaded		//2 = user does not exist or is not active (login)		//81 = user does not exist (new pass)
				// LOG ERROR
			}
			return false;
		}
		return $response;
	}

	function oscid_timestamp($valid_until){

		$pat = '/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})/';
		if( preg_match( $pat , $valid_until , $matches ) ){
			return mktime( $matches[4] , $matches[5] , $matches[6] , $matches[2] , $matches[3] , $matches[1] ) - 7200; //subtract 2 hours for possible daylight savings issue + systime difference
		}
		return false;

	}

	private function update_session(){
		$_SESSION['ses_xtciGlobal'][$this->keyaccount] = $this->session;
	}

	function clear(){
		$this->session = array( );
		$_SESSION['ses_xtci'] = array();
		$_SESSION['ses_xtciGlobal'] = array();
	}
	
	function pricelist($group = "1"){
		
		$response = xtci_pricelist_request( $this->xtci_server(), $group );
		/*if( !($this->valid_response( $response )) ){
			return $response;
		}*/
		return $response;
	}
	
	function orderingdetails(){
		
		$response = xtci_orderingdetails_request( $this->xtci_server() );
		
		return $response;
		
	}

}
?>