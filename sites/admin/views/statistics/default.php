<?PHP
   
   import( 'pages.admin' );
   
   class StatisticsTemplateObjectsPerDay extends AdminPage implements IView {
      
      protected $template = 'statistics.objectsperday';
      protected $query = false;
      protected $modulename = false;
      protected $moduletitle = 'Statistics';
      
      public function Execute( $year = null, $month = null, $numyears = 2, $offsetdata = false ) {
         
         if( !$year || !$month ) {
            
            if( !$year ) $year = date('Y');
            if( !$month ) $month = date('m');
            
            relocate( '/statistics/%04d/%02d', $year, $month );
            exit;
            
         }
         
         $month = (int) $month;
         $year = (int) $year;
         
         $this->title = __( $this->moduletitle );
         $this->module = '';
         $this->offsetdata = $offsetdata ? true : false;
         
         $this->year = sprintf( '%04d', $year );
         $this->month = sprintf( '%02d', $month );
         $this->numyears = $numyears;
         $this->monthdisplay = __( date('F', strtotime( sprintf( '%s-%d-01 00:00:00', $year, $month ) ) ) );
         
      }
      
   }
   
?>