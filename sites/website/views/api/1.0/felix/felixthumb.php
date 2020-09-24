<?PHP


   import( 'pages.json' );

   class SaveFelixThumb extends JSONPage implements NoAuthRequired, IValidatedView {
    
        private $folder = '/data/pd/felix/share/'; 
         
         
         public function Validate() {
         
            return array(  
               "execute" => array(
                  "post" => array(
                     "image" => VALIDATE_STRING,
                  )
               )
            );
            
         }
         
         public function Execute() {

                $message = 'OK';
                    
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
