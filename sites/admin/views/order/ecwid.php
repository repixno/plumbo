<?PHP
    
    /*******************************************
     *@author Tor Inge <tor.inge@eurofoto.no
     *
     *
     *
     *******************************************/
    
    import( 'pages.admin' );
     
    include( 'Spreadsheet/Excel/Writer.php' );
    class AdminPortalStatistics extends AdminPage implements IView {
        protected $template = 'order.ecwid';
        
        
        public function Execute( ) {
            
            
            //Util::Debug( $_GET );
            
             $kode = 'ECWID';
            $from = '2020-09-01';
            $to = '2020-12-31';
           
            
            if( $_GET['from'] && $_GET['to'] ){
                
              
                $from = $_GET['from'];
                $to = $_GET['to'];
                
               // $query = "select * from brukar b, kunde k,historie_ordre ho where k.kode ?  AND ho.tidspunkt BETWEEN '2017-06-01' AND '2017-01-01' AND deleted is null";
                
                
               $query =  "SELECT * from historie_ordre ho INNER JOIN historie_kunde hk ON ho.ordrenr = hk.ordrenr  INNER JOIN kunde k ON ho.uid = k.uid WHERE ho.kampanje_kode = ? AND ho.tidspunkt BETWEEN ? AND ? AND  ho.deleted is null order by ho.ordrenr desc";
               
               
               //INNER JOIN historie_mal hm ON ho.ordrenr = hm.ordrenr
              
                $orders = DB::query( $query, $kode,  $from, $to )->fetchAll( DB::FETCH_ASSOC );
                
                //$total_orders = DB::query( "SELECT count(*) FROM historie_ordre WHERE kampanje_kode = ? AND deleted IS NULL", $kampanje_kode )->fetchSingle();
                
                //Util::Debug( $orders );
                //die();
                Util::Debug( $orders  );
                $this->stats = $orders;
                
            }
            
            $this->kode = $kode;
            $this->from = $from;
            $this->to = $to;
            
            
        }
        
        
        
        
         public function XLS(  ) {
         
         $this->Execute( );
         
         $this->setTemplate( false );
         
         $oldreporting = error_reporting( 0 );
         
         require_once 'Spreadsheet/Excel/Writer.php';
         
        // Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send('test.xls');

// Creating a worksheet
$worksheet =& $workbook->addWorksheet('My first worksheet');

// The actual data
$worksheet->write(0, 0, 'Name');
$worksheet->write(0, 1, 'Age');
$worksheet->write(1, 0, 'John Smith');
$worksheet->write(1, 1, 30);
$worksheet->write(2, 0, 'Johann Schmidt');
$worksheet->write(2, 1, 31);
$worksheet->write(3, 0, 'Juan Herrera');
$worksheet->write(3, 1, 32);

// Let's send the file
$workbook->close();
      }
      
      
      
      
        
        
        
    }
?>