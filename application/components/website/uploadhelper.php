<?PHP
   
   import( 'core.db' );
   
   class UploadHelper {
      
      static function getBatchId() {
         
         return DB::Query( "SELECT nextval('flash_uploader_batch_id_seq')" )->fetchSingle();
         
      }
      
      static function getBatchImageIds( $batchid, $userid = false ) {
         
         if( !$userid ) $userid = Login::userid();
         
         $images = array();
         foreach( DB::query( 'SELECT bid FROM flash_uploader_images WHERE batch_id = ? AND uid = ?', $batchid, $userid )->fetchAll() as $row ) {
            list( $images[] ) = $row;
         }
         return $images;
         
      }

      static function iOsDetection(){
         $iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
         $iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
         $iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
         
         if( $iPod ) return "iPod";
         else if( $iPhone ) return "iPhone";
         else if( $iPad ) return "iPad";
         else return NULL;

      }
      
      
   }
   
?>