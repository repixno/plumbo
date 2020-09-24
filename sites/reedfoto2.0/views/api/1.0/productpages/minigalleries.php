<?php


    import( 'pages.json' );
    
    class APIPriceGet extends JSONPage implements NoAuthRequired,IView {
       
       
        public function Execute($albumid = null ) {
         
        $albumid = $_POST['albumid'] ? $_POST['albumid'] : $albumid ;
        
        $this->result = true;
        $this->message = 'OK';
        $album = new Album($albumid);
        //$this->album = $album->asArray();
        $images = $album->getImages();
        
        $imagedata = array();
        
        foreach( $images  as $image ){
            
            
            $imagedata[] = array(
                                 'img' => $image['fullsize'],
                                 'thumb' => $image['thumbnail']
                                );
            
        }
        
        $this->imagedata = $imagedata;
        
         
        }
 
       
    }

?>