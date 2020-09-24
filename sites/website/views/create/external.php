<?php

library( 'facebook.facebook_new' );
 
class MediaclipProject extends WebPage implements IView{
   

	protected $template = false;
	
	//private $appID = 455931714459759;
	private $applicationID = 267260676728293;
	//private $secret = "3614e4cdb4a21df0566afca6c480ae2a";
	private $secret = "5dedeacb0d8a055a60f200b3338af60b";
	private $facebookidfolder = "/data/pd/ef28/facebookid/";

	
	public function image( $image, $id, $uid  ){	   
	  
	   $uid = explode( '-', base64_decode($uid) );

	   $imagefolder = '/data/pd/ef28/facebookimages/' . $uid[0] ;
	   $imagefile = $imagefolder . '/facebook-' . $id  . '.jpg';
	   
	   if( !file_exists( $imagefolder ) ){
	      mkdir( $imagefolder );
	   }
	   
	   if( file_exists( $imagefile ) ){
	      $stream = file_get_contents( $imagefile );
	   }
	   else{
	       $image = base64_decode(  $image );
	       $stream = file_get_contents($image);
	       file_put_contents(  $imagefile , $stream );
	       
	   }
	   
      header("Content-Type: image/jpeg");
	   echo $stream; 

	}
	
	public function Execute( $customerid = null, $friendid = 'me' , $albumid = null ){
	   
		$facebook = new Facebook_new( array( 
			 'appId' => $this->applicationID,
			 'secret' => $this->secret
			 )
		);
	   
		$customeridarray = explode( '-' ,  $customerid );
		$customerinfo   = explode( '-' , base64_decode( $customeridarray[0] ));
		$customerid = $customerinfo[0] .'-'. $customeridarray[1];
      
		if( file_exists( $this->facebookidfolder . $customerid ) ){
	        $facebooktoken = file_get_contents( $this->facebookidfolder . $customerid);
	      	$facebook->setAccessToken( $facebooktoken );
            // Get User ID
		}
      
		$user = $facebook->getUser();
      
		if ($user) {
			try {
               if( $friendid && $albumid ){
                    $images =  $facebook->api("/$albumid/photos");
                    header( 'content-type: text/xml' );
					
					$xml = new SimpleXMLElement("<albums></albums>");
					$albumnode = $xml->addChild('album');
					$imagesnode = $albumnode->addChild('images');
					
                  	foreach ( $images['data'] as $image ){
						$imagenode = $imagesnode->addChild('image');
                  		$imagenode->id = $image['id'];
                  		$imagenode->uid = $image['from']['id'];
                  		$imagenode->label = $image['id'];
                  		$imagenode->height = $image['height'];
                  		$imagenode->width = $image['width'];
                  		$imagenode->date = date('Y-m-d H:i:s', strtotime( $image['created_time'] ) );
                  		$imagenode->picture = $image['picture'];
                  		$imagenode->source = $image['source'];
                  	}
                  	
                     echo $xml->asXML();
                     
				}
				else if ( $friendid ) {
					header( 'content-type: text/xml' );
					$xml = new SimpleXMLElement('<albums></albums>');
                  
					$albums = $facebook->api('/' . $friendid .'/albums');
					foreach ( $albums['data'] as $album ){
                     
                     $id = $album['id'];
                     
                     $albumCover = $facebook->api("/$id?fields=picture", "get");
                     
                     $albumnode = $xml->addChild('album');
                     $albumnode->id = $album['id'];
                     $albumnode->default_bid =  $albumCover['picture']['data']['url'];
                     $albumnode->uid = $friendid;
                     $albumnode->label = $album['name'];

                     /*
                     $id = $album['id'];
                     
                     $albumCover = $facebook->api("/$id?fields=picture", "get");                     
                     echo sprintf("<a href='%s/%s'><img src='%s'/></a><br/>" , $friendid, $id , $albumCover['picture']['data']['url'] );*/
         
                  }
                  echo $xml->asXML();
                  
               }else{
         
                  $friends = $facebook->api( '/me/friends' );
                  
                  header( 'content-type: text/xml' );
		
		            $xml = new SimpleXMLElement('<albums></albums>');

		            $albumnode = $xml->addChild('album');
		            $albumnode->id = $user;
		            $albumnode->label = "Mine Album";
		            
		            /*
                  foreach ( $friends['data'] as $friend ){
                     $albumnode = $xml->addChild('album');
                     $albumnode->id = $friend['id'];
                     //$albumnode->default_bid =  '';
                     //$albumnode->uid = $id;
                     $albumnode->label = $friend['name'];
                     
                     //echo sprintf( '<a href="/create/external/%s"><img src="http://graph.facebook.com/%s/picture?type=large" /></a><br/>', $id, $id );
                     
                     //$user = $facebook->getUser($id);
                     
                     //$ProfilePicture = $facebook->api("/$id?fields=picture", "get");  
                     
                     //util::Debug( $userCover );
                     //echo sprintf( "<a href='%s'><img src='%s'/></a><br/>" , $friend['id'] , $ProfilePicture['picture']['data']['url']  );
                     
                  }*/
         
                  echo $xml->asXML();
               };
               
			} catch (FacebookApiException $e) {
				error_log($e);
				$user = null;
			}
		}
		if ( $user ){
			$logoutUrl = $facebook->getLogoutUrl(  );
			//echo "<a href='$logoutUrl'>Logout</a>";
		}else{
			$loginUrl = $facebook->getLoginUrl( array( 
			    'scope' => 'user_photos,friends_photos'
			));
	      
			header ("Location: $loginUrl"); 
		}
	}
	
	public function CheckLogin( $customerid = null ){

	   $facebook = new Facebook_new( array( 
	        'appId' => $this->applicationID,
	        'secret' => $this->secret
	        )
	   );
	   
	   
	   $customeridarray = explode( '-' ,  $customerid );
	   $customerinfo   = explode( '-' , base64_decode( $customeridarray[0] ));
	   $customerid = $customerinfo[0] .'-'. $customeridarray[1];

      //header( 'content-type: text/xml' );
      $xml = new SimpleXMLElement('<facebook></facebook>');
	   
	   if( file_exists( $this->facebookidfolder . $customerid   ) ){
	         $facebooktoken = file_get_contents( $this->facebookidfolder . $customerid );

	      	$facebook->setAccessToken( $facebooktoken );
            // Get User ID
            
	   }

	   $user = $facebook->getUser();
	   
	   if ( $user ){
	      $login = $xml->addChild('login');
	      $login->id = "OK";
	      //unlink(   '/data/pd/ef28/facebookid/' .$customerinfo[0] );
	      /*if( !file_exists( $this->facebookidfolder . $customerid . '-' . Session::id() ) ){
	        file_put_contents( $this->facebookidfolder . $customerid  . '-' . Session::id(), $facebook->getAccessToken() );
	      }*/

	   }else{
	      $login = $xml->addChild('login');
	      $login->id = "FAIL";
	      
	   }
	   
	   echo $xml->asXML();
	}
	
	public function LoginFacebook( $customerid = null ){
	   
	   
	   $facebook = new Facebook_new( array( 
	        'appId' => $this->applicationID,
	        'secret' => $this->secret
	        )
	   );
	   
	   //$customeridarray = explode( '-' ,  $customerid );
	   $customerinfo   = explode( '-' , base64_decode( $customerid));
	   
	   $sessid = Session::id();
	   
	   if(  !empty( $customerinfo[0] ) && !empty( $sessid ) ){

	      $customerid = $customerinfo[0] .'-'. $sessid;
	      
   	   if( file_exists( $this->facebookidfolder . $customerid  ) ){
   	         $facebooktoken = file_get_contents( $this->facebookidfolder .$customerid );
   	      	$facebook->setAccessToken( $facebooktoken );
               // Get User ID 
   	   }
   	   
   	  $user = $facebook->getUser();
   	  
   	  if ( $user ){
   	      if( !file_exists( $this->facebookidfolder . $customerid) ){
   	        file_put_contents( $this->facebookidfolder . $customerid  , $facebook->getAccessToken() );
   	      }
   	      relocate( Session::pipe( 'uploadreturnurl', null, false, true ) );
   	   }else{
   	      
   	      $loginUrl = $facebook->getLoginUrl( array(
	              'scope' => 'user_photos'
	           ));
   	      
	           $stringstop = strpos( $loginUrl , '&redirect' ); 

	           /*if( !strpos( $loginUrl , '267260676728293' )){
   	           $loginUrl = str_replace( '267260676728', $this->applicationID , $loginUrl );
	           }*/
   	     
	           $loginUrl = substr_replace( $loginUrl, 'https://www.facebook.com/dialog/oauth?client_id=' . $this->applicationID, 0 ,  $stringstop );
	           
   	      
   	      //echo("Location: http://www.facebook.com/dialog/oauth/?client_id=267260676728293&redirect_uri=" . urlencode( 'http://nelly.eurofoto.no/create/external/loginfacebook/') . "&state=" .$_GET['code'] );
   	      
   	      header ("Location: $loginUrl"); 
   	      
   	   }
	   
	   }
	   else{
	      
	      util::Debug( "bug" );
	   }

	}
	
	public function Test( $customerid = null ){
      die();
	   $facebook = new Facebook_new( array( 
	        'appId' => $this->applicationID,
	        'secret' => $this->secret
	        )
	   );
	  
	   
	   $user = $facebook->getUser();
	   

	   	$loginUrl = $facebook->getLoginUrl( array(
	        'scope' => 'user_photos'
	      ));
	   
	   if ($user) {
          try {
              $user_profile = $facebook->api('/me');
          } catch (FacebookApiException $e) {
              util::Debug( $e );
              $user = null;
          }
      }
      
      util::Debug($loginUrl);
      
      
	   die();
	   
	   if( Login::isLoggedIn() ){
	   $customerinfo   = explode( '-' , base64_decode( $customerid ));
	   
	   /*if( file_exists( '/data/pd/ef28/facebookid/' .$customerinfo[0]  ) && $_GET['logout'] != "yes"){
	   
	         $facebooktoken = file_get_contents( '/data/pd/ef28/facebookid/' .$customerinfo[0] );

	      	$facebook->setAccessToken( $facebooktoken );
            // Get User ID
            
	   }*/
	   

	   $user = $facebook->getUser();
	   
	   util::Debug( $_SESSION );
	   util::Debug( $facebook );

	   
	   
	   if ($user) {
          try {
              $user_profile = $facebook->api('/me');
          } catch (FacebookApiException $e) {
              util::Debug( $e );
              $user = null;
          }
      }
	   
	   if ( $user ){
	      
	      if ($_GET['logout'] == "yes") {
	         
              unset( $_SESSION['fb_' . $facebook->getAppId() .'_code'] );
	           unset( $_SESSION['fb_' . $facebook->getAppId() .'_access_token'] );
   	        unset( $_SESSION['fb_' . $facebook->getAppId() .'_user_id'] );
            //session_destroy();
            header("Location: /create/external/test");
            //unlink(   $this->facebookidfolder .$customerinfo[0] );
         }
	      
	     /*
	      $logoutUrl = $facebook->getLogoutUrl(array(
             'next'=>'http://nelly.eurofoto.no/create/external/test/?logout=yes'
         ));*/
	      
	      echo "<a href='http://nelly.eurofoto.no/create/external/test/?logout=yes'>LOGOUT</a>";
	      
	      
	      if( !file_exists( $this->facebookidfolder . $customerinfo[0] ) ){
	        file_put_contents( $this->facebookidfolder .$customerinfo[0]  , $facebook->getAccessToken() );
	      }
	      die();
	      
	      
	      relocate( Session::pipe( 'uploadreturnurl', null, false, true ) );

	   }else{
	      
	      $loginUrl = $facebook->getLoginUrl( array(
	        'scope' => 'user_photos'
	      ));
	      
	      
	     echo "<a href='$loginUrl'>LOGIN</a>";
	     die();

	      
	      //$url = urlencode($loginUrl );
	      
	      //header ("Location: $loginUrl"); 
	      //relocate( urlencode($loginUrl ) );
	      
	   }
	   
	   
	   
	   
	}

	}

}


?>