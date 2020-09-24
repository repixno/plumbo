<?PHP
    
    /*******************************************
     *@author Tor Inge <tor.inge@eurofoto.no
     *
     *
     *
     *******************************************/
    
    import( 'pages.admin' );
     

      class AdminStabburetStats extends limitedAdminPage implements IView {
      
      protected $template = 'student.student2020';
        
        
        public function Execute( ) {
			 $from = '2020-03-01';
            $to = '2020-08-31';
			
                
            Util::Debug( $_GET );
			
	
           
            
            if( $_GET['from'] && $_GET['to'] ){
                
                $from = $_GET['from'];
                $to = $_GET['to'];
          
                
               $query =  "SELECT ho.ordrenr, ho.uid, ho.pris, ho.to_production, ho.exported, hk.navn, hk.mphone, hk.epost, hk.adresse1, hk.postnr, hk.sted, ho.kampanje_kode ,ho.kommentar ,ho.kampanje_kode, hps.tidspunkt from historie_ordre ho LEFT JOIN historie_pakke_skjema hps ON ho.ordrenr = hps.ordrenr LEFT JOIN historie_kunde hk ON hk.ordrenr = ho.ordrenr WHERE kampanje_kode = 'STU-SV' AND ho.exported BETWEEN ? AND ?  AND ho.pris!='0.00'  order by ho.ordrenr;";
               $orders = DB::query( $query,  $from, $to )->fetchAll( DB::FETCH_ASSOC );
                
                //$total_orders = DB::query( "SELECT count(*) FROM historie_ordre WHERE kampanje_kode = ? AND deleted IS NULL", $kampanje_kode )->fetchSingle();
                
                //Util::Debug( $orders );
                //die();
                Util::Debug( $orders  );
                $this->stats = $orders;
                
            }
            
          
            $this->from = $from;
            $this->to = $to;
            
            
        }
             
    
      public function XLS(  ) {
         
				if( $_GET['from'] && $_GET['to'] ){
				$from = $_GET['from'];
				$to = $_GET['to'];
				
				/*
				$from = '2017-08-24';
				$to = '2017-08-25';
            */

	
	$query =  "SELECT ho.ordrenr, ho.uid, ho.pris, ho.to_production, ho.exported, hk.navn, hk.mphone, hk.epost, hk.adresse1, hk.postnr, hk.sted, ho.kampanje_kode ,ho.kommentar ,ho.kampanje_kode, hps.tidspunkt from historie_ordre ho LEFT JOIN historie_pakke_skjema hps ON ho.ordrenr = hps.ordrenr LEFT JOIN historie_kunde hk ON hk.ordrenr = ho.ordrenr WHERE kampanje_kode = 'STU-SV' AND ho.exported BETWEEN ? AND ?  AND ho.pris!='0.00'  order by ho.ordrenr;";
   $orders = DB::query( $query,  $from, $to )->fetchAll( DB::FETCH_ASSOC );
				}   

        
         $this->setTemplate( false );
         
         $oldreporting = error_reporting( 0 );
         
         require_once 'Spreadsheet/Excel/Writer.php';
         
// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('student2020.xls');

// Creating a worksheet
$worksheet =& $workbook->addWorksheet('My first worksheet');

// overskrift
$worksheet->write(0, 0, 'Ordrenr');
$worksheet->write(0, 1, 'Navn');
$worksheet->write(0, 2, 'Adresse');
$worksheet->write(0, 3, 'Postnr');
$worksheet->write(0, 4, 'Sted');
$worksheet->write(0, 5, 'Mobil');
$worksheet->write(0, 6, 'Epost');
$worksheet->write(0, 7, 'Bestilt');
$worksheet->write(0, 8, 'Kommentar');
$worksheet->write(0, 9, 'Sendt');

$i=1;
// for å få inn verdiane
foreach( $orders as $order ){
	$i++;
	$worksheet->write($i, 0, $order['ordrenr']);
	$worksheet->write($i, 1, $order['navn']);
	$worksheet->write($i, 2, utf8_decode( $order['adresse1'] ) );
	$worksheet->write($i, 3, utf8_decode($order['postnr']));
	$worksheet->write($i, 4, utf8_decode($order['sted']) );
	$worksheet->write($i, 5, $order['mphone']);
	$worksheet->write($i, 6, utf8_decode($order ['epost'] ) );
	$worksheet->write($i, 7, $order['exported']);
	$worksheet->write($i, 8, utf8_decode( $order['kommentar']) );
   $worksheet->write($i, 9, utf8_decode( $order['tidspunkt']) );
            
}        
    
// Sender fila
$workbook->close();
         error_reporting( $oldreporting );
         
      }
 
        
        
    }
?>
