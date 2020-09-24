<?PHP
   
   import( 'pages.admin' );
   import( 'website.user' );
   import( 'website.subscription' );
   
   class ImportSubscriptions extends AdminPage implements IView {
      
      protected $template = 'import.subscriptions.index';
      
      public function Execute() {
         
         
      }
      
      public function upload(){
  

            
         $arrResult = array();
         $handle = fopen($_FILES['list']['tmp_name'], "r");
         if( $handle ) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
               $arrResult[] = $data;
            }
            fclose($handle);
         }

         $currentDate = date("Y-m-d");
         $dateOneYearAdded = strtotime(date("Y-m-d", strtotime($currentDate)) . " +1 year");
         $i = 0;
         
         
         foreach ( $arrResult as $result ){
            $tempsubscription = null;

            if($tempuser = user::fromUsername($result[0])){
               
               $tempsubscription = Subscription::fromUserId($tempuser->uid);
               
               if( $tempsubscription ){
                  
                  if($tempsubscription->valid_to < $dateOneYearAdded){                  
                     //Invalidate all old subscriptions
                     DB::query( "UPDATE subscriptions SET cancelled = ?, active = ? WHERE uid = ?", date( 'Y-m-d H:i:s' ), 0, $tempuser->uid ); 
                  }
               }
               
               // Create and save a new subscription
               $subscription                    = new Subscription();
               $subscription->uid               = $tempuser->uid;
               $subscription->type_subscription = 2;
               $subscription->registered        = date( 'Y-m-d H:i:s' );
               $subscription->start             = date( 'Y-m-d' );
               $subscription->valid_to          = date( 'Y-m-d' ,  $dateOneYearAdded);
               $subscription->active            = 1;
               $subscription->orderid           = $this->orderid;
               $subscription->save();
               
               $i++;
               
            }
            
         }
         
         $this->subcount = $i;
   
      }
         
}
      
      
?>