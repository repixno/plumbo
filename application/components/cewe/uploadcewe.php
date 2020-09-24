<?php

class ceweUpload2{
    
    //private $apiurl = 'https://tas02.photoprintit.com/api/1.1/api';
    //private $apiAccessKey = '84d5fff65156920a682f71f502f63966';
    //private $clientVersion = '0.0.0-apidoc';
    
    
    private $apiurl = 'https://cmp.photoprintit.com/api/1.1/api';
    private $apiAccessKey = '8ccc7bec8f9899140873db6b01254f35cc3a04ed';
    private $clientVersion = '1.1.2-2-b20160420-222552';
	
	public function UploadApiMulti( $filearray  ){
        
        
		$curl_arr = array();
		$master = curl_multi_init();
        
        $i=0;
        
		
		
		foreach( $filearray as $filedata ){
		
			$file = "/data/bildearkiv/" . $filedata['file'];
			 
			$cfile = curl_file_create( $file, 'image/jpeg', $filedata['title'] );
		
			$data = array(
					'file' => $cfile,
					'filename' =>  $filedata['title'],
					'photoAlbumId' =>  $filedata['albumid'],
                    'fields' => $filedata['hashcode']
				);
			
			//Util::Debug($this->apiurl . '/photos');
			$this->ch = curl_init();
            $this->setHeaders();
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            
			$curl_arr[$i] = $this->ch;
			
			curl_setopt($curl_arr[$i], CURLOPT_URL, $this->apiurl . '/photos' );
            curl_setopt($curl_arr[$i], CURLOPT_TIMEOUT, 90);
			curl_setopt($curl_arr[$i], CURLOPT_POST, true);
			curl_setopt($curl_arr[$i], CURLOPT_POSTFIELDS, $data  );
			
			curl_multi_add_handle($master, $curl_arr[$i]);
            
			$i++;
		}
		$running = null;
		do {
			$output = curl_multi_exec($master,$running);
			$outputarray[] = json_decode($output);
		} while($running > 0);
		
        
        
		
		foreach(  $curl_arr as $curlret ){
			$results[] = curl_multi_getcontent  ( $curlret );
		}
		
		Util::Debug($results);
		
        //$output = curl_exec( $this->ch);
        //$output = json_decode($output);
        return $outputarray;    
    }
    
    
    

    
    
    
    private function setHeaders(){
        
        $clid = $_SESSION['cldId'] ? $_SESSION['cldId'] : '' ;

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