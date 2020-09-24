<?

    model( 'reedfoto.album' );
    
    
    class Reedfotoalbum extends DBReedfotoAlbum {

 
 
 
 
        static function fromIdentifier( $identifier ) {
         
            return Reedfotoalbum::fromFieldValue( array( 'identifier' => $identifier ), 'Reedfotoalbum', false );
         
        }
 
    }


?>