<?PHP

class reedFotoModuleConvert{    
    
    private $orderfolder = "/home/produksjon/man_ord/foveatest/";
    
    public function convertimage( $imagefile, $gruppeimage, $rfmodule, $bw = false ){
        config( 'production.reedfoto' );
        
        $attributes = unserialize( $rfmodule['attributes']);
        
        $gruppetype = DB::query("SELECT identifier FROM bildeinfo WHERE bid = ?", $attributes['gruppebid'])->fetchSingle();
        $aid = DB::query("SELECT aid FROM bildeinfo WHERE bid = ?" , $attributes['bid'] )->fetchSingle();
        $text = DB::query( "SELECT school from reedfoto_album where aid = ?" , $aid)->fetchSingle();
        $grade = DB::query( "SELECT grade from reedfoto_album where aid = ?" , $aid)->fetchSingle();
        
        if( $gruppetype == "gruppe-fellesutenmal" ){
            $text = "utemmal";
        }
        else if( $gruppetype == "gruppe"  ){
            $text .= " - " . $grade;
        }
        
        $id = substr($rfmodule['id'], -3);
        $artnr = $rfmodule['artikkelnr'];
        $filename = sprintf( "%03d",  $rfmodule['antall'] ) . "-" . $rfmodule['ordrenr'] . "-" . $id;
        
        $module = Settings::Get( 'reedfoto' , 'modules' );
        $selected = $module[$artnr];
        
        if( $selected['modules'] ){
            foreach( $selected['modules'] as $artikkelnr ){
                $this->doConvertimage($imagefile, $gruppeimage, $module[$artikkelnr] ,  $filename . "-" .$artikkelnr, $text, $bw);
            }
        }else{
            $this->doConvertimage($imagefile, $gruppeimage, $module[$artnr], $filename . "." .$artnr , $text, $bw);
        }
        
        
        
    }
    
    public function doConvertimage( $imagefile, $gruppeimage, $module, $artnr , $text, $bw = false){
        
        $mainwidth = $module['size']['x'];
        $mainheight = $module['size']['y'];
        //$filename = "";
        //$imagefile = $this->orderfolder . "test.jpg";
        
        $imageid = basename($imagefile, ".jpg");
        
        $templatefolder = '/home/reedfoto/maler/';
        //3000x3543
        $image = new Imagick();
        $image->setResolution(300,300);
        $image->newImage($mainwidth,$mainheight, new ImagickPixel('white'));
        $image->setImageFormat('jpg');
        
        
        if( file_exists( $imagefile ) && is_array( $module['template'] )  ){
            //$module = 'modul5';
            foreach( $module['template'] as $ret ){
                
                if( $ret['mal'] && $text == 'utemmal' ){
                    continue;
                }
                else if( $ret['mal'] ){
                    $userimage = new Imagick( $templatefolder .  $ret['mal'] );
                }
                else if( $ret['text'] ){
                    
                    $userimage = new Imagick();
                    $draw = new ImagickDraw();
                    $pixel = new ImagickPixel( 'none' );
                    
                    /* New image */
                    $userimage->newImage($mainwidth, $mainheight, $pixel);
                    
                    $draw->setFillColor('white');

                    /* Font properties */
                    $draw->setFont("/home/reedfoto/maler/pala.ttf");
                    $draw->setFontSize( 90 );
                    
                    
                    $metrics = $image->queryFontMetrics($draw, $text);
                    $x = ($mainwidth -  $metrics['textWidth']) / 2;
                    
                    /* Create text */
                    $image->annotateImage($draw, $x, 2310, 0, $text);
                    
                    
                }
                else{
                    
                    if( $ret['type'] == "gruppe" ){
                        $userimage = new Imagick( $gruppeimage );
                    }else{
                        $userimage = new Imagick( $imagefile );
                        if( $userimage->getImageWidth() > $userimage->getImageHeight() ){
                            $userimage->rotateImage( new ImagickPixel('none'),90 );
                        }
                    }
                    if( $ret['deg'] != 0 ){
                        $userimage->rotateImage( new ImagickPixel('none'), $ret['deg']);
                    }
                    if( $bw == 'true'){
                        $userimage->modulateImage(100,0,100);
                    }
                    
                    if( $ret['dx'] /  $ret['dy']  > $userimage->getImageWidth() / $userimage->getImageHeight() ){
                       $ratio =  $userimage->getImageWidth() / $ret['dx'];
                        
                        $y = ( $userimage->getImageHeight() - ( $ret['dy'] * $ratio ) )  / 2  ;
                        //Util::Debug( $y );
                        if( $y  > 10  ){
                             $userimage->cropImage($userimage->getImageWidth()  , $ret['dy'] * $ratio, 0, $y );
                        }  
                    }
                    
                    $userimage->scaleImage( $ret['dx'],  $ret['dy'], true );
                    
                    if( $ret['color'] == 'bw' && !$bw ){
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
                
                
                Util::Debug($imagewidth ." " . $ret['dx']);
                Util::Debug($imageheight ."   " . $ret['dy']);
                
                /*if( $imagewidth != $ret['dx'] ){
                    $cropx = ( $imagewidth - $ret['dx'] ) / 2;
                    $userimage->$image->cropImage($ret['dx'],$ret['dy'], $cropx ,0);
                }
                if( $imageheight != $ret['dy'] ){
                    
                    $cropy = ( $imageheight - $ret['dy'] ) / 2;
                    
                    $userimage->$image->cropImage($ret['dx'],$ret['dy'], 0 ,$cropy);
                    
                }*/
                
                $x = $ret['x'];
                $y = $ret['y'];
                $u = 1;   
                if( empty( $ret['repx'] ) ) $ret['repx'] = 1;
                if( empty( $ret['repy'] ) ) $ret['repy'] = 1;
                
                for ($i = 1; $i <= $ret['repx']; $i++) {
                    Util::Debug( "i:" . $i );
                    $image->compositeImage( $userimage, $userimage->getImageCompose() , $x , $y );
                    Util::Debug( $x );
                    $x += $imagewidth + $ret['stepx'] + 0;
                    
                    if( $i == $ret['repx'] && $u < $ret['repy'] ){
                        Util::Debug( $u );
                        $y +=  $imageheight + $ret['stepy'] + 0;
                        $i = 0;
                        $x = $ret['x'];
                        $u++;
                    }
                }
                
            }                    
            //$userimage->scaleImage( 2398,  3543, true );
            //$image->compositeImage( $userimage, $userimage->getImageCompose() , 0 , 0 );
            
            $orderfolder = dirname($imagefile);
            $orderfolder = str_replace( 'autoedit', '',$orderfolder );
            $destination  = $orderfolder  . $artnr . ".jpg";
            
            $image->setImageFormat('jpeg');
            $image->setImageCompressionQuality(100);
            //$image->writeImage( $this->orderfolder . $module. "-m.jpg" );
            $image->writeImage( $destination );
            
            /*if( !file_exists( dirname( $prevdestination ) ) ){
                mkdir( dirname( $prevdestination ), 0755, true );
            }
            
            $image->writeImage( $prevdestination );*/
        }
        
    }
    
}

?>