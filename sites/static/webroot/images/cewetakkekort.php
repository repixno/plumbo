<?PHP
   

   class CewetakkekortImageViewer extends Webpage implements IView {
        protected $resourcepath = '/data/pd/ef28/cms/takkkort/cewethumbs/';
        protected $template = false;
      
        public function Execute(  $image = '', $force = false ) {

            $thumbname = $image . '_thumbnail.jpg';
          
          
            if( file_exists( $this->resourcepath . $thumbname ) ){
              
            }else{
                
                $img = new Imagick( $this->resourcepath . $image );
                $img->thumbnailImage( 150, 150, true );
              
                $img->writeImage( $this->resourcepath . $thumbname  );
              
          }
          
          header( 'Content-Type: image/jpeg' );
          
          //echo $handle;
          readfile( $this->resourcepath . $thumbname  );
          
    }

      
   }
   
?>