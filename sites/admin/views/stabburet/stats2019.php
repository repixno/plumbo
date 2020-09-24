<?PHP
    
    /*******************************************
     *@author Tor Inge <tor.inge@eurofoto.no
     *
     *
     *
     *******************************************/
    
    import( 'pages.admin' );
     

      class AdminStabburetStats extends limitedAdminPage implements IView {
      
      protected $template = 'stabburet.stats2019';
        
        
        public function Execute( ) {
			 $from = '2019-07-31';
            $to = '2019-08-31';
			
                
            Util::Debug( $_GET );
			
	
           
            
            if( $_GET['from'] && $_GET['to'] ){
                
                $from = $_GET['from'];
                $to = $_GET['to'];
          
                
               $query =  "SELECT * from historie_ordre ho
			   INNER JOIN historie_kunde hk ON ho.ordrenr = hk.ordrenr
			   INNER JOIN historie_mal hm ON ho.ordrenr = hm.ordrenr
			   INNER JOIN historie_ordrelinje hol ON ho.ordrenr = hol.ordrenr
			   INNER JOIN brukar b ON ho.uid = b.uid
			   INNER JOIN kunde k ON ho.uid = k.uid
			   WHERE ho.kampanje_kode = 'ST-001'
			   AND ho.tidspunkt
			   BETWEEN ? AND ?
			   AND  ho.deleted is null
			   ORDER by ho.ordrenr desc";
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

	
	$query =  "SELECT * from historie_ordre ho INNER JOIN historie_kunde hk ON ho.ordrenr = hk.ordrenr INNER JOIN historie_mal hm ON ho.ordrenr = hm.ordrenr INNER JOIN historie_ordrelinje hol ON ho.ordrenr = hol.ordrenr  INNER JOIN brukar b ON ho.uid = b.uid  INNER JOIN kunde k ON ho.uid = k.uid WHERE ho.kampanje_kode = 'ST-001' AND ho.tidspunkt BETWEEN ? AND ? AND  ho.deleted is null order by ho.ordrenr desc";
   $orders = DB::query( $query,  $from, $to )->fetchAll( DB::FETCH_ASSOC );
				}   

        
         $this->setTemplate( false );
         
         $oldreporting = error_reporting( 0 );
         
         require_once 'Spreadsheet/Excel/Writer.php';
         
// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('stabburet2019.xls');

// Creating a worksheet
$worksheet =& $workbook->addWorksheet('My first worksheet');

// overskrift
$worksheet->write(0, 0, 'Antall');
$worksheet->write(0, 1, 'Ordrenr');
$worksheet->write(0, 2, 'Navn');
$worksheet->write(0, 3, 'Adresse');
$worksheet->write(0, 4, 'Postnr');
$worksheet->write(0, 5, 'Sted');
$worksheet->write(0, 6, 'Mobil');
$worksheet->write(0, 7, 'Epost');
$worksheet->write(0, 8, 'Nyhetsbrev Orkla');
$worksheet->write(0, 9, 'Annonser');
$worksheet->write(0, 10, 'Tilpassninger');
$worksheet->write(0, 11, 'Nyhetsbrev Repix');
$worksheet->write(0, 12, 'Registert Dato');
$worksheet->write(0, 13, 'Enhet');
$worksheet->write(0, 14, 'Tidspunkt');
$worksheet->write(0, 15, 'Malid');
$worksheet->write(0, 16, 'Tekst til produkt');
$worksheet->write(0, 17, 'Produkt');

$i=1;
// for å få inn verdiane
foreach( $orders as $order ){
	$i++;
	$worksheet->write($i, 0, $order['antall']);
	$worksheet->write($i, 1, $order['ordrenr']);
	$worksheet->write($i, 2, utf8_decode( $order['namn'] ) );
	$worksheet->write($i, 3, utf8_decode ($order['adresse1']) );
	$worksheet->write($i, 4, utf8_decode($order['postnr']));
	$worksheet->write($i, 5, utf8_decode($order['sted']) );
	$worksheet->write($i, 6, $order['mphone']);
	$worksheet->write($i, 7, utf8_decode($order ['epost'] ) );
	$worksheet->write($i, 8, $order['newsletter_orkla']);
	$worksheet->write($i, 9, $order['stabburet_annonser']);
	$worksheet->write($i, 10, $order['stabburet_tilpassninger']);
	$worksheet->write($i, 11, $order['newsletter_repix']);
	$worksheet->write($i, 12, $order['registrert']);
	$worksheet->write($i, 13, $order['source']);
	$worksheet->write($i, 14, $order['tidspunkt']);
	$worksheet->write($i, 15, $order['malid']);
	$worksheet->write($i, 16, utf8_decode( $order['text']) );
	$worksheet->write($i, 17, utf8_decode( $order['tekst']) );
            
}        
    
// Sender fila
$workbook->close();
         error_reporting( $oldreporting );
         
      }
 
        
        
    }
?>
