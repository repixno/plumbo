<?php
/**
 * ******************************************************

 *********************************************************/

class Clipart extends WebPage implements IView {

    protected $template = null;
    
    private $path = "/var/www/marie.eurofoto.no/sites/ukeplan/webroot/familiefigurer/";
    private $savepath = "/data/pd/ef28/cms/clipart/thumbs/";
    
    
    public function Execute( $cat, $name ){
        
        $filepath =  $this->savepath . $cat . "/" . $name;
        
        header( "Content-Type: image/jpeg" );
        
        echo file_get_contents($filepath);
        
        
     }
     
    public function catlist( $cat = "" ){
        
        $savepath = $this->savepath . $cat ;
        
        foreach( glob( $savepath . "/*")  as $file ){
            
            $filename[] = basename( $file );
            
        }
        
        header('Content-Type: application/json');
        echo json_encode( $filename );
        
    }
    
    public function cat( $cat = "" ){
        
        $savepath = $this->savepath . $cat . "/";
        
        
        if( !file_exists( $savepath ) ){
            mkdir( $savepath );
        }
        
        $catpath = $this->path . $cat;
        
        foreach( glob( $catpath . "/*")  as $file ){
            
            $imagic = new Imagick($file);
            
            $imagic->resizeImage(400,400, imagick::FILTER_LANCZOS, 0.9, true);
            
            $filename = basename( $file );
            
            $imagic->writeImage( $savepath . $filename );
            
            Util::Debug( basename(  dirname( $file ) )  );
            
        }
        
        
    }
    
    

    
}

