<?php

   /**
    * API to check users subcription
    *
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'website.subscription' );

   class APIUserCheckSubscription extends JSONPage implements IValidatedView {

      public function Validate() {

         return array();

      }


      /**
       * Check users subcription
       * 
       * @api-result result Boolean true/false
       * @api-result message String yes/no
       * @api-result start Date
       * @api-result stop Date
       * @api-result quarantined_images Integer
       * 
       */ 
      public function Execute() {
         $userid = Login::userid();   
         $subscription = Subscription::staticAsArray( $userid );
         
               
         if( $subscription ){
            $this->result = true;
            $this->start = date('j. F Y', strtotime($subscription['start']));
            $this->stop = date('j. F Y', strtotime($subscription['stop']));
            $this->message = "yes";
            
         }else{
            
              try{
               $quarantined_images = DB::query( "
                  SELECT 
                     count(*)
                  FROM 
                     bildeinfo 
                  WHERE 
                     owner_uid = ? AND
                     quarantined_at IS NOT NULL AND
                     deleted_at IS NULL"
                  , $userid )->fetchSingle();
               }catch( Exception $e ){
                  return null;
               }
                           
            $this->result = false;
            $this->message = "no";
            $this->quarantined_images = $quarantined_images;
         }

      }
      
         

   }

?>
