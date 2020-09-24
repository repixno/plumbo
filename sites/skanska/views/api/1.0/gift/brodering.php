<?PHP
   import( 'pages.json' );
   library( 'fonts.yctinttf' );
   
   class Broderingpreview extends JSONPage implements NoAuthRequired, IView {
    
         private $folder = '/var/www/repix/sites/website/webroot/fonts/'; 
         
         public function Execute() {
            

                
            $image = new Imagick();
            $image->newImage(100, 100, new ImagickPixel('red'));
            $image->setImageFormat('png');
            
            header('Content-type: image/png');
            echo $image
                
                
            
         }      
   }



?>