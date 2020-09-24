<?PHP
   
   import( 'pages.protected' );
   import( 'website.product' );
   
   class StatisticsApressenProductSales extends ProtectedPage implements IView {
      
      protected $template = 'partner.apressen.querydata';
      
      public function Execute() {
         
         $options = array();
         $artnrs = DB::query( 'SELECT DISTINCT(hol.artikkelnr) FROM historie_ordrelinje hol LEFT JOIN historie_ordre ho ON ho.ordrenr = hol.ordrenr WHERE ho.kampanje_kode = ?', 'AP-997' )->fetchAll();
         foreach( $artnrs as $row ) {
            
            list( $artnr ) = $row;
            $option = ProductOption::fromRefId( $artnr );
            // if( !$option->hasTag( 'correctionmethod' ) && !$option->hasTag('productionmethod') ) {
               $product = new Product( $option->productid );
               $options[] = array(
                  'prodno' => $artnr,
                  'title' => sprintf( '%04d. %s, %s', $artnr, $product->title, $option->title ),
               );
            // }
            
         }
         
         $returnurls = DB::query( 'SELECT DISTINCT(sup.value) FROM historie_ordre ho LEFT JOIN site_user_preferences sup ON sup.key = ? AND sup.userid = ho.uid WHERE ho.kampanje_kode = ?', 'apressen_returnurl', 'AP-997' )->fetchAll();
         foreach( $returnurls as $id => $row ) {
            
            list( $url ) = $row;
            $parsedurl = parse_url( $url );
            $hosts[$parsedurl['host']] = $id;
            
         }
         
         $this->options = $options;
         $this->hosts = array_values( array_flip( $hosts ) );
         $this->periodstart = date( 'Y-m-01' );
         $this->periodend = date( 'Y-m-t' );
         
      }
      
      public function Report() {
         
         if( !count( $_POST['options'] ) || !count( $_POST['hosts'] ) ) {
            
            relocate( '/partner/apressen/productsales' ); die();
            
         }
         
         $articlelist = implode( ',', $_POST['options'] );
         $hostsselect = "sup.value LIKE 'http://".implode( "%' OR sup.value LIKE 'http://", $_POST['hosts'] )."%'";
         
         $query = DB::query( 'SELECT SUM(hol.pris*hol.antall) as count, 
                                     hol.artikkelnr,
                                     date(ho.tidspunkt) as date 
                                 FROM historie_ordrelinje hol
                              LEFT JOIN historie_ordre ho ON ho.ordrenr=hol.ordrenr
                              LEFT JOIN site_user_preferences sup ON sup.key = ? AND sup.userid = ho.uid 
                              WHERE ho.tidspunkt BETWEEN ? AND ? 
                                 AND ('.$hostsselect.')
                                 AND hol.artikkelnr IN ('.$articlelist.')
                           GROUP BY date(ho.tidspunkt), hol.artikkelnr', 
                           
                           'apressen_returnurl', 
                           $_POST['periodstart'].' 00:00:00', 
                           $_POST['periodend'].' 23:59:59' );
                           
         $unixendtime = strtotime( $_POST['periodend'] );
         $unixstarttime = strtotime( $_POST['periodstart'] );
         $dates = array( date( 'Y-m-d', $unixstarttime ) => array() );
         $i = 0; while( $unixstarttime < $unixendtime ) {
            $unixstarttime = strtotime( date( 'Y-m-d', strtotime( '+1 day', $unixstarttime ) ) );
            $dates[date( 'Y-m-d', $unixstarttime )] = array();
            if( $i++ > 1000 ) break;
         }
         
         while( list( $sum, $prodno, $date ) = $query->fetchRow() ) {
            $data[$prodno][$date] = $sum;
         }
         
         if( count( $data ) ) {
            $prodnos = array_keys( $data );
            sort( $prodnos );
         } else {
            $prodnos = array();
         }
         $output = array(
            'dates' => array(),
            'prodnos' => array(),
            'total' => 0.00,
         );
         $sums['prodnos'] = array();
         
         foreach( $prodnos as $prodno ) {
            $sums['prodnos'][sprintf( '%04d', $prodno)] = '-';
         }
         foreach( array_keys( $dates ) as $date ) {
            $output['dates'][$date]['prodnos'] = array();
            $output['dates'][$date]['date'] = $date;
            $output['dates'][$date]['sum'] = '-';
         }
         
         foreach( $dates as $date => $d ) {
            foreach( $prodnos as $prodno ) {
               $output['dates'][$date]['prodnos'][sprintf( '%04d', $prodno)] = '-';
            }
         }
         
         if( count( $data ) )
         foreach( $data as $prodno => $subdata ) {
            foreach( $subdata as $date => $sum ) {
               $output['dates'][$date]['prodnos'][sprintf( '%04d', $prodno)] = $sum;
               $output['dates'][$date]['sum'] += $sum;
               $output['total'] += $sum;
               $sums['prodnos'][sprintf( '%04d', $prodno)] += $sum;
            }
         }
         
         foreach( $sums['prodnos'] as $prodno => $sum ) {
            
            $option = ProductOption::fromRefId( $prodno );
            $product = new Product( $option->productid );
            $output['prodnos'][$prodno] = array(
               'prodno' => $prodno,
               'title' => sprintf( '%s, %s', $product->title, $option->title ),
               'sum' => $sum,
            );
         }
         
         $this->output = $output;
         $this->periodstart = $_POST['periodstart'];
         $this->periodend = $_POST['periodend'];
         $this->setTemplate( 'partner.apressen.report' );
         
      }
      
   }
   
?>