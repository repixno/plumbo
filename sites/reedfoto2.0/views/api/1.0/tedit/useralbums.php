<?PHP
   import( 'pages.json' );
   
   class SaveProject extends JSONPage implements NoAuthRequired, IView {

         public function Execute(){

                //$albumid = $_POST['albumid'];
                
                $albums = Album::enum();
                            
                
                //$data2 = unserialize( $data );
                $this->result = true;
                $this->data = $albums;
                $this->thumbs = $thumbarray;  
                $this->message = 'OK';
                return true;
         }      
   }
?>
