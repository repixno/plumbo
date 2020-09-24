<?PHP
config( 'website.storage' );

   class Streamthumb extends Webpage implements IView {
      
      private $projectowners = array ( 842043, 842373, 948940 );
      protected $template = '';
      public function execute(){
         relocate( '/frontpage/' );
         die();
      }
      
      public $watermark  = '/var/www/repix/sites/website/views/images/watermarks/reedfoto_watermark.png';
      
      public function thumbnail($bid=0){
         header('Cache-control: max-age='.(60*60));
         $headers = getallheaders();
            
         if( isset( $headers['If-None-Match'] ) ) {
            // Output a 304 Not Modified header
            header( 'HTTP/1.1 304 Not Modified' );
            header( 'Content-Length: 0' );
            exit;
         }   
         $thumb = new Image($bid);
         $imagespath = Settings::Get( 'storage', 'path');
         $cachefilename = "/home/www/tmpbilder/" . $thumb->hashcode . ".mc_preview.jpg";
         try{
            if ( !file_exists($cachefilename) ) {
               $filsrc = $imagespath . $thumb->getFilename()  . ".preview.jpg";
               symlink($filsrc,$cachefilename);	
            }
               
            header( "Content-Type: image/jpeg" );
            header( 'content-length: ' . filesize( $cachefilename ) );
            // setup caching headers
            header( "ETag: \"" . $thumb->hashcode . "\"");
            header( "Accept-Ranges: bytes");
            header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
            header( 'Cache-Control: public' );
            header( 'Pragma: public' );
            
            readfile ($cachefilename);
          
         }catch (Exception $e){
            $cachefilename = '/var/www/repix/sites/static/webroot/gfx/404/not_found_100px.jpg';
            header( "Content-Type: image/jpeg" );
            readfile( $cachefilename );
         }
      
      }
      
      public function image($bid, $width=0, $height=0, $bw = 0, $rotate = null ){
         header('Cache-control: max-age='.(60*60));
         $headers = getallheaders();
         
         if(!$width || $width > 800){
            $width = 800;
         }
         if(!$height || $height > 600 ){
            $height = 600;
         }
         $image = new Image($bid);
         $cachefilename = "/home/www/tmpbilder/" .  $image->hashcode . "_" . $width . "_" . $height . "_$rotate" . "_$bw" . ".jpg";
         
         if( file_exists($cachefilename) ){
            $fileModTime = filemtime( $cachefilename );
            // if browser sent id, we check if they match
            if (isset($headers['If-Modified-Since']) &&  (strtotime($headers['If-Modified-Since']) == $fileModTime)){
               // Output a 304 Not Modified header
               header( 'HTTP/1.1 304 Not Modified' );
               header( 'Content-Length: 0' );
               exit;
            }
         }
         
         $image_dx = $image->x;
         $image_dy = $image->y;
         
         $thumbRatio = $width / $height;
         $imageRatio = $image->x / $image->y;
         
         if($thumbRatio > $imageRatio){
            $width = $height * $imageRatio;
         }else{
            $height = $width * $imageRatio;
         }

         $imagespath = Settings::Get( 'storage', 'path');
         $filesrc = $imagespath .  $image->getFilename();

            try{
               if($image->hashcode && file_exists($cachefilename)){
                  session_write_close();
                  // grab all request headers
                  $hashcode = md5_file($cachefilename);
                  
                  header("Content-Type: image/jpeg");
                  header( 'content-length: '.filesize( $cachefilename ) );
                  // setup caching headers
                  header( "ETag: \"" . $hashcode . "\"");
                  header( "Accept-Ranges: bytes");
                  header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                  header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime). ' GMT', true, 200);
                  header( 'Cache-Control: public' );
                  header( 'Pragma: public' );
                  readfile ($cachefilename);
            
               }
               else{
                  $thumb = new Imagick( $filesrc );
                  if( $rotate ){
                     if( $rotate == 90 || $rotate == 270 ){
                        $x = $height;
                        $y = $width;
                     }else{
                        $x = $width;
                        $y = $height;
                     }
                     $thumb->thumbnailImage( (int) $x,(int) $y, true);
                     $thumb->rotateImage(new ImagickPixel(), $rotate);
                  }
                  else{
                     $thumb->thumbnailImage( (int) $width, (int) $height, true);
                  }
                  if( $image->licensefee > 0 || in_array( $image['owner_uid'],  $this->projectowners ) ){
                     $watermark = new Imagick( $this->watermark  );
                     $watermark->thumbnailImage( (int)  $thumb->getImageWidth() / 2 , $thumb->getImageHeight() / 2 , true );
                     $thumb->compositeImage( $watermark, $watermark->getImageCompose() , $thumb->getImageWidth() - ( $watermark->getImageWidth()) , $thumb->getImageHeight() - ( $watermark->getImageHeight() ) );
                  }
                  if( $bw == 1 ){
                     $thumb->modulateImage(100,0,100);
                  }
                  
                  $thumb->writeImage( $cachefilename );
                  $hashcode = md5_file($cachefilename);
                  $fileModTime = filemtime( $cachefilename );
                  
                  header( "Content-Type: image/jpeg" );           
                  header( 'content-length: ' . filesize( $cachefilename ) );
                  // setup caching headers
                  header( "ETag: \"" . $hashcode . "\"");
                  header( "Accept-Ranges: bytes");
                  header( sprintf( 'Expires: %s GMT', gmdate( 'D, d M Y H:i:s', time() + ( 86400 * 60 ) ) ) );
                  header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime). ' GMT', true, 200);
                  header( 'Cache-Control: public' );
                  header( 'Pragma: public' );
                  
                  readfile ($cachefilename);
               }
            }catch (Exception $e){

               //$cachefilename = '/var/www/repix/sites/static/webroot/gfx/404/not_found_250px.jpg';
               //header( "Content-Type: image/jpeg" );
               //readfile( $cachefilename );      
            
               Util::Debug( $e );

            }

      }  
   }


?>