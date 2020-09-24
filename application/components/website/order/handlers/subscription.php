<?php

   /**
    * 
    * Handle products tagged as goods.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'website.order.handlers.base' );
   import( 'website.subscription' );
   import( 'website.subscriptiontype' );
   
   class OrderHandlerSubscription extends OrderHandlerBase {
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::process( $order, $type, $item );
         
         if( $this->parseItem( $item ) ) {
            $this->finalize();
         }
      }
      
      /**
       * Parse the subscription item and update correct db tables
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function parseItem( $item ) {

         // What's the status of previous subscription stop?
         // Is it an order or a subscription? What's the expire date?
         $status = Subscription::staticAsArray( Login::userid() );
         if( !empty( $status ) ) {
            $subscriptionstop = $status['stop'];
         }
         
         $subtype = SubscriptionType::fromRefId( $item['refid'] );
         
         $subtype->isLoaded();
         
         if( $subtype->isLoaded() ) {

            // If there is a previous expire date than subscription should start after that.
            // Calculate the new subscription expire date.
            if( isset( $subscriptionstop ) ) {
               $validfrom = $subscriptionstop;
               $validto = date( 'Y-m-d', strtotime( "$subscriptionstop".sprintf( '+%d MONTH', $subtype->months ).sprintf( "+%d DAY", $subtype->days ) ) );
            } else {
               $validfrom = $this->today;
               $validto = date( 'Y-m-d', strtotime( "$this->today".sprintf( '+%d MONTH', $subtype->months ).sprintf( "+%d DAY", $subtype->days ) ) );
            }
            
            // Invalidate all old subscriptions
            DB::query( "UPDATE subscriptions SET cancelled = ?, active = ? WHERE uid = ?", date( 'Y-m-d H:i:s' ), 0, Login::userid() );

            // Create and save a new subscription
            $subscription                    = new Subscription();
            $subscription->uid               = Login::userid();
            $subscription->type_subscription = $subtype->id;
            $subscription->registered        = date( 'Y-m-d H:i:s' );
            $subscription->start             = $validfrom;
            $subscription->valid_to          = $validto;
            $subscription->active            = 1;
            $subscription->orderid           = $this->orderid;
            
            $subscription->save();            
            
            return true;
         }
         
         return false;
         
      }
      
   }



?>