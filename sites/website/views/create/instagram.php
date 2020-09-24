<?php
/**
 * Instagram PHP API example usage.
 * This is the entry point of your application, it will detect whether
 * the user is already authenticated and will present her the login
 * window in case she is not.
 * 
 * If the authentication token is already stored (as a cookie in this case),
 * the user will be redirected to callback.php which is basically the same
 * URI callback that you must set up with Instagram as the return address
 * for your application on their developers section:
 * http://instagr.am/developer/
 * 
 * 
 * If you have any question, check http://mauriciocuenca.com/ for the
 * latest updates
 */
//require_once 'Instagram.php';
library( 'instagram.Instagram' );
/**
 * Configuration params, make sure to write exactly the ones
 * instagram provide you at http://instagr.am/developer/
 */

class MediaclipInstagram extends WebPage implements IView{
    
    protected $templage = false;
    
    private $instagramfolder = "/data/pd/ef28/instagram/";
    
    private $config = array(
            'client_id' => '3db3101cd2be47d386e69f2daae34956',
            'client_secret' => 'c3943a22b59e48afa3ed31cc363c12f4',
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'http://www.eurofoto.no/create/instagram/callback',
        );
    public function Execute( ){
        relocate( 'http://instagram.com' );
    }
    
    public function Images( $customerid = null  ){
        
        //file_put_contents( '/data/pd/ef28/instagram/test.txt', $customerid );
        
        $this->template = null;
        session_start();
        $instagram = new Instagram( $this->config );
        
        $customeridarray = explode( '-' ,  $customerid );
	$customerinfo   = explode( '-' , base64_decode( $customeridarray[0] ));
	$customerid = $customerinfo[0] .'-'. $customeridarray[1];
               
	if( file_exists( $this->instagramfolder . 'id/' . $customerid   ) ){
	    $accessToken = file_get_contents( $this->instagramfolder . 'id/'  . $customerid );
	    $_SESSION['InstagramAccessToken'] = $accessToken;
            $accessToken = explode ( '.' , $accessToken );
            $instagram->setAccessToken( $_SESSION['InstagramAccessToken'] );
	    // Get User ID
	}

        $popular = $instagram->getUserRecent( $accessToken[0] );
        $images = json_decode($popular, true);
        header( 'content-type: text/xml' );
	
        //Util::Debug($images);
        //die();
        
        $xml = new SimpleXMLElement("<images></images>");
        
        $i = 0 ;
        while( $images ){
            $i++;
            
            if( $i < 50 ){
                foreach ( $images['data'] as $image ){
                    $imagenode = $xml->addChild('image');
                    $imagenode->id = $image['id'];
                    $imagenode->uid = $image['user']['id'];
                    $imagenode->label = $image['id'];
                    $imagenode->height = $image['images']['standard_resolution']['height'];
                    $imagenode->width = $image['images']['standard_resolution']['width'];
                    $imagenode->date = date('Y-m-d H:i:s', strtotime( $image['created_time'] ) );
                    $imagenode->picture = $image['images']['standard_resolution']['url'];
                    $imagenode->source = $image['images']['standard_resolution']['url'];
                }
                
                if( $images['pagination']['next_url'] ){
                    $popular = $instagram->getUserRecent( $accessToken[0], $images['pagination']['next_max_id']  );
                    $images = json_decode($popular, true);
                }
                else{
                    $images = null;
                }
            }else{
                //mail( 'tor.inge@eurofoto.no', "instagram bug", "user" . $customerinfo[0] . " <<<>>>" . $i  );
                $image = null;
            }
        }

	echo $xml->asXML();
        
        //relocate( 'http://instagram.com' );
    }
    
    
    public Function LoginInstagram( $customerid = null ){
        $this->template = null;
        /**
        * This is how a wrong response looks like
        * array(1) { ["InstagramOAuthToken"]=> string(89) "{"code": 400, "error_type": "OAuthException", "error_message": "No matching code found."}" }
        */
        
        if( !$customerid && $_SESSION['InstagramAccessId'] ){
            $customerid = $_SESSION['InstagramAccessId'];
        }
        
        $customerinfo   = explode( '-' , base64_decode( $customerid));
	$sessid = Session::id();
        session_start();
        
        if ( isset($_SESSION['InstagramAccessToken'])  ) {
            $customerid = $customerinfo[0] .'-'. $sessid;
            $instagram = new Instagram($this->config);
            $instagram->setAccessToken( $_SESSION['InstagramAccessToken'] );
            $user = explode( '.' , $_SESSION['InstagramAccessToken']  );
            if ( $user ){
		if( !file_exists( $this->instagramfolder . 'id/' . $customerid) ){
                    file_put_contents( $this->instagramfolder . 'id/' . $customerid  , $_SESSION['InstagramAccessToken']  );
                }
                relocate( Session::pipe( 'uploadreturnurl', null, false, true ) );
            }
            //header('Location: /create/instagram/callback');
            //die();
        }else{
            
            Util::Debug( "FAIL" );
            $_SESSION['InstagramAccessId'] = $customerid;
            // Instantiate the API handler object
            $instagram = new Instagram($this->config);
            $instagram->openAuthorizationUrl();
        }
    }
    
    
    public Function CheckLogin( $customerid ){
        $this->template = null;
        session_start();
        $instagram = new Instagram($this->config);
        
        $customeridarray = explode( '-' ,  $customerid );
	$customerinfo   = explode( '-' , base64_decode( $customeridarray[0] ));
	$customerid = $customerinfo[0] .'-'. $customeridarray[1];
        
        $xml = new SimpleXMLElement('<instagram></instagram>');
	       
	if( file_exists( $this->instagramfolder . 'id/' . $customerid   ) ){
	    $accessToken = file_get_contents( $this->instagramfolder . 'id/'  . $customerid );
	    $_SESSION['InstagramAccessToken'] = $accessToken;
            $accessToken = explode ( ',' , $accessToken );
            $instagram->setAccessToken( $_SESSION['InstagramAccessToken'] );
	    // Get User ID
	}
        if( $accessToken ){
            $user = $instagram->getAccessToken();
        }
        else{
            $user = null;
        }
        
	if ( $user ){
	    $login = $xml->addChild('login');
	    $login->id = "OK";
	    
	}else{
	    $login = $xml->addChild('login');
	    $login->id = "FAIL"; 
	}
	echo $xml->asXML();
    }
    
    public Function Callback(){
        session_start();
        // Instantiate the API handler object
        $instagram = new Instagram($this->config);
        if( empty( $_SESSION['InstagramAccessToken'] ) ){
            $accessToken = $instagram->getAccessToken();
            $_SESSION['InstagramAccessToken'] = $accessToken;
            $user = $instagram->getCurrentUser();
            $_SESSION['InstagramUser'] = $user;
        }
        else{
            //$_SESSION['InstagramAccessToken'] = null;
        }
        //Util::Debug( $accessToken );
        $instagram->setAccessToken( $_SESSION['InstagramAccessToken'] );
        
        relocate( "/create/instagram/LoginInstagram" );
        Util::Debug( $_SESSION['InstagramAccessToken'] );
        die();
        
        //Util::Debug( $_SESSION['InstagramUser']->id  );
        $popular = $instagram->getUserRecent( $_SESSION['InstagramUser']->id );
        
        // After getting the response, let's iterate the payload
        $response = json_decode($popular, true);
        
        //Util::Debug( $response );
        
        //Util::Debug( $response['data'] );
        
       
       Util::Debug( $response['data'] );
       
        while( $response ){
            $this->Stream( $response['data'] );
            
            if( $response['pagination']['next_url'] ){
                $popular = $instagram->getUserRecent( $_SESSION['InstagramUser']->id, $response['pagination']['next_max_id']  );
                $response = json_decode($popular, true);
            }
            else{
                $response = null;
            }
            
        }
        
        
        
         
        Util::Debug( $i );
        die();
        
    }
    
    
    private function Stream( $resonse_data ){
         try{
            Util::Debug( "ALBUM " );
            foreach ( $resonse_data as $data) {
                $link = $data['link'];
                $id = $data['id'];
                $caption = $data['caption']['text'];
                $author = $data['caption']['from']['username'];
                $thumbnail = $data['images']['thumbnail']['url'];
                $fullsize = $data['images']['standard_resolution']['url'];
                echo  "<a href=\"$fullsize\" target=\"_blank\"><img src=\"$thumbnail\" style=\"margin: 5px\"/></a>" ;   
                $i++;
            }
        }catch( Exception $e ){
            Util::Debug( $e );
        }
    }

 }