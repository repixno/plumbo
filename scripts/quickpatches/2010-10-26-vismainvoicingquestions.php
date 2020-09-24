<?PHP

   include "../../bootstrap.php";
   config('website.config');
   
   import( 'services.visma.base' );

   echo "ORDERDATE\tPRODUCTIONDATE\tCUSTOMERNO\tINVOICENO\tORDERNO\tUID\tAMOUNT\tRESTAMOUNT\tPERIOD\tINVOICEDATE\tENTRYDATE\n";
   
   $vismaJP = new VismaBase('JapanPhotoHoldingNorgeASGLOBALData');
   $vismaEF = new VismaBase('EurofotoASGLOBALData');
   $query = $vismaJP->query( "SELECT VoucherNo, CustomerNo, GLAccRec, Period, Amount, RestAmount, VoucherDate, CollectionCode, CollectionDate, Created, EntryDate FROM JapanPhotoHoldingNorgeAS.DebLTransaction WHERE VoucherNo > '824020' AND VoucherNo < '850001' ORDER BY VoucherNo" );
   while( list( $invoiceno, $customerno, $glaccrec, $period, $amount, $restamount, $invoicedate, $collectioncode, $collectiondate, $created, $entrydate ) = $vismaJP->fetchRow( $query ) ) {
      
      $sub = $vismaEF->query( "SELECT OrderNo FROM EurofotoAS.CustomerOrderAndCopyView WHERE InvoiceNo = '$invoiceno' ORDER BY OrderDate DESC" );
      list( $orderno ) = $vismaEF->fetchRow( $sub );
      
      if( $orderno < 1000000 ) continue;
      
      list( $uid, $orderdate, $productiondate ) = DB::query( 'SELECT uid, tidspunkt, to_production FROM historie_ordre WHERE ordrenr = ?', $orderno )->fetchRow();
      
      if( strtotime( $orderdate ) >= strtotime( '2010-08-31 00:00:00' ) ) continue;
      
      $invoicedate = date( 'Y-m-d', $vismaJP->UnixTimeStamp( $invoicedate ) );
      $collectiondate = date( 'Y-m-d', $vismaJP->UnixTimeStamp( $collectiondate ) );
      $created = date( 'Y-m-d', $vismaJP->UnixTimeStamp( $created ) );
      $entrydate = date( 'Y-m-d', $vismaJP->UnixTimeStamp( $entrydate ) );
      $orderdate = date( 'Y-m-d', strtotime( $orderdate ) );
      $productiondate = date( 'Y-m-d', strtotime( $productiondate ) );
      
      $amount = number_format( $amount, 2, ',', '' );
      $restamount = number_format( $restamount, 2, ',', '' );
      
      echo "$orderdate\t$productiondate\t$customerno\t$invoiceno\t$orderno\t$uid\t$amount\t$restamount\t$period\t$invoicedate\t$entrydate\n";
      
   }
   
?>