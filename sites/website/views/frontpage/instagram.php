<?PHP
   
   class Instagram extends WebPage implements IView {
      
      protected $template = 'frontpage.instagram';
      
      public function Execute() {
        
        
        $client_id = '3db3101cd2be47d386e69f2daae34956';
        $user_id = '1771868092';
        
        $tag = 'eurofotoas';
        
        //$tagurl = 'https://api.instagram.com/v1/tags/' . $tag . '/media/recent?client_id=' . $client_id;
        
        $url = 'https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?count=10&client_id='.$client_id;
        
        $data = file_get_contents($url);
        $json = json_decode($data);
        
        
        $images = array();
        
        foreach( $json->data as $image ){
            
            //Util::Debug( $caption );
            $images[] = array( 'thumb' => $image->images->low_resolution,
                                'link' => $image->link,
                                'likes' => $image->likes->count,
                                'caption' => str_replace( '#', ' #' , substr( $image->caption->text, 0 , 75 ) )
                              
                             );
            
        }
        
        
        $this->images = $images;
        
        //Util::Debug( $images );
        //exit;  
      }
      
   }
   
?>