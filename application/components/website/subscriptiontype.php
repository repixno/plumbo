<?php

   model( 'subscription.subscriptiontypes' );
   
   class SubscriptionType extends DBSubscriptionTypes {
      
      
      
      static function fromRefId( $refid  = 0 ){
         
         $subscriptions = Model::fromFieldValue( array( 'product_id' => $refid ), 'DBSubscriptionTypes' );
         
         return $subscriptions;
         
      }
      
      
 
      
   }
   
   

?>