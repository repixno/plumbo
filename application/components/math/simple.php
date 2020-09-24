<?PHP


define( 'SECRET_KEY', 'Gai9aim9' );

class SC{
    
    
    static function encode( $string ){
        
        return urlencode(  base64_encode( $string   . '-' .  md5( $string . SECRET_KEY ) ) );
        
    }
    
    
    static function decode( $code ){
                
        $code = urldecode( base64_decode( $code ) );
        
        $code = explode( '-', $code );
        
        if( md5( $code[0] . SECRET_KEY ) ===   $code[1] ){
            return $code[0];
        }else{
            return false;
        }
        
    }
    
    
    
    
}


?>