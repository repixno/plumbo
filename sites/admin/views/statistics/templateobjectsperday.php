<?PHP
   
   import( 'pages.admin' );
   
   class StatisticsTemplateObjectsPerDay extends AdminPage implements IView {
      
      protected $template = 'statistics.objectsperday';
      protected $query = false;
      protected $modulename = false;
      protected $moduletitle = false;
      
      public function Execute( $year = null, $month = null, $numyears = 2, $offsetdata = false, $includedateintable = false, $portal = false ) {
         
         if( !$this->query ) die();         
         
         $this->selectedportal = $portal;
         
         if( $portal == 0 ){
            $this->query = str_replace( "xxxBYPORTALxxx", "" , $this->query );
         }else{
            
            switch( $portal ){
               case 1:
                  $portalname = "= 'TK-001'";
               break;
               case 2:
                  $portalname = "= 'UP-001'";
               break;
               case 3:
                  $portalname = "= 'DM-001'";
               break;
               case 4:
                  $portalname = "= 'VG-997'";
               break;
               case 5:
                  $portalname = "in ( '' , 'FC-001', 'NT-001', 'AM-997')";
               break;
               case 6:
                  $portalname = "= 'ST-001'";
               break;
               case 7:
                  $portalname = "= 'SM-001'";
               break;
            }
            
            
            switch( $this->modulename ){
               case 'newordersperday':
               case 'averageordertotalperday':
               case 'activeusers':
               case 'activeuserslastyear':   
                  $this->query = str_replace( "xxxBYPORTALxxx", "kampanje_kode " . $portalname . " AND " , $this->query );
               break;
               case 'totalsalesperday':
                  $this->query = str_replace( "xxxBYPORTALxxx", "ho.kampanje_kode " . $portalname . " AND " , $this->query );
               break;
               case 'newusersperday':
                  $this->query = str_replace( "xxxBYPORTALxxx", "kode " . $portalname . " AND " , $this->query );
               break;
            }
            
         }
         
         if( !$year || !$month ) {
            
            if( !$year ) $year = date('Y');
            if( !$month ) $month = date('m');
            
            relocate( '/statistics/%s/%04d/%02d', $this->modulename, $year, $month );
            exit;
            
         }
         
         $month = (int) $month;
         $year = (int) $year;
         
         $this->title = __( $this->moduletitle );
         $this->module = $this->modulename;
         $this->offsetdata = $offsetdata ? true : false;
         
         $years = array();
         for( $addyear = 2001; $addyear <= date('Y'); $addyear++ ) {
            $years[] = $addyear;
         }
         $this->years = $years;
         
         $months = array();
         for( $addmonth = 1; $addmonth <= 12; $addmonth++ ) {
            $timestamp = strtotime( sprintf( '%04d-%02d-01', $year, $addmonth ) );
            if( $timestamp < time() ) {
               $months[] = array(
                  'number' => sprintf( '%02d', $addmonth ),
                  'name' => __( date( 'F', $timestamp ) ),
               );
            }
         }
         $this->months = $months;
         
         $colors = array(
            '#E1B026',
            '#E11632',
            '#0097DB',
            '#0E668E',
            '#7E0E8E',
            '#078E69',
            '#DBD27F',
            '#C3E100',
            '#8E4C0E',
            '#82D2E1',
            '#19592A',
            '#520659',
            '#594D3C',
         );
         
         $weekdays = array(
            1 => __( 'Mon' ),
            2 => __( 'Tue' ),
            3 => __( 'Wed' ),
            4 => __( 'Thu' ),
            5 => __( 'Fri' ),
            6 => str_replace( 'ø', 'o', __( 'Sat' ) ),
            7 => str_replace( 'ø', 'o', __( 'Sun' ) ),
         );
         
         $this->year = sprintf( '%04d', $year );
         $this->month = sprintf( '%02d', $month );
         $this->numyears = $numyears;
         $this->monthdisplay = __( date('F', strtotime( sprintf( '%s-%d-01 00:00:00', $year, $month ) ) ) );
         
         $periodstart = date( 'Y-m-d H:i:s', strtotime( sprintf( '%04d-%02d-01 00:00:00', $year, $month ) ) );
         $periodend = date( 'Y-m-d H:i:s', strtotime( sprintf( '%04d-%02d-%d 23:59:59', $year, $month, date( 't', strtotime( $periodstart ) ) ) ) );
         
         $periods[sprintf('%04d-%02d', $year, $month)] = array( $periodstart, $periodend );
         
         for( $i = 1; $i < $numyears; $i++ ) {
            $periodstart = date( 'Y-m-d H:i:s', strtotime( '-1 year', strtotime( $periodstart ) ) );
            list( $year, $month ) = explode('-', date('Y-m', strtotime( $periodstart ) ) );
            $periodend = date( 'Y-m-d H:i:s', strtotime( sprintf( '%04d-%02d-%d 23:59:59', $year, $month, date( 't', strtotime( $periodstart ) ) ) ) );
            $periods[sprintf('%04d-%02d', $year, $month)] = array( $periodstart, $periodend );
         }
         
         $data = array(); $series = array();
         foreach( $periods as $period => $perioddata ) {
            $firstrun = $roundid++ ? false : true;
            list( $periodstart, $periodend ) = $perioddata;
            
            if( $this->modulename == 'activeusers'){
               $sporring =  DB::query( $this->query, $periodstart, $periodend, $periodstart, $periodend )->fetchAll();
            }else if($this->modulename == 'activeuserslastyear'){
               
               WHILE(  $periodend >= $periodstart ){
                  
                  $nextday = date( 'Y-m-d' , strtotime( $periodstart . '+ 1 day' ) );
                  $moddedquery = str_replace( "xxxFROMxxx" , "'$periodstart'", $this->query );
                  $moddedquery = str_replace( "xxxTOxxx" , "'$nextday'", $moddedquery);
               
                  $periodstart = $nextday;
                  $tmpsporring = DB::query( $moddedquery )->fetchAll();
                  
                  if($tmpsporring ){
                     $sporring[] = $tmpsporring[0]; 
                  }
                  
                      
               }
            }
            else{
               $sporring =  DB::query( $this->query, $periodstart, $periodend )->fetchAll();
            }
            
            foreach( $sporring as $row ) {
               list( $count, $datekey ) = $row;
               $data[$period][substr($datekey, -2)] = round( $count, 2 ).($includedateintable ? ' ('.$datekey.' - '.__( date('D', strtotime($datekey) ) ).')' : '');
               
            }
            
            
            
            if( $firstrun ){
               $countdate = date( 'Y-m-d' , strtotime( $periodstart ) );
               
               $periodendseries = date( 'Y-m-d', strtotime( $periodstart . "+ 1 month" ) );
                              
               While( $countdate <  $periodendseries ){
                     $series[substr($countdate, -2)] = date( 'N', strtotime( $countdate ) );
                     
                     $countdate = date( 'Y-m-d' , strtotime( $countdate . "+ 1 day" )  );
               }
            }
            
            
         }
         
         ksort( $series );
         ksort( $data );
         
         $values = array();
         $timenow = time();
         
         $firstperiod = true;
         
         foreach( $data as $period => $datarows ) {
            
            // filter data by sorted series key to ensure all series have values
            $filtereddata = array();
            
            ksort( $datarows );
            
            if( $offsetdata ) {
               
               $datekey = sprintf( '%02d', key( $datarows ) );
               $weekday = date( 'N', strtotime( sprintf( '%s-%s', $period, $datekey ) ) );
               
               if( $series[$datekey] < $weekday ) {
                  $offset = $weekday - $series[$datekey];
                  if( $offset == 6 ){
                     $offset = 1;
                     for( $i = 0; $i < $offset; $i++ ) {
                        array_shift( $datarows  );
                        array_pop( $datarows );
                     }
                  }else{
                     for( $i = 0; $i < $offset; $i++ ) {
                        array_unshift( $datarows, $newitem = 0 );
                        array_pop( $datarows );
                     }
                  }
                  Util::Debug( $datarows );
                  
               }  elseif( $series[$datekey] > $weekday ) {
                  
                  $offset = $series[$datekey] - $weekday;
                  for( $i = 0; $i < $offset; $i++ ) {
                     array_shift( $datarows );
                  }
                  
               }
            
            }
               
            foreach( $series as $serieskey => $d ) {
               
               if( $offsetdata ) {
                  $filtereddata = $datarows;
               } else {
                  $timestamp = strtotime( sprintf( '%s-%02d 23:59:59', $period, $serieskey ) );
                  //if( $timestamp && $timestamp < $timenow ) {
                     $filtereddata[$serieskey] = (int) $datarows[$serieskey];
                  //}
               }
               
            }
            
            list( $year, $month ) = explode( '-', $period );
            $month = __( date('F', strtotime( sprintf( '%s-%d-01 00:00:00', $year, $month ) ) ) );
            
            // add to output values
            $values[] = array(
               'label' => sprintf( '%s, %04d', $month, $year ),
               'color' => array_shift( $colors ),
               'data' => array_values( $filtereddata ),
            );
            
            $firstperiod = false;
            
         }
         
         if( $offsetdata ) {
            
            $oldseries = $series;
            $series = array();
            
            foreach( $oldseries as $key => $d ) {
               
               $series[] = sprintf( '%s (%02d)', $weekdays[date( 'N', strtotime( sprintf( '%04d-%02d-%02d', $this->year, $this->month, $key ) ) )], $key );
               
            }
            
         } else {
            
            $series = array_keys( $series );
            
         }
         
         // add the series and values
         $this->series = $series;
         $this->values = $values;
         
      }
      
      public function XLS( $year = null, $month = null, $numyears = 2, $offsetdata = false, $includedateintable = false, $portal = false ) {
         
         $this->Execute( $year , $month, $numyears , $offsetdata, $includedateintable, $portal  );
         
         $this->setTemplate( false );
         
         $oldreporting = error_reporting( 0 );
         
         require_once 'Spreadsheet/Excel/Writer.php';
         
         // Creating a workbook
         $workbook = new Spreadsheet_Excel_Writer();
         
         // sending HTTP headers
         $workbook->send( sprintf( '%s-%04d-%02d-%dyears', $this->modulename, $year, $month, $numyears ).'.xls' );
         
         // Creating a worksheet
         $worksheet =& $workbook->addWorksheet( 'Report' );
         
         // write the data
         $fields = array_merge( array( 'Period' ), $this->series );
         $worksheet->writeRow( $row++, 0, $fields );
         
         foreach( $this->values as $graph ) {
            $line = array_merge( array( $graph['label'] ), $graph['data'] );
            $worksheet->writeRow( $row++, 0, $line );
         }
         
         // Let's send the file
         $workbook->close();
         
         error_reporting( $oldreporting );
         
      }
      
   }
   
?>