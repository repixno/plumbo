<?PHP
    
    /*******************************************
     *@author Tor Inge <tor.inge@eurofoto.no
     *
     *
     *
     *******************************************/
    
    import( 'pages.admin' );
     

      class AdminStabburetStats extends limitedAdminPage implements IView {
      
      protected $template = 'stabburet.repixstats';
        
        
        public function Execute( ) {
			 $from = '2018-07-31';
            $to = '2018-11-06';
			
                
            Util::Debug( $_GET );
			
	
           
            
            if( $_GET['from'] && $_GET['to'] ){
                
                $from = $_GET['from'];
                $to = $_GET['to'];
          
                
               $query =  "SELECT * from historie_ordre ho
			   INNER JOIN historie_kunde hk ON ho.ordrenr = hk.ordrenr
			   INNER JOIN historie_ordrelinje hol ON ho.ordrenr = hol.ordrenr
			   INNER JOIN kunde k ON ho.uid = k.uid
			   WHERE ho.kampanje_kode = 'ST-001'
               AND k.newsletter_repix='t'
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

	
	$query =  "SELECT * from  kunde k   INNER JOIN historie_ordre ho ON k.uid = ho.uid INNER JOIN historie_kunde hk ON k.uid = hk.uid  WHERE ho.kampanje_kode = 'ST-001'  AND ho.tidspunkt BETWEEN ? AND ? AND  ho.deleted is null AND k.newsletter_repix='t' order by ho.ordrenr desc";
   $orders = DB::query( $query,  $from, $to )->fetchAll( DB::FETCH_ASSOC );
				}   

        
         $this->setTemplate( false );
         
         $oldreporting = error_reporting( 0 );
         
         require_once 'Spreadsheet/Excel/Writer.php';
         
// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('Repix_ stabburet_2018.xls');

// Creating a worksheet
$worksheet =& $workbook->addWorksheet('My first worksheet');

// overskrift
$worksheet->write(0, 0, 'Navn');
$worksheet->write(0, 1, 'Epost');
$worksheet->write(0, 2, 'Fornavn');
$worksheet->write(0, 3, 'Etternavn');
$worksheet->write(0, 4, 'Telefon');
$worksheet->write(0, 5, 'Adresse');
$worksheet->write(0, 6, 'Postnr');
$worksheet->write(0, 7, 'Stad');





$i=1;
// for å få inn verdiane
foreach( $orders as $order ){
	$i++;

$worksheet->write($i, 0, utf8_decode( $order['namn'] ) );
$worksheet->write($i, 1, utf8_decode($order ['epost'] ) );
$worksheet->write($i, 2, utf8_decode ($order['fnavn']) );
$worksheet->write($i, 3, utf8_decode($order['enavn']));
$worksheet->write($i, 4, utf8_decode($order ['telefon_mobil'] ) );
$worksheet->write($i, 5, utf8_decode($order ['adresse1'] ) );
$worksheet->write($i, 6, utf8_decode($order ['postnr'] ) );
$worksheet->write($i, 7, utf8_decode($order ['stad'] ) );
				




            
}        
    
// Sender fila
$workbook->close();
         error_reporting( $oldreporting );
         
      }
 
        
        
    }
?>
