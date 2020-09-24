<?PHP
   

   class EftakkekortImageViewer extends Webpage implements IView {
      
      protected $resourcepath = '/data/pd/ef28/cms/takkkort/efcards/';
      
      private $mcserver = "http://sandra.eurofoto.no/ECommerceBridge/Library/GreetingCard/Themes/";
      
      protected $template = false;
      
    public function Execute(  $image = '', $force = false ) {

          $thumbname = $image . '.jpg';
          
          
          if( file_exists( $this->resourcepath . $thumbname ) ){
              
          }else{
              $image = base64_decode( $image );
    
    
              //$image = urlencode ( $this->mcserver . $image );
              $image = urlencode( $image );
              
              $image = str_replace( '%2F', '/', $image );
              $image = str_replace( '+', '%20', $image );
              
              
              
              
              
               if( strpos( $image, "packages" ) ){
                  $image =  "http://bente.eurofoto.no/ECommerceBridge" . $image;
               }else{
                  $image =  $this->mcserver . $image;
               }
              $handle = file_get_contents( "$image" );
          
              
              $img = new Imagick();
              $img->readImageBlob("$handle");
              
              $img->thumbnailImage( 150, 150, true );
              
              $img->writeImage( $this->resourcepath . $thumbname  );
              
          }
          
          header( 'Content-Type: image/jpeg' );
          
          //echo $handle;
          readfile( $this->resourcepath . $thumbname  );
          
          //$img->readImageFile($handle);
          //$img->resizeImage(128, 128, 0, 0);
          //$img->writeImage( $this->resourcepath . "foo.jpg" );   
    }

      
   }
   