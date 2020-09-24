<?php

import( 'core.settings' );
model( 'user.cewemyphotos');


class ceweMyPhotos extends DBceweMyphotos{
    
    static function getByUserid($userid){
        
        try{
            return ceweMyPhotos::fromFieldValue(
                      array(
                         'userid' => $userid,
                      ),
                      'ceweMyPhotos'
                   );
        }catch( Exception $e ){
            return false;
        }
        
    }
    
    
    
}