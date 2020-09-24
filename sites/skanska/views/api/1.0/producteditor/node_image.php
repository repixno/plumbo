<?php

    import( 'pages.json' );
    library( 'fonts.yctinttf' );
    
    
    class CreateNodeImage extends JSONPage implements NoAuthRequired, IView {

        public function Execute(){
            
            $object = $_GET['object'];
            $ratio =  $_GET['ratio'];
            
            $object = json_decode( $object );
            $uniqueid = uniqid();
            $object->uniqueid = $uniqueid;
            $object->ratio = $ratio;
            
            $imarr == array();
            $fileinfo = parse_url($object->src , PHP_URL_PATH );
            
            list( $null, $null, $null,  $imarr['imageid'] , $imarr['width'], $imarr['height'], $imarr['cropwidth'], $imarr['cropheight'], $imarr['cropx'], $imarr['cropy']  ) = explode( "/" , $fileinfo );
            foreach( $imarr as $key=>$ret ){
                if( $key == 'imageid' ){
                    $$key  =  $ret;
                }else{
                    $$key  =  $ret * $ratio;
                }
            }
            
            $imagesrc = "/data/bildearkiv/" . DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", (int)$object->id )->fetchSingle();

            try{
                if( $cropx ){
                    
                  $object->src  =  $this->Thumb( $imageid , $width, $height, $cropwidth, $cropheight, $cropx, $cropy , $imagesrc );
                    
                }else{
                    $object->src =  $imagesrc;
                }
            }catch( Exception $e ){
                
                mail( 'tor.inge@eurofoto.no', "test node1 " , print_r( $e, true ) );
                
            }
            
            $object = json_encode( $object, JSON_NUMERIC_CHECK );
            $send = urlencode( $object );
            $command  = sprintf( 'node /home/toringe/fabrictext/image.js "%s" ', $send  );
            
            //util::Debug($command);
            $kake = system( $command );
            
            if( file_exists('/tmp/' . $uniqueid . '.png' ) ){
                
                header('Content-type: image/png');
                //$immg = new  Imagick('/tmp/' . $uniqueid . '.png');
                
                readfile( '/tmp/' . $uniqueid . '.png' );
            }

            exit;
        }
        
        public function Thumb( $id, $width = 0, $height = 0, $cropwidth = null , $cropheight = null, $cropx = null, $cropy = null, $imagesrc  ){
            
            $filesrc = $imagesrc;
            
            $cachefilename = "/tmp/" .  $thumb->hashcode . ".jpg";
            $image = new Imagick( $filesrc );
            
            $imagewidth = $image->getImageWidth();
            
            $imageratio = $imagewidth / $width;

            $image->cropImage( $cropwidth * $imageratio , $cropheight * $imageratio , $cropx  * $imageratio , $cropy * $imageratio );
            
            $image->writeImage( $cachefilename );
            
            return $cachefilename;
        
        }
        
    }

?>