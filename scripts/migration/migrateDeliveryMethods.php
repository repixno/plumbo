<?PHP
   
   $languages = array(
                      0x01 => 'en_US',
                      0x02 => 'nb_NO',
                   );
   
   include "../../bootstrap.php";
   
   config( 'website.config' );
   
   import( 'website.deliverytype' );
   
   $records = DB::query( "SELECT deliveryid, name, description FROM region_delivery" );
   while( list( $deliveryid, $name, $description ) = $records->fetchRow() ) {
      
      echo "Processing $deliveryid... ";
      
      try {
         
         $deliverytype = DeliveryType::fromRefId( $deliveryid );
         echo "Skipped!\n";
         
      } catch( SecurityException $e ) {
         
         $deliverytype = new DeliveryType();
         $deliverytype->refid = $deliveryid;
         $deliverytype->save();
         
         foreach( $languages as $languageid => $language ) {
            
            $title = DB::query( 'SELECT message FROM language_resource WHERE lang_res_id = ? AND language = ?', $name, $languageid )->fetchSingle();
            $body = DB::query( 'SELECT message FROM language_resource WHERE lang_res_id = ? AND language = ?', $description, $languageid )->fetchSingle();
            
            $deliverytype->setTitle( $title, $language );
            $deliverytype->setBody( $body, $language );
            
         }
         
         $deliverytype->save();
         
         echo "Migrated!\n";
         
      }
      
   }
   
?>