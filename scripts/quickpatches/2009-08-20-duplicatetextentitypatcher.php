<?PHP
   
exit();
   $dryrun = true;
   
   include "../../bootstrap.php";
   config('website.config');
   
   $oldrecords = array();
   /// $query = "SELECT * FROM site_textentity WHERE id IN (SELECT id FROM (SELECT COUNT(id) as counter, id, type FROM site_textentity GROUP BY id, type) as sub WHERE counter >= '2');";
   // $query = "SELECT * FROM site_delivery_options WHERE id IN (SELECT id FROM (SELECT COUNT(id) as counter, id FROM site_delivery_options GROUP BY id) as sub WHERE counter >= '2');";
   $query = "SELECT * FROM site_payment_options WHERE id IN (SELECT id FROM (SELECT COUNT(id) as counter, id FROM site_payment_options GROUP BY id) as sub WHERE counter >= '2');";
   foreach( DB::query( $query )->fetchAll( DB::FETCH_ASSOC ) as $row ) {
      $oldrecords[$row['id']] = $row;
   }
   
   foreach( $oldrecords as $id => $record ) {
      
      $value = (int) $id;
      /// $query = 'DELETE FROM site_textentity WHERE id = '."'$value'";
      // $query = 'DELETE FROM site_delivery_options WHERE id = '."'$value'";
      $query = 'DELETE FROM site_payment_options WHERE id = '."'$value'";
      
      echo "$query\n";

      if( !$dryrun ) {
         
         DB::query( $query );
         echo "DELETED\n";
      
      }
      
      $values = array();
      foreach( array_values( $record ) as $value ) {
         $values[] = is_null( $value ) ? 'NULL' : "'$value'";
      }
      
      /// $query = 'INSERT INTO site_textentity ("'.implode( '","', array_keys( $record ) ).'") VALUES ('.implode( ",", $values ).')';
      // $query = 'INSERT INTO site_delivery_options ("'.implode( '","', array_keys( $record ) ).'") VALUES ('.implode( ",", $values ).')';
      $query = 'INSERT INTO site_payment_options ("'.implode( '","', array_keys( $record ) ).'") VALUES ('.implode( ",", $values ).')';
      
      echo "$query\n";
      
      if( !$dryrun ) {
         
         DB::query( $query );
         echo "INSERTED\n";
      
      }
      
   }
   
?>