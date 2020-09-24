<?PHP
   import( 'pages.json' );

   
   class SaveStabburetThumb extends JSONPage implements NoAuthRequired, IView {

         private $thumbfolder = '/data/pd/stabburet/lokk/share/'; 

         public function Execute() {

                $message = 'OK';
                $thumb = $_POST['thumb'];

                $thumbid = md5( uniqid() );
                
                try{
                    $thumbfolder = $this->thumbfolder;
                    if( !file_exists( $thumbfolder ) ){
                      mkdir( $thumbfolder );
                    }
                    file_put_contents( $thumbfolder . $thumbid . '.txt' , $thumb );
                }catch( Exception $e ){
                    
                }
                
                $this->result = true;
                $this->data =  $thumbid;
                $this->message = $message;
                return true;
         }      
   }



?>
