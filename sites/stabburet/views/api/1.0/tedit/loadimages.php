<?PHP


   import( 'pages.json' );

   class SaveProject extends JSONPage implements NoAuthRequired, IView {

         public function Execute(){

                $albumid = $_POST['albumid'];
                
                $album = new Album( $albumid );
                
                
                
                
                //$data2 = unserialize( $data );
                $this->result = true;
                $this->data = $album->getImages();
                $this->thumbs = $thumbarray;  
                $this->message = 'OK';
                return true;
         }      
   }



?>
