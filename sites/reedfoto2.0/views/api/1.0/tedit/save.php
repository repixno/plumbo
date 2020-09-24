<?PHP


   import( 'pages.json' );

   class SaveProject extends JSONPage implements NoAuthRequired, IView {
    
        private $folder = '/var/www/repix/data/tedit/templates/'; 
         
         public function Execute() {

                $message = 'OK';
                    
                $data = $_POST['data'];
                $category = $_POST['category'];
                $category = 'Baby';
                $image = $_POST['image'];
                $name = $_POST['name'];
                
                $themefolder = $this->folder . $category . '/' . $name;
                if( !file_exists( $themefolder) ){
                    mkdir( $themefolder );
                }
                
                /*try{
                    $imageblob = str_replace( 'data:image/png;base64,', '' , $image );
                    $imageobject = new Imagick();
                    $imageobject->readImageBlob( $imageblob );
                    $imageobject->writeImage( $themefolder . '/thumb.png' );
                }catch( Exception $e ){
                    $message = $e->getMessage();
                }*/
                
                
                file_put_contents( $themefolder . '/theme.txt', $data  );
                file_put_contents( $themefolder . '/thumb.txt' , $image );
                
                
                $this->result = true;
                $this->data =  $data;
                $this->message = $message;
                return true;
         }      
   }



?>
