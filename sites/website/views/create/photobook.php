<?php
// vertion: from 04.12
//model("user.project");
model("user.projectunique");
import("website.project");
model('user.mediaclipsession');
library( 'mobiledetect.Mobile_Detect' );
config('website.mediaclipkobling');

class MediaclipProject extends WebPage implements IView{

	protected $template = 'create.photobook';
	protected $module = "Photobook";
	//todo-> add to settings
	private $secret_code = "yz9987gd";
	private $anonymous = 639866;




	public function Execute(){
      
		if( !Dispatcher::getSiteSetting( 'mediaclip.nologinrequired' ) ) {
			if( !Login::isLoggedIn() ) {
				Session::pipe( 'loginredirecturl', sprintf( '/create/%s', strtolower( $this->module ) ) );
				relocate( '/login/' );
				die();    
			}
         
		}
   	
		$this->setupEnvironment();
   
		$user = new User( Login::userid() );
		$projects = array();
		foreach ( $user->listProjects() as $project ) {
   
			$type = 'mediaclip';
         
			$projects[] = array(
				'id' => $project->id,
				'title' => $project->title,
				'description' => $project->description,
				'url' => '',
				'isShared' => $project->share ? true : false,
				'date' => strftime( '%e. %B %Y', strtotime( $project->created ) ),
				'timestamp' => strtotime( $project->created ),
				'product' => $project->product->asArray(),
				'type' => $type,
				'editurl' => '/create/edit/' . $project->id
   		     );
		}
      
	   $this->projects = $projects;
	   
	}
   
	public function setupEnvironment(){
   
		$mSession = md5(date("U"));
		$sessid = Session::id();
		$mc_lang = $this->DefineLanguage();
		$user_id = $this->DefineUserid();
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
   
	public function DefineLanguage(){
   
		switch(i18n::languageCode()){
	   		case "no_NO":
	   		case "nn_NO":
	   		case "nb_NO":
	   			$mc_lang = "nb-NO";
			break;
			case "sv_SE":
				$mc_lang = "sv-SE";
			break;
			case "da_DK":
				$mc_lang = "da-DK";
	   		break;
	   		case "en_US":
	   		case "en_GB":
	   		case "fi_FI":
	   			$mc_lang = "en-CA";
	   		break;
   			default : $mc_lang = "nb-NO";
   		}
		
   		//return $mc_lang;
		
		return "nb-NO";
   }
   
	public function DefineUserid(){
   		//add support for not_loggedin
   		if ( Login::isLoggedIn() ) {
   		   return Login::userid();
   		} else {
   		   return $this->anonymous;
   		}
	}
   
	public function create( $art_type=0, $productoptionid = 0, $template = null ){
		if( !Dispatcher::getSiteSetting( 'mediaclip.nologinrequired' ) ) {
      
        if( !Login::isLoggedIn() ) {
            
				Session::pipe( 'loginredirecturl', sprintf( '/create/%s/create/%s/%s', strtolower( $this->module ), $art_type, $productoptionid, $template ) );
				relocate( '/login/' );
				die();
            
			}	
		}
	   
		$title = $_POST['title'];
	  
		$created = new Project();
	  
		$created->userid = $this->DefineUserid();
		$created->title = $title;
		$created->created = date( 'Y-m-d H:i:s' );
		$created->type = $this->module;
		if( (int) $productoptionid > 0 ) {
			$created->productoptionid = $productoptionid;
		}
		$created->save();
	  
		//$this->edit($created->id);
	   
		relocate("/create/". strtolower( $this->module)."/edit/" . $created->id ."/".$art_type."/".$template);
	  
	}
	  
	public function edit( $id, $art_type=0, $template=0 ){
        
        $arttemplate = $art_type;
        
        if( strpos( $art_type, '-' ) ){
			$artoptions = explode( '-', $art_type );
			$art_type = $artoptions[0];
		}
        
		
		 if( !Dispatcher::getSiteSetting( 'mediaclip.nologinrequired' ) ) {
			
			if( !Login::isLoggedIn() ) {
			   
			   Session::pipe( 'loginredirecturl', sprintf( '/create/%s/edit/%s/%s', strtolower( $this->module ), $id, $art_type ) );
			   relocate( '/login/' );
			   die();
			   
			}
			
		 }
	   
	   
		 $environment = $this->setupEnvironment();
		

	  
		if(!Login::isLoggedIn() && Session::get('login_warning')!="false"){
			 $this->login_warning = "true";
			 Session::set('login_warning','false');
		}else{
			$this->login_warning = "";
		}
		 
		$this->sharedproject = Session::pipe( "sharedproject" );
	  
		$this->settemplate("create.mediaclip");
		
		$mediaclip_server = Settings::Get( 'mediaclip', 'server', 'mia.eurofoto.no' );
	   
		$environment = $this->setupEnvironment();
	  
		$project = new Project($id);
	   
		/*if( $project->userid == 639866 && $id <  1043101  ){	
			   relocate( sprintf( '/create/%s/create/%s', strtolower( $this->module ), $art_type ) );	   
		}*/
	  
	  
		//$_SESSION["mediaclip"]["userid"] = $this->DefineUserid();
		//$_SESSION["mediaclip"]["title"] = $project->title;
	  
		$location = WebsiteHelper::rootBaseUrl();
		$version = "3";
		$server = exec("hostname") . ".repix.no";
	  
		if ( $project->userid != $this->DefineUserid() ) { 
			$tmparttype = $art_type ? $art_type . '/' : '';
			relocate( '/create/' . strtolower( $this->module) . '/create/' . $tmparttype );
		  
		}
	  
		$unique = new DBUserProjectUnique();
		$unique->userid = $this->DefineUserid();
		$unique->project = $id;
		$unique->host = $location;
		$unique->save();
		 /*
		$version = new DBUserProjectVersions();
		$version->project_id = $project->id;
		$version->project_xml = $project->project_xml;
		$version->save();	
		*/
		$this->id = $id;
	  
		if ( strlen( $project->title ) ) {
		  $this->title = $project->title;
		}
		
		
		if( !$art_type || $art_type == 0 ){
			//$art_type = Project::productInfo($project->productid);
			if( strlen( $project->productid ) > 10 ){
				$art_type = substr( $project->productid, 0, $length-3 );
			}else{
				$art_type =  $project->productid;
			}
			
			$articleid = $project->productid;
			
		}
		
		//lag inn i skipproductarray 28.08: 77035,7031,7032,7033,7036,7037,7034,980,981,982,983,984,985,986,7446 
		if( strlen( $project->project_xml ) <  4 ){
		   $skipproductArray = array( 7113000,7136,6030,1015,3035, 3036, 3037, 3038, 8035,8036,8037,7010,7011,7015,8001,8002,7456,969,968, 7116,7115, 7113,939,940, 7105, 7233, 7237, 7238, 7239, 7240 , 7241, 8071, 8072, 8073, 8074, 8075, 8076, 8077, 8078, 7234, 7274, 7275, 7309, 7317, 7318, 7319, 7346,7035,7031,7032,7033,7036, 7037,7034,980,981,982,983,984,985,986,7446,7244,909  );
		   if( in_array( $art_type , $skipproductArray ) ){  
			  //$MCProductId = "&MCProductId=" . $art_type . "000";
			  $MCProductId = "&productId=" . $art_type . "000";
		   }
		   //$MCProductId .= "&MCThemeUrl={library}GreetingCard\\Themes\\10x18_Landscape\\11_Christmas\\Jul05\\theme.xml&MCMode=full";
		}
	   
		if( $art_type == 7346 ){
		   $theme = 'Instagram_magnet_15x15\FirstCategory\standard';
		   $theme = str_replace( '/', '\\' , $theme ) . '\\theme.xml';
		   $MCProductId .= "&MCThemeUrl={library}Gifting\\Themes\\" . $theme ;
	   
		}
	   
		if( $template ){
		
			if( base64_encode(base64_decode($template, true)) !== $template ){
				$MCProductId .= "&MCThemeUrl=$(package:eurofoto/cards)/themes/" . $template . "&MCMode=full";
			}
			else{
				if( $art_type == 939 ){
					$MCProductId = "&MCProductId=" . $art_type . "000";
				}
				if( $this->module == 'GreetingCard' ){
					
					$theme = base64_decode( $template );
					
					if( strpos( $theme, '/' ) ){
						$theme = str_replace( '/', '\\' , $theme ) . '\theme.xml';
						$MCProductId .= "&MCThemeUrl={library}GreetingCard\\Themes\\" . $theme . "&MCMode=full";
					}else{
						$MCProductId .= "&MCThemeUrl=$(package:eurofoto/cards)/themes/" . $theme . "&MCMode=full";
					}

					//$MCProductId .= "&MCThemeUrl=$(package:eurofoto/cards)/themes/" . $theme . "&MCMode=full";
					Session::delete( "selectedtemplate"  );
				}
				else if( $this->module == 'Gifting' ){
					$theme = base64_decode( $template );
					$theme = str_replace( '/', '\\' , $theme ) . '\\theme.xml';
					$MCProductId .= "&MCThemeUrl={library}Gifting\\Themes\\" . $theme . "&MCMode=full";
					Session::delete( "selectedtemplate"  );
				}
				else if( $this->module == 'Photobook' ){
					$theme = base64_decode( $template );
					$theme = str_replace( '/', '\\' , $theme ) . '\\theme.xml';
					$MCProductId .= "&MCThemeUrl={library}Photobook\\Themes\\" . $theme . "&MCMode=full";
					Session::delete( "selectedtemplate"  );
				}
			}
	
		}
	
		Session::pipe( 'uploadreturnurl', sprintf( '/create/%s/edit/%d/%d', strtolower( $this->module ), $id, $art_type ) );
	
		//$active_session = DB::query( "SELECT * FROM mediaclip_session WHERE sessionid = ?", $logindata['sessionid'] )->fetchSingle();
		
        
        //$detect = new Mobile_Detect;
		
		//if( $detect->isMobile() || $detect->isTablet() || $_GET['html5'] !== null  || 1 == 1){

			$productkobling  = Settings::Get( 'mediaclip', 'produktkobling' );
			
			if( is_array( $productkobling[$arttemplate] )  ){
				$productId = $productkobling[$arttemplate]['template'];
				//$art_type = $productkobling[$arttemplate]['art_type'];
				$articleid = $productkobling[$arttemplate]['art_type'];
			}
			else if( $productkobling[$arttemplate] ){
				$productId = $productkobling[$arttemplate];
				$articleid = $art_type . "000";
			}else{
				if( !$articleid ){
					$articleid = $art_type . "000";
				}
			}

			if( $productId  && $articleid ){
				$MCProductId  = '&productId=' . urlencode( $productId  );
				$art_type = $articleid;
			}
			else if( strlen($project->project_xml) == 0 ){
				//$MCProductId  = '&productId=' . $articleid;
			}
			
			if( strlen( $project->project_xml ) > 4 ){
				$MCProductId  = '';
			}
			

			$efcustomer = array(
				'art_type' => $art_type,
				'Sessionid' => $environment['sessid'],
				'customerid' => $environment['customerid'],
				'idcheck' => $environment['idcheck'],
				'location' => $location,
				'orderid' => $id,
				'unique' => $unique->id,
				'predefinert' => $project->predefined,
				'server' => $server,
			);
			$efinfo = serialize( $efcustomer );
			$sessionid = md5(uniqid());
			
			$logindata =  Login::data();
			$mcsession = new DBmediaclipSession();        
			$mcsession->sessionid = $sessionid;
			$mcsession->userid = (int)$logindata['userid'];
			$mcsession->loggedin = true;
			$mcsession->browser = $_SERVER['HTTP_USER_AGENT'];
			$mcsession->efcustomer = $efinfo;
			$mcsession->save();

			
			$designers = "html5,flash";
			
			$flasharray = array( 7310,2030, 7035,7031, 7032, 7033,7036, 7037, 7034, 980, 981, 982, 983, 984, 985,986,909,908,910  );
			$kalenderstart= "2019&data-autofill-option-startMonth=01";
			
			if( strtolower( $this->module ) == "poster" ){
				$designers = "flash,html5";
			}
			else if( strtolower( $this->module )  == "photobook" ){
				
				if( strpos( $project->project_xml , 'creationVersion="HTML5' ) == null &&  strpos( $project->project_xml , 'productId="$(package:eurofoto') == null  &&  strlen($project->project_xml) > 10 ){
				
					// denne må ligge live:
					$designers = "flash,html5";
					
					//$designers = "html5";
				}
			}
			else if(  strtolower( $this->module ) == 'greetingcard' && ( strpos( $project->project_xml, 'xmlns:gifting="Mediaclip.Gifting.Model' ) ||  strlen( $project->project_xml )  < 4 ) ){
				$this->module = "Gifting";
			}
			
			else if(  strtolower( $this->module ) == 'calendar' && ( strpos( $project->project_xml, 'xmlns:gifting="Mediaclip.Gifting.Model' ) ||  strlen( $project->project_xml )  < 4 ) ){
				
				$this->module = "Gifting";
			}
			else if( in_array( $art_type, $flasharray ) ){
				$designers = "flash,html5";
			}
			
			$this->flashurl = '//' . $mediaclip_server . '/ECommerceBridge/designer/?module=' . $this->module .
									//'&themeUrl=' . $themeUrl .
									'&culture=' . $this->DefineLanguage() .
									'&designers=' . $designers .
									'&art_type=' . $art_type .
									
								//	'&data-autofill-option-startYear=' . $kalenderstart .
									'&sessionId=' . $sessionid.$MCProductId;					
								
		//}
		
		
		/*else{
			
			if( $this->module == 'GreetingCard' && strpos( $project->project_xml, 'xmlns:gifting="Mediaclip.Gifting.Model' ) ){
				$this->module = "Gifting";
			}

			$this->flashurl = '//' . $mediaclip_server . '/ECommerceBridge/designer
									?module=' . $this->module .
									'&culture=' . $this->DefineLanguage() .
									'&designers=flash,html5' .
									'&art_type=' . $art_type .
									'&sessionId=' . $sessionid.$MCProductId;
		}*/
	
	}

	

	
	public function predef($hash){
      
		if( !Dispatcher::getSiteSetting( 'mediaclip.nologinrequired' ) ) {
         
			if( !Login::isLoggedIn() ) {
         
			    Session::pipe( 'loginredirecturl', sprintf( '/create/%s/predef/%s', strtolower( $this->module ), $hash ) );
			    relocate( '/login/' );
			    die();
            
			}
		}
      
		$predef_project = new Project;
      
		$project = $predef_project->predefinedFromHash( $hash );   
      
		$portals = array();
		foreach (DB::query( "SELECT portal FROM mediaclip_predefined_project_portals WHERE predefined_project_row_id=?;", $project->id )->fetchAll() as $row){  
			$portals[] = $row[0];   
		}
      
		$currentportal = Dispatcher::getPortal();
      
		if( empty( $currentportal ) ) {
			$currentportal = "EF-997";
		}
      
		$predef_project = new Project($project->projectid);
		if( in_array( $currentportal, $portals ) ) {
         
        if(($project->status == 1 && Login::isAdmin()) || $project->status == 2 ){
            if( $rowid = $predef_project->duplicatePredef( $this->DefineUserid())) {
               relocate( "/create/photobook/edit/$rowid" );
            }
         }   
      }
      
	}
   
}


?>