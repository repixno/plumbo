<?PHP

    /***********************************************
     *For resetting mediaclip projects to a
     *previous state
     *@author Tor Inge Lovland <tor.inge@eurofoto.no>
     ************************************************/
    
   import( 'pages.admin' );
   model('user.mediaclipsession');
   
   class EditMediaclip extends AdminPage implements IView {
      protected $template = 'mediaclip.edit';
      private $secret_code = "yz9987gd";
	  
      public function Execute($id) {
        
        
        
        $art_type = DB::query( "SELECT article_id FROM mediaclip_orders WHERE id = ? ", $id )->fetchSingle();
        $project_xml = DB::query( "SELECT project_xml FROM mediaclip_orders WHERE id = ? ", $id )->fetchSingle();
        
        $module = DB::query( "SELECT type FROM mediaclip_projects WHERE id in ( SELECT project_id FROM mediaclip_orders WHERE id = ? )", $id )->fetchSingle();
        
        $environment = $this->setupEnvironment();
        
        $efcustomer = array(
			'art_type' => $art_type,
			'Sessionid' => $environment['sessid'],
			'customerid' => $environment['customerid'],
			'idcheck' => $environment['idcheck'],
			'location' => "http://marie.repix.no",
			'orderid' => $id,
			'unique' => $unique->id,
			'predefinert' => 0,
			'server' => "jasmin.repix.no",
			'adminuser' => "1",
		);
		
		
		
		$efinfo = serialize( $efcustomer );
        $logindata =  Login::data();
        
        $sessionid = $logindata['sessionid'];
        
        //Util::Debug($sessionid);
        
        
        $check = DB::query( "SELECT id FROM mediaclip_session WHERE sessionid = ?" , $sessionid )->fetchSingle();
        
        //Util::Debug( $check );
	
        //exit;
       
            $mcsession = new DBmediaclipSession();     
        
		  
		
		 
        
		$mcsession->sessionid = $sessionid;
		$mcsession->userid = (int)$logindata['userid'];
		$mcsession->loggedin = true;
		$mcsession->browser = $_SERVER['HTTP_USER_AGENT'];
		$mcsession->efcustomer = $efinfo;
	
		$mcsession->save();
		
		$designers = "html5";
	//	$designers = "flash,html5";
		
        /*$this->flashurl = 'http://bente.repix.no/ECommerceBridge/Modules/ViewModule.aspx?swfFile='. $module . 'Module.swf&culture=nb-NO'.
                                                    '&art_type=' . $art_type .
													'&adminuser=' . $logindata['userid'] .
                                                    '&sessionId=' . $sessionid;*/
		 
		 //har satt å hente frå live mappa sidan ef3 sin 5.2 er den som ligge på mia
		// $this->flashurl = 'http://bente.repix.no/ECommerceBridge/designer/
		 /*$this->flashurl = 'http://bente.repix.no/ECommerceBridge/designer/
									?module=' . $module .
									'&culture=nb-NO' .
									'&adminuser=' . $logindata['userid'] .
									'&designers=flash,html5' .
									'&art_type=' . $art_type .
									'&sessionId=' . $sessionid;*/
									
			if( $module == 'Greetingcard' && strpos( $project_xml, 'xmlns:gifting="Mediaclip.Gifting.Model' ) ){
				$module = "Gifting";
			}
			
			
				if( $module == 'GreetingCard' && strpos( $project_xml, 'xmlns:gifting="Mediaclip.Gifting.Model' ) ){
				$module = "Gifting";
			}
			
			
			if( $art_type == '897' && strpos( $project_xml, 'xmlns:photobook="Mediaclip.Photobook.Model' ) ){
				$module = "Photobook";
				
			}
			
			
			if( $module == 'GreetingCard' && strpos( $project_xml, 'xmlns:gifting="Mediaclip.Gifting.Model' ) ){
				$module = "Gifting";
			}
			
			
			
			
			
			
			
								
			$this->flashurl = 'http://jasmin.repix.no/ECommerceBridge/designer
									?module=' . $module .
									//'&themeUrl=' . $themeUrl .
									'&culture=nb-NO' .
									'&adminuser=' . $logindata['userid'] .
									'&designers=' . $designers .
									'&art_type=' . $art_type .
									'&sessionId=' . $sessionid.$MCProductId;						
			
      }
		
		
      
      
      	public function setupEnvironment(){
   
            $mSession = md5(date("U"));
            $sessid = Session::id();
            $mc_lang = 'nb-NO';
            $user_id = Login::userid();
            $user = new User( $user_id );
            $idcheck = md5( $user->registrert . $this->secret_code );
            $customerid = base64_encode($user_id . "-" . $mSession);
            $tmp_session = md5($user_id . "-" . $m_session);
          
            return array(
                "sessid" => $sessid, 
                "customerid" => $customerid,
                "idcheck" => $idcheck
            );	
        }
      
   }
   
?>
