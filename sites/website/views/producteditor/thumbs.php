<?PHP
config( 'website.storage' );

    class teditThumbs extends WebPage implements IView {
        
        
        protected $template = null;
        
        public function Execute( $id, $width = 0, $height = 0, $cropwidth = null , $cropheight = null, $cropx = null, $cropy = null ){
            
            $thumb = new Image($id);
            
            //$thumb = $thumb->asArray();
            
            $imagespath = Settings::Get( 'storage', 'path');
            
            $rotate= null;
            $bw = 0;
         
            $imagewidth = 800;
            $imageheight = 600;
         
            $thumbRatio = $imagewidth / $imageheight;
            $imageRatio = $thumb->x / $thumb->y;
            
            if($thumbRatio > $imageRatio){
                $imagewidth = $imageheight * $imageRatio;
             }else{
                $imageheight = $imagewidth * $imageRatio;
             }
         
            $filesrc = $imagespath .  $thumb->getFilename();
            
            
            
            
            $cachefilename = "/home/www/tmpbilder/" .  $thumb->hashcode . "_" . $imagewidth . "_" . $imageheight . "_$rotate" . "_$bw" . ".jpg";
            
            
            if( !file_exists( $cachefilename ) ){
                $thumbfile = new Imagick( $filesrc );
                if( $rotate ){
                     if( $rotate == 90 || $rotate == 270 ){
                        $x = $height;
                        $y = $width;
                     }else{
                        $x = $width;
                        $y = $height;
                     }
                     $thumbfile->thumbnailImage( (int) $x,(int) $y, true);
                     $thumbfile->rotateImage(new ImagickPixel(), $rotate);
                  }
                  else{
                     $thumbfile->thumbnailImage( (int) $width, (int) $height, true);
                  }
                  if( $bw == 1 ){
                     $thumbfile->modulateImage(100,0,100);
                  }
                  
                  $thumbfile->writeImage( $cachefilename );
            }
            
            
            $image = new Imagick($cachefilename);
            
            if( $width == 0 ){
                $width = 100;
                $height = 100;
            }else if ( $_GET['crop'] == 'true'  || ( $cropheight == null && $cropwidth == null ) ){
                /*$width = 300;
                $height = 300;*/
            }
            else{
                $image->thumbnailImage( (int) $width, (int) $height, true);
                $image->cropImage( $cropwidth, $cropheight , $cropx , $cropy );
            }
            
            $image->thumbnailImage( (int) $width, (int) $height, true);
            
            //$image->resizeImage ( (int) $width, (int) $height,  imagick::FILTER_LANCZOS );
            
            header( "Content-Type: image/jpeg" );
            echo $image;
        
        }

      
      
   }
   
   ?>