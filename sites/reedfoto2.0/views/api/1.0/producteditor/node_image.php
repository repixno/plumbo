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
            $imagesrc = DB::query( "SELECT filnamn FROM bildeinfo WHERE bid = ?", (int)$object->id )->fetchSingle();
            
            
            $object->src = "/data/bildearkiv/" . $imagesrc;
            $object = json_encode( $object, JSON_NUMERIC_CHECK );
            $send = urlencode( $object );
            
            $command  = sprintf( 'node /home/toringe/fabrictext/image.js "%s" ', $send  );
            //util::Debug($command);
            $kake = system( $command );
                
            if( file_exists('/tmp/' . $uniqueid . '.png' ) ){
                header('Content-Type: image/png');
                readfile( '/tmp/' . $uniqueid . '.png' );
            }

            die();
        }
        
    }

?>