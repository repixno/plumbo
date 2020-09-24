<?PHP
   
   $languages = array(
                      0x01 => 'en_US',
                      0x02 => 'nb_NO',
                   );
   
   include "../../bootstrap.php";
   
   config( 'website.config' );
   
   import( 'website.paymenttype' );
   
   $records = DB::query( "SELECT paymentid, name, description FROM region_payment" );
   while( list( $paymentid, $name, $description ) = $records->fetchRow() ) {
      
      echo "Processing $paymentid... ";
      
      try {
         
         $paymenttype = PaymentType::fromRefId( $paymentid );
         echo "Skipped!\n";
         
      } catch( SecurityException $e ) {
         
         $paymenttype = new PaymentType();
         $paymenttype->refid = $paymentid;
         $paymenttype->save();
         
         foreach( $languages as $languageid => $language ) {
            
            $title = DB::query( 'SELECT message FROM language_resource WHERE lang_res_id = ? AND language = ?', $name, $languageid )->fetchSingle();
            $body = DB::query( 'SELECT message FROM language_resource WHERE lang_res_id = ? AND language = ?', $description, $languageid )->fetchSingle();
            
            $paymenttype->setTitle( $title, $language );
            $paymenttype->setBody( $body, $language );
            
         }
         
         $paymenttype->save();
         
         echo "Migrated!\n";
         
      }
      
   }
   
?>