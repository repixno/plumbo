<?PHP
   
   library( 'campaignmonitor.base' );
   config( 'services.campaignmonitor' );
   
   class CampaignMonitorList {
      
      private $client = null;
      private $listid = null;
      
      public function __construct( $listid = null ) {
         
         $client = Settings::Get( 'campaignmonitor', 'client' );
         $apikey = Settings::Get( 'campaignmonitor', 'apikey' );
         
         $this->listid = $listid;
         $this->client = new CampaignMonitor( $apikey, $client );
         
      }
      
      public function subscriberAddWithCustomFields( $email, $name, $fields ) {
         
         $result = $this->client->subscriberAddAndResubscribeWithCustomFields( $email, $name, $fields, $this->listid );
         return $result['Code'] == 0 ? true : false;
         
      }
      
      public function subscribersGetIsSubscribed( $email ) {
         
         $result = $this->client->subscribersGetIsSubscribed( $email, $this->listid );
         return $result == 'True' || $result['anyType'] == 'True' ? true : false;
         
      }
      
      public function subscriberUnsubscribe( $email ) {
         
         $result = $this->client->subscriberUnsubscribe( $email, $this->listid );
         return $result['Code'] == 0 ? true : false;
         
      }
      
   }
   
?>