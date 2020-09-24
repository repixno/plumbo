<?php

    import( 'pages.json' );
    library( 'fonts.yctinttf' );
    
    
    class CreateSmilesText extends JSONPage implements NoAuthRequired, IView {
        
        private $fontfolder = '/var/www/repix/sites/website/webroot/editorfonts/';
        
     
        public function Execute(){
            
            $object = $_GET['object'];
            $ratio =  $_GET['ratio'];
            
            $object = urldecode( $object );
            $object = json_decode( $object );
            
            $uniqueid = uniqid();
            $object->uniqueid = $uniqueid;
            $object->ratiokake = $ratio;
            $object->fontFamily = str_replace( "'", "", $object->fontFamily );
            try{
                $object->fontfile = $this->getFontfile( $object->fontFamily );
                
            }catch( Exception $e ){
                echo "kakke";
            }
            
            
            

            $object = json_encode( $object, JSON_NUMERIC_CHECK );
            $send = urlencode( $object );
            
            
            
            $command  = sprintf( 'node /home/toringe/fabrictext/text.js "%s" ', $send  );
            

            $kake = system( $command );
                
            if( file_exists('/tmp/' . $uniqueid . '.png' ) ){
                header('Content-Type: image/png');
                readfile( '/tmp/' . $uniqueid . '.png' );
            }
            
            exit;
        }
        
        
        
        public function getFontfile( $fontname ){
            
            $fontlist = array();
            
            
            
            foreach( glob( $this->fontfolder . '*ttf' ) as $font ){  
                
                //create yctin_ttf object
                $ttf = new ycTIN_TTF();
                 
                //open font file
                if ($ttf->open($font)) {
                    $rs = $ttf->getNameTable();
                    $rs = reset( $rs );
                    if( $fontname == $rs[1] ){
                        
                       return $font;
                        
                    }
                }
                
            }
        }
    }

?>