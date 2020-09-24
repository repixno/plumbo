<?PHP
   
   import( 'pages.admin' );
   
   class DebugMonitor extends AdminPage implements IView {
      
      protected $template = 'debug.monitor';
      
      public function Execute( $identifier = null ) {
         
         $identstring = $identifier ? $identifier : Debug::uniqueIdentifier();
         Debug::Write( sprintf( 'Connected to %s', $identstring  ), $identstring );
         $this->identifier = $identifier;
         
      }
      
      public function fetchEvents( $identifier = null ) {
         
         $this->setTemplate( false );
         header( 'content-type: text/x-json' );
         
         $records = array();
         $events = Debug::Read( $identifier );
         if( is_array( $events ) )
         foreach( $events as $record ) {
            list( $occured, $data ) = $record;
            $records[] = array(
               'occured' => date( 'Y-m-d H:i:s', round( $occured ) ),
               'data' => print_r( $data, true ),
            );
         }
         
         echo json_encode( $records );
         
      }
      
   }
   
?>