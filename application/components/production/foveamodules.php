<?PHP

class foveamodules{    
    
    private $orderfolder = "/home/produksjon/man_ord/foveatest/";
    
    public function convertimage( $imagefile = '', $destination, $module, $prevdestination = null ){
        
        //$filename = "";
        //$imagefile = $this->orderfolder . "test.jpg";
        
        //Util::Debug( $module );
        
        $templatefolder = '/home/produksjon/fovea/maler/';
        
        //3000x3543
        $image = new Imagick();
        $image->setResolution(300,300);
        $image->newImage(2398, 3543, new ImagickPixel('white'));
        
        $image->setImageFormat('jpg');
        
        
        if( file_exists( $imagefile ) && is_array( $module )  ){
            //$module = 'modul5';
            
            
            foreach( $module as $ret ){
                
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
                    $userimage->scaleImage( $ret['dx'],  $ret['dy'], true );
                    
                    if( $ret['color'] == 'bw' ){
                        $userimage->modulateImage(100,0,100);
                    }
                    
                    if( $ret['border'] ){
                        $borders = explode( '-', $ret['border']  );
                        
                        foreach( $borders as $border ){
                            $borderarr = explode( '.' , $border );
                                
                            if( $borderarr[0]  == 'F' ){
                                $bordercolor = "#FFFFFF";
                            }else{
                                $bordercolor = "#000000";
                            }
                            
                            $bordersize = explode( 'x', $borderarr[1] );
                                
                            $userimage->borderImage( $bordercolor, $bordersize[0], $bordersize[1] );
                        }
                        
                    }else{
                        $userimage->borderImage( '#000000', 1, 1 );
                    }
                    
                }
                
                $imagewidth = $userimage->getImageWidth();
                $imageheight = $userimage->getImageHeight();
                $x = $ret['x'];
                $y = $ret['y'];
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
                
                
                
            }                     
            //$userimage->scaleImage( 2398,  3543, true );
            //$image->compositeImage( $userimage, $userimage->getImageCompose() , 0 , 0 );
            
            $image->setImageFormat('jpeg');
            $image->setImageCompressionQuality(100);
            //$image->writeImage( $this->orderfolder . $module. "-m.jpg" );
            $image->writeImage( $destination );
            
            if( !file_exists( dirname( $prevdestination ) ) ){
                mkdir( dirname( $prevdestination ), 0755, true );
            }
            
            $image->writeImage( $prevdestination );
        }
        
    }
    
}

?>