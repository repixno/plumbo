<?PHP
   
   library( 'campaignmonitor.base' );
   config( 'services.campaignmonitor' );
   
   class CampaignMonitorCampaign {
      
      private $client = null;
      
      public function __construct() {
         
         $apikey = Settings::Get( 'campaignmonitor', 'apikey' );
         $client = Settings::Get( 'campaignmonitor', 'client' );
         
         $this->client = new CampaignMonitor( $apikey, $client );
         
      }
      
      public function getCampaigns() {
         
         $result = $this->client->clientGetCampaigns();
         util::Debug( $result['anyType'] );
         
      }
      
      public function getUnsubscribed( $campaignid = '' ) {
         
         $result = $this->client->campaignGetUnsubscribes( $campaignid );
         echo "Unsubscribed:\n";
         util::Debug( count( $result['anyType']['SubscriberUnsubscribe'] ) );
         
      }
      
      public function GetSubscriberClicks( $campaignid = '' ) {
         
         $result = $this->client->campaignGetSubscriberClicks( $campaignid );
         echo "Clicks:\n";
         util::Debug( $result['anyType'] );
         
      }
      
      public function GetBounces( $campaignid = '' ) {
         
         $result = $this->client->campaignGetBounces( $campaignid );
         echo "Bounces:\n";
         util::Debug( count( $result['anyType']['SubscriberBounce'] ) );
         
      }
      
   }
   
?>