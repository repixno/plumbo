<?PHP
   
   library( 'campaignmonitor.base' );
   config( 'services.campaignmonitor' );
   
   class CampaignMonitorUser {
      
      private $client = null;
      
      public function __construct() {
         
         $apikey = Settings::Get( 'campaignmonitor', 'apikey' );
         $client = Settings::Get( 'campaignmonitor', 'client' );
         
         $this->client = new CampaignMonitor( $apikey, $client );
         
      }
      
      public function getClients() {
         
         $result = $this->client->userGetClients();
         util::Debug( $result['anyType'] );
         
      }
      
   }
   
?>