<?

config('production.reedfoto');

class FetchalbumLogin extends WebPage implements IView {
      
    protected $template = null;
    private $module = "";
    
    function Execute(){
        
        $this->module = $_GET['module'];
        
        $module = Settings::Get( 'reedfoto' , 'modules' );
        $selected = $module[$_GET['module']];

        if( $_GET['portrait'] || $selected['modules'] )
        $imagefile = DB::query("SELECT filnamn FROM bildeinfo WHERE bid = ?", $_GET['portrait'] )->fetchSingle();
        if( $_GET['klassebilde'] )
        $gruppeimagefile = DB::query("SELECT filnamn FROM bildeinfo WHERE bid = ?", $_GET['klassebilde'] )->fetchSingle();
        
        $imagefile = '/data/bildearkiv/' . $imagefile . '.preview.jpg';
        $gruppeimage = '/data/bildearkiv/' . $gruppeimagefile . '.preview.jpg';

        
        
        if(  $selected['modules']  ){
            $image = $this->doConvertmodule($imagefile, $gruppeimage, $selected, $text, $ratio);
        }
        else{
            $artnr =  $_GET['module'];
            $ratio = $selected['size']['x'] / 150;
            $image = $this->doConvertimage($artnr, $imagefile, $gruppeimage, $selected, $text, $ratio ); 
        }
       
                
        header('Content-type: image/jpeg');
        echo $image;
    }
    
    public function doConvertmodule( $imagefile, $gruppeimage = null, $artnrs, $text = null, $ratio){

        
        $module = Settings::Get( 'reedfoto' , 'modules' );
        
        $images = array();
        
        $finalthumb = new Imagick();
        $finalthumb->newImage(300,200, new ImagickPixel('white'));
        $finalthumb->setImageFormat('jpg');
        
        $x = 0;
        $y = 0;
        
        foreach( $artnrs['modules'] as $artnr ){
 
            //'modules' => array( 7379, 7380, 7385 )
            
            if( $artnr == 7379 ){
                $x = 120;
                $y = -10;
            }
            if( $artnr == 7380 ){
                $x = 150;
                $y = 90;
            }
             if( $artnr == 7385 ){
                $x = 10;
                $y = 10;
            }
            
            
            
            $selected = $module[$artnr];
            $ratio = $selected['size']['x'] / 150;
            $image = $this->doConvertimage($artnr, $imagefile, $gruppeimage, $selected, $text, $ratio);
            
            $image->rotateImage( new ImagickPixel('none'),-10 );
            
            $finalthumb->compositeImage( $image, $image->getImageCompose() , $x , $y );
        
            
        }
        
        return $finalthumb;
        //exit;
        
    }
    
    public function doConvertimage( $artnr, $imagefile, $gruppeimage = null, $module, $text = null, $ratio){
        
       
        
        $templatefolder = "/var/www/repix/sites/static/webroot/gfx/reedfoto/";
        $mainwidth = $module['size']['x'] / $ratio;
        $mainheight = $module['size']['y'] / $ratio ;

        //3000x3543
        $image = new Imagick();
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
                    $draw->setFont($templatefolder. "pala.ttf");
                    $draw->setFontSize( 4 );
                    
                    
                    $metrics = $image->queryFontMetrics($draw, $text);
                    $x = ($mainwidth -  ( $metrics['textWidth'] / $ratio ) ) / 2;
                    
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
                
                try{
                    if( $ret['dx'] /  $ret['dy']  > $userimage->getImageWidth() / $userimage->getImageHeight() ){
                        $imgratio =  $userimage->getImageWidth() / ( $ret['dx'] / $ratio );
                         
                         $y = ( $userimage->getImageHeight() - ( $ret['dy'] * $imgratio ) )  / 2  ;
                         //Util::Debug( $y );
                         if( $y  > 10  ){
                              $userimage->cropImage($userimage->getImageWidth()  , $ret['dy'] * $imgratio, 0, $y );
                         }  
                     }
                     $userimage->scaleImage( $ret['dx'] / $ratio,  $ret['dy'] / $ratio, true );
                }catch( Exception $e){
                    
                }
                
                
                
                if( $ret['color'] == 'bw' ){
                    $userimage->modulateImage(100,0,100);
                }
                        
              }
                    
                $imagewidth = $userimage->getImageWidth();
                $imageheight = $userimage->getImageHeight();
    
                /*if( $imagewidth != $ret['dx'] ){
                    $cropx = ( $imagewidth - $ret['dx'] ) / 2;
                    $userimage->$image->cropImage($ret['dx'],$ret['dy'], $cropx ,0);
                }
                if( $imageheight != $ret['dy'] ){
                    
                    $cropy = ( $imageheight - $ret['dy'] ) / 2;
                    
                    $userimage->$image->cropImage($ret['dx'],$ret['dy'], 0 ,$cropy);
                    
                }*/
                
                $x = $ret['x'] / $ratio;
                $y = $ret['y'] / $ratio;
                $u = 1;   
                if( empty( $ret['repx'] ) ) $ret['repx'] = 1;
                if( empty( $ret['repy'] ) ) $ret['repy'] = 1;
                
                for ($i = 1; $i <= $ret['repx']; $i++) {
                    
                    $image->compositeImage( $userimage, $userimage->getImageCompose() , $x , $y );
                    $x += $imagewidth + $ret['stepx'] + 3;
                    
                    if( $i == $ret['repx'] && $u < $ret['repy'] ){
                        
                        $y +=  $imageheight + 2+ 0;
                        $i = 0;
                        $x = $ret['x'] / $ratio;
                        $u++;
                    }
                }
                
            
            }
            
            
            
            if( $this->module  == '7291'  ){
                
                $image = new Imagick( '/var/www/repix/sites/reedfoto2.0/webroot/imgs/products/digital.png' );
                
            }
            
            if( $artnr  == '7385' || $artnr == '7286' ){
                $image->rotateImage( new ImagickPixel('none'), -90 );
            }

            
            
            
            
            
        
            return  $image;
            
            
            /*if( !file_exists( dirname( $prevdestination ) ) ){
                mkdir( dirname( $prevdestination ), 0755, true );
            }
            
            $image->writeImage( $prevdestination );*/
        }
        
    }
    
    

}
      
?>