<?php

import( 'core.settings' );
//model( 'user.cewemyphotos');
import( 'cewe.cewemyphotos' );
model( 'user.ceweimage' );


class ceweApi{
    
    
    //private $apiurl = 'https://tas02.photoprintit.com/api/1.1/api';
    /////////private $apiAccessKey = '84d5fff65156920a682f71f502f63966';
	private $apiAccessKey  = '9cf10b805bf35d1dcedc8fa71e32d1b4'; 
    //private $clientVersion = '0.0.0-apidoc';
    

    private $apiurl = 'https://cmp.photoprintit.com/api/1.1/api';
    //private $apiAccessKey = '8ccc7bec8f9899140873db6b01254f35cc3a04ed';
	
    private $clientVersion = '1.1.2-2-b20160420-222552';
	
	private $secretPartnerKey =  '6XLMsAQ6w5';
	
	   
    
    public function __construct() {
        session_start();
        $this->ch = curl_init();
        $this->setHeaders();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //debugging
        $fp = fopen( '/home/toringe/debug/errorlog.txt', 'w');     
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_STDERR, $fp);
        
        
        if( !$this->checklogin() ){
            $logindata = ceweMyPhotos::getByUserid( Login::userid() );
            if( $logindata->id ){
                $output = $this->login( $logindata->ceweuserid, null, $logindata->refreshtoken  );
            } 
        }
    }

    public function __destruct() {
       curl_close($this->ch);
    }
    
    public function credetials(){
        $credentials = array(
                            'apiurl' => $this->apiurl,
                            'accesskey' => $this->apiAccessKey,
                            'clientversion' => $this->clientVersion,
                            'clid' => $this->cldId()
                            );
        
        return $credentials;
    }
    
    public function system(){
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . "/system");
        $output = curl_exec( $this->ch);
        $this->debug();
        return json_decode( $output );
    }
    
	public function getTask( $username, $data ){
		
		$data = array(
			"secretPartnerKey" => $this->secretPartnerKey,
			"login" => $username
		);
		Util::Debug($data);
		return $this->getApi( '/pixi/tasks', $data );
		
	}
	
	public function getPixiPhotos( $taskid ){
		
		$data = array(
			"secretPartnerKey" => $this->secretPartnerKey
		);
		return $this->getApi( '/pixi/tasks/'.$taskid.'/photos', $data );
	}
    
	public function createPixiuser( $username ){
		
		$data = array(
			"secretPartnerKey" => $this->secretPartnerKey,
			//"login" => $user->username,
			"login" => $username,
			"country" => "NO"
		);
		
		return $this->postApi( '/pixi/users', $data );
	}
	
	public function createTask(  $username , $title ){
		
		//$user = new User( Login::userid() );
		//$username = 'tor.inge@reedfoto.no';
		
		$data = array(
			"secretPartnerKey" => $this->secretPartnerKey,
			//"login" => $user->username,
			"login" => $username,
			"title" => $title
		);
		
		return $this->postApi( '/pixi/tasks', $data );
		
	}
	
    private function cldId(){
        $clid = $_SESSION['cldId'] ? $_SESSION['cldId'] : '' ;
        return $clid;
    }
    
    public function thumb($id, $size){
        $url = sprintf( '%s/photos/%s.jpg?size=%s&errorImage=false&cldId=%s&clientVersion=%s', $this->apiurl, $id, $size, $this->cldId(), $this->clientVersion );
        return $url;
    }
    
	public function ceweImageFromid( $imageid ){
		$imagid = $this->decodeBid($imageid);
		$image = $this->getApi( '/photos/' . $imageid  );
		return $this->ceweImageArray( $image );
	}
	
    public function ceweImageArray( $image = null, $aid = null ){
        
        
        $bid = $this->encodeBid( $image->id );
        //$aid = 123454;
        $uid = Login::userid();
        
        return array(
				'id'                => $bid,
				'ceweid'			=> $image->id,
				'date'              => date( 'Y-m-d',  $image->timestamp / 1000 ),
				'title'             => $image->name,
				'description'       => '',
				'identifier'        => '',
				'urlname'           => Util::urlize( $image->name ),
				'thumbnail'         => $this->thumb($image->id, 300 ),
				'screensize'        => $this->thumb($image->id, 720 ),
				'fullsize'          => $this->thumb($image->id, 'orginal' ),
				'aid'               => $aid,
				'quarantined'       => $quarantined_at,
				'urls'              => array(
                    'private'            => '/myaccount/cewealbum/image/' . $image->id . '/' . $aid,
                    'privatealbum'       => sprintf( '/myaccount/cewealbum/%s/%s', $aid, $urlizedalbumtitle ),
                    'gallery'            => sprintf( '/gallery/cewealbum/image/%d', $bid ),
                    'galleryalbum'       => sprintf( '/gallery/cewealbum/%s/%s', $aid, $urlizedalbumtitle ),
                    'shared'             => sprintf( '/shared/cewealbum/image/%d', $bid ),
                    'sharedalbum'        => sprintf( '/shared/cewealbum/%s/%s', $aid, $urlizedalbumtitle ),
                    'sharedstream'       => sprintf( '/shared/cewealbum/image/stream/%d/%s', $bid, 'share' ),
				),
				'exif'              => array(
                        'date'   => $image->exif_date,
                        'width'  => $image->x,
                        'height' => $image->y,
                        'xres'   => $image->exif_x_res,
                        'yres'   => $image->exif_y_res,
                        'make'   => $image->exifMake,
                        'model'  => $image->exifImageDescription,
                        'orientation' => $image->exif_orientation,
                        'exposuretime' => $image->exif_exposure_time,
                        'gps' => array(
                                    'altitude' => $image->exif_gps_altitude,
                                    'latitude' => $image->exif_gps_latitude,
                                    'longitude' => $image->exif_gps_longitude,
                                 )
                        ),
				'imagedate'        => date( 'Y-m-d', $image->timestamp / 1000 ),
				'x'                 => $image->width,
				'y'                 => $image->height,
				'permission'        => 1,
            'owner'             => array(
               'uid'               => $uid,
			    'name'              => User::getNameFromUid( $uid ),
			    'yours'             => $uid == login::userid(),
                
            ),
            'license'           => array(
               'type'              => 0,
               'fee'               => 0,
            ),
		);
    }
    
    public function ceweAlbumArray( $cewealbum = null ){
		 
         $uid = Login::userid();
         
		 $result = array(
				  'id' => $cewealbum->id,
				  'title' => $cewealbum->name,
				  'urlname' => $cewealbum->name,
				  'identifier' => '',
				  'ownerid' => $uid,
				  'ownerid' => $uid,
				  'numviewed' => 0,
				  'permission' => 1,
				  'publickey' => null,
				  'password' => '',
				  'created' => date('Y-d-m H:i:s'),
				  'owner' => array(
					 'uid' => $uid,
					 'name' => User::getNameFromUid( $uid ),
					 'yours' => 1,
					 'preferences' => array(
			         'purchase' => 1,
					 'download' => 1,
					 'year'     => 2016,
			      ),
            ),
            'access' => array(
               'purchase' => 1,
               'download' => 1,
            ),
			);
         
         
         $result['descriptionraw'] = '';
        $result['description'] = '';
        $result['albumurl'] = '/myaccount/cewealbum/'. $cewealbum->id . '/' . $cewealbum->name ;
        $result['shared'] = array(
           'link' => false,
           'password' => false,
           'public' => false,
           'groups' => false,
           'friends' => false,
           'friendsorgroups' => false,
        );
        $result['galleryurl'] = '';
        $result['sharingurl'] = '';
        $result['isshared'] = false;
         
         
		 $result['thumbnail'] =  $this->thumb($cewealbum->coverPhotoId, 300 );
		 $result['defaultimageid'] = $this->encodeBid( $cewealbum->coverPhotoId );
		 $result['thumbnailurl'] = $this->thumb($cewealbum->coverPhotoId, 300 );
		 $result['numimages'] = $cewealbum->photoCount;
		 
		 return $result;
		 
	}
    
    
    public function checklogin(){
        
        $login = $this->getApi( '/account'  );
        
        $usercheck = ceweMyPhotos::getByUserid( Login::userid() );
        
        if( $login->user->login  !==  $usercheck->ceweuserid  ){
            //$this->deleteApi( '/account/session' );
            return false;
        }
        
        //setcookie("cldId", $login->session->cldId, time() + 3600);
        
        if( $login->user->login ){
            return true;
        }else{
            return false;
        } 
    }
    
    
    public function Login( $username, $password = null , $refreshToken = null  ){
        //$clid = $_SESSION['cldId'];
        
        $userid = Login::userid();
        
        $logindata = ceweMyPhotos::getByUserid( $userid );
        
        if( $logindata->id ){
            $mycewe = new ceweMyPhotos( $logindata->id );    
        }else{
            $mycewe = new ceweMyPhotos();
            $mycewe->userid = $userid;
        }
        
        if( $password ){
            $data = array(
                "login" =>  $username,
                "password" => $password,
                "createRefreshToken" => "true",
                "lifetime" =>  "31536000000",
                "deviceName" => "eurofoto.no",
              );
            
            $output = $this->postApi('/account/session', $data );
            $mycewe->ceweuserid = $output->user->login;
            $mycewe->clid = $output->session->cldId;
            $mycewe->refreshtoken = $output->refreshToken->tokenId;  
            $mycewe->save();
            
        }else{
            $data = array(
                'login' => $username,
                'refreshToken' => $refreshToken 
            );
            $output = $this->postApi('/account/session', $data );
        }
        
        //file_put_contents( sprintf( '/home/toringe/debug/%s.txt', Login::userid() ), serialize( $output )  );
        $_SESSION['cldId']  =  $output->session->cldId;
        $this->setHeaders();
        return $output;
    }
	
	public function Logout(){
        
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . '/account/session?invalidateRefreshToken=true' );
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $output = curl_exec( $this->ch);
        //$this->debugging();
        $output = json_decode($output);
        
        return $output;    
    }
    
    public function postApi( $api, $data ){
		
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . $api );
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS,  json_encode( $data )  );
        $output = curl_exec( $this->ch);
        //$this->debugging();
        $output = json_decode($output);
        return $output;    
    }
    
    public function getApi( $api, $data  = array()){
        
        $getdata = "";
        foreach( $data as $key=>$ret ){
            $getdata .= sprintf("%s=%s&", $key, $ret );
        }
		
        $url = $this->apiurl . $api  . '?' . $getdata;
		
		//Util::Debug( $url );
		
        curl_setopt($this->ch, CURLOPT_POST, false);
        curl_setopt($this->ch, CURLOPT_URL, $url );
        $output = curl_exec( $this->ch);
        //$this->debugging();
        $output = json_decode($output);
        return $output;
    }
    
    public function deleteApi( $api, $data = array() ){
        
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . $api );
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS,  json_encode( $data )  );
        $output = curl_exec( $this->ch);
        //$this->debugging();
        $output = json_decode($output);
        
        return $output;    
    }
    
    public function newUser( $data ){

        $output = $this->postApi('/account/user', $data );
        //$this->debugging();
        //$output = json_decode($output);
        return $output;
    }
    
    private function debugging(){
        if($errno = curl_errno($this->ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
    }
    
    private function setHeaders(){
        
        $clid = $_SESSION['cldId'] ? $_SESSION['cldId'] : '' ;        
        $headers = array();
        $headers[] = 'clientVersion: 0.0.0-apidoc';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        $headers[] = 'apiAccessKey: ' . $this->apiAccessKey ;
        $headers[] = 'cldId:' . $clid;
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
    }
      
	
	public function encodeBid( $str ){
		
		$bid = DB::query( "SELECT bid FROM cewe_image where ceweid = ?", $str )->fetchSingle();
		
		if( !$bid ){
			$newid = new DBceweImage();
			$newid->ceweid = $str;
			$newid->save();
			
			return $newid->bid;
		}
		else{
			return $bid;
		}
	}
	
	public function decodeBid( $number ){
		
		$cewid = DB::query( "SELECT ceweid FROM cewe_image where bid = ?", $number )->fetchSingle();		
		return $cewid;

	}

}




class pixiUpload{
    //private $apiurl = 'https://tas02.photoprintit.com/api/1.1/api';
    //private $apiAccessKey = '84d5fff65156920a682f71f502f63966';
    //private $clientVersion = '0.0.0-apidoc';
    

    private $apiurl = 'https://cmp.photoprintit.com/api/1.1/api';
    //private $apiAccessKey = '8ccc7bec8f9899140873db6b01254f35cc3a04ed';
    private $clientVersion = '1.1.2-2-b20160420-222552';
	
	private $secretPartnerKey =  '6XLMsAQ6w5';
	
	private $apiAccessKey  = '9cf10b805bf35d1dcedc8fa71e32d1b4';
    
    public function __construct( $clid = null  ) {
        $this->ch = curl_init();
        $this->setHeaders( $clid );
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //debugging
        $fp = fopen( '/home/toringe/debug/errorpixiupload.txt', 'w');     
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_STDERR, $fp);
    }
    
    
    public function UploadApi( $file, $title, $albumtitle, $taskid, $login  ){
        
        $file = "/data/bildearkiv/" . $file;
        
        $cfile = curl_file_create( $file, 'image/jpeg', $title );
        
        $data = array(
                'file' => $cfile,
                'filename' => $title,
                'secretPartnerKey' => $this->secretPartnerKey,
				'albumTitle' => $albumtitle
            );
        
        Util::Debug( $this->apiurl . '/pixi/tasks/'. $taskid . '/photos' );
        
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . '/pixi/tasks/'. $taskid . '/photos' );
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $data  );
        $output = curl_exec( $this->ch);
        $output = json_decode($output);
        return $output;    
    }
    
    
    private function setHeaders( $clid = null ){
        
        $clid = $clid ? $clid : $_SESSION['cldId'] ;

		
		Util::Debug($clid);
		
		
        $headers = array();
        $headers[] = 'clientVersion: ' . $this->clientVersion;
        $headers[] = 'Content-Type: multipart/form-data';
        $headers[] = 'Accept: application/json';
        $headers[] = 'apiAccessKey: ' . $this->apiAccessKey ;
        $headers[] = 'cldId:' . $clid;
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
    }
}

class ceweUpload{
    
    //private $apiurl = 'https://tas02.photoprintit.com/api/1.1/api';
    //private $apiAccessKey = '84d5fff65156920a682f71f502f63966';
    //private $clientVersion = '0.0.0-apidoc';
    
    
    private $apiurl = 'https://cmp.photoprintit.com/api/1.1/api';
    private $apiAccessKey = '8ccc7bec8f9899140873db6b01254f35cc3a04ed';
    private $clientVersion = '1.1.2-2-b20160420-222552';
    
    public function __construct( $clid = null  ) {
        $this->ch = curl_init();
        $this->setHeaders( $clid );
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //debugging
        $fp = fopen( '/home/toringe/debug/errorlogupload.txt', 'w');     
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_STDERR, $fp);
    }
    
    
    public function UploadApi( $file, $title, $albumid  ){
        
        $file = "/data/bildearkiv/" . $file;
        
        $cfile = curl_file_create( $file, 'image/jpeg', $title );
        
        $data = array(
                'file' => $cfile,
                'filename' => $title,
                'photoAlbumId' => $albumid
            );
        
        Util::Debug($this->apiurl . '/photos');
        
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . '/photos' );
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $data  );
        $output = curl_exec( $this->ch);
        $output = json_decode($output);
        return $output;    
    }
    
    public function UploadApi2( $file, $title, $albumid  ){
        
        $file = "/data/bildearkiv/" . $file;
        
        $cfile = curl_file_create( $file, 'image/jpeg', $title );
        
        $data = array(
                'file' => $cfile,
                'filename' => $title,
                'photoAlbumId' => $albumid
            );
        
        Util::Debug($this->apiurl . '/photos');
        
        curl_setopt($this->ch, CURLOPT_URL, $this->apiurl . '/photos' );
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $data  );
        $output = curl_exec( $this->ch);
        $output = json_decode($output);
        return $output;    
    }
    
    
    private function setHeaders( $clid = null ){
        
        $clid = $clid ? $clid : $_SESSION['cldId'] ;

		
		Util::Debug($clid);
		
		
        $headers = array();
        $headers[] = 'clientVersion: ' . $this->clientVersion;
        $headers[] = 'Content-Type: multipart/form-data';
        $headers[] = 'Accept: application/json';
        $headers[] = 'apiAccessKey: ' . $this->apiAccessKey ;
        $headers[] = 'cldId:' . $clid;
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
    }
    
}



?>