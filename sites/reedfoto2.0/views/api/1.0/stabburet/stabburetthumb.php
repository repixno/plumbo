<?PHP


   import( 'pages.json' );

   class SaveProject extends JSONPage implements NoAuthRequired, IView {
    
        private $folder = '/data/pd/stabburet/lokk/share/'; 
         
         public function Execute() {

                $message = 'OK';
                    
                $data = $_POST['data'];
                $category = $_POST['category'];
                $category = 'Baby';
                $image = $_POST['image'];
                $name = md5( uniqid() );
                
                $themefolder = $this->folder  . $name;

                file_put_contents( $themefolder . '.txt' , $image );
                
                $this->result = true;
                $this->data =  $name;
                $this->message = $message;
                return true;
         }      
   }



?>
