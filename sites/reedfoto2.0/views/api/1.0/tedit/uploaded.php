<?PHP


   import( 'pages.json' );

   class uploadedImage extends JSONPage implements NoAuthRequired, IView {
    
        private $folder = '/var/www/repix/data/tedit/uploads/'; 
         
        public function Execute() {
                $sesessionid = session_id();
                $uploadfolder = $this->folder . $sesessionid ;
                
                $message = "OK";
                
                if( file_exists( $uploadfolder ) ){
                    
                    foreach( glob( $uploadfolder . '/*.jpg') as $res){
                         $images[] = basename( $res );
                    }
                }else{
                    
                    $message = "No images";
                    
                }
                
                
                       
                
                $this->result = true;
                $this->images =  $images;
                $this->message = $message;
                return true;
         }      
   }



?>
