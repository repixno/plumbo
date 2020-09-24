<?PHP
   
   import( 'services.visma.base' );
   import( 'pages.admin' );
   import( 'gui.toolkit' );
   
   class SystemVismaInterface extends AdminPage implements IView {
      
      private function cleanNastyNordeaNumber( $number ) {
         
         $number = preg_replace( '/[^0-9,-]*/', '', $number );
         return (float) str_replace( ',', '.', $number );
         
      }
      
      private function cleanNastyNordeaDate( $datefield ) {
         
         list( $date, $time ) = explode( ' ', $datefield );
         list( $day, $month, $year ) = explode( '.', $date );
         
         if( strlen( $tday = $day ) == 4 ) {
            $day = $year;
            $year = $tday;
         }
         
         $day = (int) $day;
         $month = (int) $month;
         $year = (int) $year;
         
         return sprintf( '%04d-%02d-%02d', $year, $month, $day );
         
      }
      
      public function Download() {
         
         if( $_POST['download']['records'] ) {
            
            $this->setTemplate( false );
            
            header( 'Content-Type: text/plain' );
            header( 'Content-Disposition: attachment; filename="'.$_POST['download']['filename'].'"' );
            
            foreach( $_POST['download']['records'] as $row ) {
               
               $record = unserialize( base64_decode( $row ) );
               
               // ORDERNO:   10, left-padded
               // INVOICENO: 10, left-padded
               // AMOUNT:    10, left-padded
               // CURRENCY:   3, left-padded
               // SETTLED:   10, dato-format
               // PURCHASED: 10, dato-format
               // AUTREF:    10, left-padded
               // SHOPREF:   12, left-padded
               // 
               // totalt: 75 char
               
               $values = array();
               if( is_array( $record ) )
               foreach( $record as $key => $val ) {
                  switch( $key ) {
                     
                     case 'currency':
                        $padwidth = 3;
                        break;
                     
                     case 'shopref':
                        $padwidth = 12;
                        break;
                        
                     default:
                        $padwidth = 10;
                        break;
                     
                  }
                  
                  $values[] = str_pad( $val, $padwidth, ' ', STR_PAD_LEFT );
               }
               
               echo implode( '', $values )."\n";
               
            }
            
         } else {
            
            $this->header = 'No data found! :(';
            
         }
         
      }
      
      public function Execute() {
         
         if( isset( $_FILES['upload']['tmp_name']['xmlfile'] ) ) {
            
            $this->header = 'Processing XML';
            
            $xml = simplexml_load_file( $_FILES['upload']['tmp_name']['xmlfile'] );
            
            $batchnumber = $this->cleanNastyNordeaNumber( (string) $xml->Batch->BatchNumber );
            $batchamount = $this->cleanNastyNordeaNumber( (string) $xml->Batch->BatchAmount );
            $createddate = $this->cleanNastyNordeaDate( (string) $xml->Batch->CreatedDate );
            $settlementdate = $this->cleanNastyNordeaDate( (string) $xml->Batch->SettlementDate );
            $merchantnumber = $this->cleanNastyNordeaNumber( (string) $xml->Batch->MerchantNumber );
            $merchantname = (string) $xml->Batch->MerchantName;
            $ownreference = (string) $xml->Batch->OwnReference;
            $currency = (string) $xml->Batch->Currency;
            $transcount = (string) $xml->Batch->NumberOfTransactions;
            
            $form = new Form();
            $form->addHeader( 'Batch' );
            $form->addRaw( 'Number', $batchnumber );
            $form->addRaw( 'Amount', $batchamount );
            $form->addRaw( 'Settlement Date', $settlementdate );
            $form->addSpacer();
            
            $form->addHeader( 'Merchant' );
            $form->addRaw( 'Number', $merchantnumber );
            $form->addRaw( 'Name', $merchantname );
            $form->render();
            
            new text();
            
            $table = new Table();
            $table->AddColumns( array(
               'CardNumber' => array( 'string' ),
               'PurchaseDate' => array( 'datetimedb' ),
               'OrderNo' => array( 'integer' ),
               'Invoice' => array( 'integer' ),
               'Curr.' => array( 'string' ),
               'Amount' => array( 'integer' ),
               'AutRef' => array( 'string' ),
               'ShopRef' => array( 'string' ),
               'Rejected' => array( 'string' ),
            ) );
            
            $form = new form( 'download' );
            
            $vismaJP = new VismaBase('JapanPhotoHoldingNorgeASGLOBALData');
            //$vismaEF = new VismaBase('EurofotoASGLOBALData');
            
            $sum = 0;
            foreach( $xml->Batch->Transactions->Transaction as $transaction ) {
               
               $orderno = (int) $transaction->TransactionReference;
               list( $invoiceno ) = $vismaJP->fetchRow( $vismaJP->query( "SELECT InvoiceNo FROM JapanPhotoHoldingNorgeAS.CustomerOrderAndCopyView WHERE OrderNo = '$orderno' ORDER BY OrderDate DESC" ) );
               /*
               if( !$invoiceno ) {
                  list( $invoiceno ) = $vismaEF->fetchRow( $vismaEF->query( "SELECT InvoiceNo FROM EurofotoAS.CustomerOrderAndCopyView WHERE OrderNo = '$orderno' ORDER BY OrderDate DESC" ) );
               }
               */
               
               $amount = $this->cleanNastyNordeaNumber( (string) $transaction->TransactionAmount );
               $purchasedate = $this->cleanNastyNordeaDate( (string) $transaction->PurchaseDate );
               $autref = (string) $transaction->AutRef;
               $shopref = (string) $transaction->ShopRef;
               $rejected = (string) $transaction->Rejected;
               
               $sum += $amount;
               
               if( $rejected == 'Nei' || $rejected == 'Nej' ) {
                  $form->addHidden( 'records]['.md5( $merchantnumber.$batchnumber.++$rowid ), base64_encode(
                     serialize( array(
                        'orderno'   => $orderno,
                        'invoiceno' => $invoiceno,
                        'amount'    => round( $amount, 2 ) * 100,
                        'currency'  => $currency,
                        'settled'   => $settlementdate,
                        'purchased' => $purchasedate,
                        'autref'    => $autref,
                        'shopref'   => $shopref,
                     ) )
                  ) );
               }
               
               $table->AddRow( array(
                  '****-****-****-'. (string) $transaction->CardNumber,
                  $purchasedate,
                  (string) $orderno,
                  (string) $invoiceno,
                  (string) $transaction->Currency,
                  (string) $transaction->TransactionAmount,
                  $autref,
                  $shopref,
                  $rejected,
               ) );
               
            }
            
            if( round( $sum - $batchamount, 2 ) == 0 || !$rowid ) {
               
               $table->render();
               
               $form->setBackLink( '/system/visma' );
               $form->setBaseLink( '/system/visma/download' );
               
               $form->addHidden( 'filename', util::urlize( sprintf( 'batch %s %s %s.txt', $merchantname, $batchnumber, $settlementdate ) ) );
               
               $form->addSubmit( 'Download', 'Back' );
               
               $form->render();
               
            } else {
               
               new header( 'File seems to be invalid!' );
               
               new text( 'Batch amount does not match given amount!' );
               
            }
            
         } else {
            
            $this->header = 'Upload Visma-XML';
            
            $form = new form( 'upload' );
            $form->addField( 'XML File', 'xmlfile', '', null, 'file' );
            $form->addSubmit( 'Upload' );
            $form->render();
            
         }
         
      }
      
   }
   
?>
