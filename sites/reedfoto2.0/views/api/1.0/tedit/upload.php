<?PHP


   import( 'pages.json' );

   class uploadImage extends JSONPage implements NoAuthRequired, IView {
    
        private $folder = '/var/www/repix/data/tedit/uploads/'; 
         
        public function Execute() {
                $sesessionid = session_id();
                $uploadfolder = $this->folder . $sesessionid ;
                
                if( !file_exists( $uploadfolder ) ){
                    mkdir( $uploadfolder );
                }
                
                if($_FILES){
                    $name = $_FILES['imageupload']['name'];
                    $tmp = $_FILES['imageupload']['tmp_name'];
                    $path =  $uploadfolder . "/" . basename($name);
                    if(move_uploaded_file($tmp,$path)){
                            $message = "Done! File saved...";
                    }else{
                            $message = "Error on uploading!";
                    }
                }
                
                       
                
                $this->result = true;
                $this->name =  basename($name);
                $this->message = $message;
                return true;
         }      
   }



?>
