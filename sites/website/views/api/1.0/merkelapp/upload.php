<?PHP


   import( 'pages.json' );

   class UploadImage extends JSONPage implements NoAuthRequired, IView {
      
      private $tmpfolder = '/home/www/tmpbilder/';
      private $width = 154;
      private $height = 154;
      private $secret = 'Ku6uwuRi';
      
      /**
       * Execute
       * 
       * @author Tor Inge Løvland
       */
      public function Execute() {
 
         
         try{
            $filename = md5( uniqid() . $this->secret ) . '.jpg';
            if (move_uploaded_file($_FILES['file']['tmp_name'],  $this->tmpfolder . $filename )) {
               
               //instantiate the image magick class
               $image = new Imagick( $this->tmpfolder . $filename );
               
               //crop and resize the image
               $image->thumbnailImage ( 500, 500, true );
               $image->writeImage( $this->tmpfolder . $filename);
               
               $data = array('filename' => $filename );
                
            } else {
               
                $data = array('error' => 'Failed to save' . serialize( $_FILES ) );
                
            }
         }catch ( Exception  $e){
            $data = array('error' => 'fail'  );
         }
         
         header('Content-type: text/html');
         echo json_encode($data);
         
         
         
         die();
         
      }
      
      
   }


?>