<?PHP
   
   library( 'campaignmonitor.base' );
   config( 'services.campaignmonitor' );
   
   class CampaignMonitorClient {
      
      private $client = null;
      
      public function __construct( $client = null ) {
         
         if( !$client )
         $client = Settings::Get( 'campaignmonitor', 'client' );
         $apikey = Settings::Get( 'campaignmonitor', 'apikey' );
         
         $this->client = new CampaignMonitor( $apikey, $client );
         
      }
      
      public function getClientIdFromPortal( $portal ) {
         foreach( $this->getClientLists() as $clientid => $clientname ) {
            if( preg_match( '/(.*)\('.$portal.'\)/', $clientname ) ) {
               return $clientid;
            }
         }
         return false;
      }
      
      public function getClientLists( $client = null ) {
         
         $result = $this->client->clientGetLists( $client );
         $results = array();
         
         if( isset( $result['anyType']['List'] ) )
         foreach( $result['anyType']['List'] as $record ) {
            $results[$record['ListID']] = $record['Name'];
         }
         
         return $results;
         
      }
      
      
   }
   
?>