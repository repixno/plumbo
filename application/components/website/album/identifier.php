<?PHP
   
   model( 'album.identifier' );
   
   class AlbumIdentifier extends DBAlbumIdentifier {
      
      static function create() {
         
         $chars = preg_split( '//', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 0, PREG_SPLIT_NO_EMPTY );
         
         do {
         
            $segments = array();
            for( $i = 0; $i < 3; $i++ ) {
               $segment = '';
               foreach( array_rand( $chars, 4 ) as $key ) {
                  $segment .= $chars[$key];
               }
               $segments[] = $segment;
            }
            
            $identifier = implode( '-', $segments ); 
            
         } while( AlbumIdentifier::fromIdentifier( $identifier ) !== false );
         
         $object = new AlbumIdentifier();
         $object->identifier = $identifier;
         
         return $object;
         
      }
      
      static function nextBatchId() {
         
         return DB::query( 'SELECT MAX(batchid) FROM album_identifiers' )->fetchSingle() + 1;
         
      }
      
      static function fromIdentifier( $identifier ) {
         
         return AlbumIdentifier::fromFieldValue( array( 'identifier' => $identifier ), 'AlbumIdentifier', false );
         
      }
      
   }
   
?>