<?PHP

class foveaTemplate{    
    
    private $orderfolder = "/home/produksjon/man_ord/foveatest/";
    
    public function convertimage( $imagefile = '', $destination, $module){
        
        //$filename = "";
        //$imagefile = $this->orderfolder . "test.jpg";
        //$destination = "/home/produksjon/fovea/test/15x15_liggende_08.jpg";
        //$module = '15x15_liggende_08';
        //Util::Debug( "Module!!!!!!!!" . $module );
        
        $templatefolder = '/home/produksjon/fovea/maler/';
        
        $modularray = 
        
        
        $image = new Imagick();
        $image->setResolution(300,300);
        $image->newImage( $module['size']['width'], $module['size']['height'], new ImagickPixel('white'));
        $image->setImageFormat('jpg');
         
        if( file_exists( $imagefile ) && is_array( $module ) ){
            
            
            if( !file_exists( dirname( $destination ) ) ){
               mkdir( dirname( $destination ) , 0755 , true );
            }
            foreach( $module['template'] as $ret ){
                
                $x = $ret['x'];
                $y = $ret['y'];
                
                
                //Util::Debug( $ret['mal'] );
                //Util::Debug( $templatefolder .  $ret['mal'] );
                
                
                if( $ret['mal'] ){
                    $userimage = new Imagick( $templatefolder .  $ret['mal'] );
                }else{
                    
                    
                    $userimage = new Imagick( $imagefile );
                    if( $userimage->getImageWidth() > $userimage->getImageHeight() ){
                        $userimage->rotateImage( new ImagickPixel('none'),90 );
                    }
                    if( $ret['deg'] != 0 ){
                        $userimage->rotateImage( new ImagickPixel('none'), $ret['deg']);
                    }
                    
                    
                    $imageratio = $userimage->getImageHeight() / $userimage->getImageWidth();
                    
                    //util::Debug( "iamgeratiop" . $imageratio );                    
                    $placeholderratio = $ret['dy'] / $ret['dx'];
                    //util::Debug( "placeholderratio" . $placeholderratio );
                    
                    if( $placeholderratio > 1 ){
                        if(  $imageratio > $placeholderratio ){
                            $imagewidth = $ret['dx'];
                            $imageheight = $ret['dx'] * $imageratio;
                            $y = $y - ( ( $imageheight - $ret['dy'] ) / 2 );
                            
                        }else if( $imageratio < $placeholderratio ){
                            $imagewidth = $ret['dy']  * $imageratio;
                            $imageheight = $ret['dy'];
                            $x = $x - ( ( $imagewidth - $ret['dx'] ) / 2 );
                        }
                    }
                    else if ( $placeholderratio < 1 ){
                        if(  $imageratio > $placeholderratio ){
                            $imagewidth = $ret['dx'];
                            $imageheight = $ret['dx'] / $imageratio;
                            $y = $y - ( ( $imageheight - $ret['dy'] ) / 2 );
                        }else if( $imageratio < $placeholderratio ){
                            $imageheight = $ret['dy'];
                            $imagewidth = $ret['dy'] / $imageratio;
                            $x = $x - ( ( $imagewidth - $ret['dx'] ) / 2 );
                        }
                    }
                    
                    
                $userimage->scaleImage( $imagewidth,  $imageheight, true );
                
                $imagewidth = $userimage->getImageWidth();
                $imageheight = $userimage->getImageHeight();
                
                
                
                /*
                $u = 1;
                if( empty( $ret['repx'] ) ) $ret['repx'] = 1;
                if( empty( $ret['repy'] ) ) $ret['repy'] = 1;
                
                for ($i = 1; $i <= $ret['repx']; $i++) {
                    Util::Debug( "i:" . $i );
                    $image->compositeImage( $userimage, $userimage->getImageCompose() , $x , $y );
                    Util::Debug( $x );
                    $x += $imagewidth + $ret['stepx'] + 2;
                    
                    if( $i == $ret['repx'] && $u < $ret['repy'] ){
                        Util::Debug( $u );
                        $y +=  $imageheight + $ret['stepy'] + 2;
                        $i = 0;
                        $x = $ret['x'];
                        $u++;
                    }
                }
                */
                
            }
            $image->compositeImage( $userimage, $userimage->getImageCompose() , $x , $y );
        }
        $image->setImageFormat('jpeg');
        $image->setImageCompressionQuality(100);
        $image->writeImage( $destination );
        
    }
    
    }
}

?>