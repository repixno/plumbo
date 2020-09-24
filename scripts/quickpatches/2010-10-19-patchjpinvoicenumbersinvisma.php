<?PHP

   die();

   include "../../bootstrap.php";
   config('website.config');
   
   import( 'math.checksum' );
   import( 'services.visma.base' );
   
   echo "CUSTOMERNO\tINVOICENO\tKID\tNEWKID\tAMOUNT\tRESTAMOUNT\n";
   $visma = new VismaBase('JapanPhotoHoldingNorgeASGLOBALData');
   $query = $visma->query( "SELECT CustomerNo, VoucherNo, Kid, Amount, RestAmount FROM JapanPhotoHoldingNorgeAS.DebLTransaction WHERE VoucherNo > '824020' AND VoucherNo < '829731' AND RestAmount > 0 ORDER BY VoucherNo" );
   while( list( $customerno, $invoiceno, $kid, $amount, $restamount ) = $visma->fetchRow( $query ) ) {
      $newkid = sprintf( '%06d%07d', $customerno, $invoiceno );
      $newkid = sprintf( '%s%d', $newkid, Checksum::mod10( $newkid ) );
      echo "$customerno\t$invoiceno\t$kid\t$newkid\t$amount\t$restamount\n";
      #echo "UPDATE JapanPhotoHoldingNorgeAS.DebLTransaction SET Kid = '$newkid' WHERE Kid = '$kid' AND VoucherNo > '824020' AND VoucherNo < '829731'\n";
      $visma->query( "UPDATE JapanPhotoHoldingNorgeAS.DebLTransaction SET Kid = '$newkid' WHERE Kid = '$kid' AND VoucherNo > '824020' AND VoucherNo < '829731'" );
      $numpatched++;
      /*
      $subquery = $visma->query( "SELECT VoucherNo, CustomerNo FROM JapanPhotoHoldingNorgeAS.DebLTransaction WHERE Kid = '$kid' AND VoucherNo > '824020' AND VoucherNo < '829731'" );
      while( list( $invoiceno, $customerno ) = $visma->fetchRow( $subquery ) ) {
         echo "$invoiceno,$customerno\n";
      }
      */
      #die();
   }
   
   echo "Updated $numpatched records\n";
   
?>