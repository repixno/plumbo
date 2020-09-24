<?php

   /**
    * Payed Storage subscription class
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'user.subscription' );
   
   class Subscription extends DBSubscription {
      
      
      /**
       * Create a subscription calculation from userid
       *
       * @param integer $userid
       * @return array
       */
      static function fromUserId( $userid = 0 ) {
         
         if( $userid == 0){
            $userid = Login::userid();
         }
         
         try{
            
            $res = DB::query( "
               SELECT 
                  id
               FROM 
                  subscriptions 
               WHERE 
                  uid = ? AND
                  active=1 AND
                  valid_to > ? 
               LIMIT 1
            ", $userid, date( 'Y-m-d' ) );
            
            
            if( $res->count() ) {
               
               list( $id ) = $res->fetchRow();
               $subscription = new Subscription( $id );
               return $subscription;
               
            } else {
               
               return false;
               
            }
         
         } catch( Exception $e ) {
            
            echo $e->getMessage();;
            die();

            return false;
            
         }
         
      }
      
      
      /**
       * Return an array representation of a subscription
       *
       * @return unknown
       */
      public function status() {

         $purchaseStop = $this->latestOrderValidTo();
         
         if( isset( $purchaseStop ) ) {
            
            if( $purchaseStop > $this->validto ) {
               $stop = $purchaseStop;
               $type = "order";
            } else {
               $stop = $this->valid_to;
               $type = "subscription";
            }
            
         } else {
            $stop = $this->validto;
            $type = "subscription";
         }
         
         return array(
            "type" => $type,
            "start" => $this->registered,
            "stop" => $stop,
         );

      }

      static function staticAsArray( $userid ) {
         
         $result = null;
         
         try {
            
            if( $subscription = Subscription::fromUserId( $userid ) ) {
               if( $subscription->isLoaded() && $subscription instanceof Subscription ) {
                  $result = $subscription->status();
               } else {
                  throw new Exception( 'No active subscription' );
               }
            } else {
               throw new Exception( 'No active subscription' );
            }
            
         } catch( Exception $e ) {
            
            if( $expires = Subscription::latestOrderValidToByUserId( $userid ) ) {
               
               $expiresunix = strtotime( $expires );
               
               if( $expiresunix > time() ) {
               
                  $result = array(
                     "type" => 'order',
                     "start" => date( 'Y-m-d', strtotime( '- 12 MONTH', $expiresunix ) ),
                     "stop" => $expires,
                  );
                  
               }
               
            }
            
         }
         
         
         
         if( is_array( $result ) ) {
            
            // if its less than 90 days left, allow purchase of more storage
            if( ( strtotime( $result['stop'] ) - time() ) < ( 86400 * 90 ) ) {
               $result['buymore'] = true;
            }
            
            return $result;
            
         } else {
            
            return null;
            
         }
         
      }

      /**
       * A purchase rewards with free storage for 12 months
       * Calculate how long that is
       * 
       * @return timestamp
       * 
       */
      private function latestOrderValidTo() {

         return self::latestOrderValidToByUserId( Login::userid() );

      }
      
      static function latestOrderValidToByUserId( $userid ) {
         
         $refids = Subscription::subscriptionRefIds();
         $refstring = implode( ',', $refids );
         
         $res = DB::query( "
            SELECT 
               ho.tidspunkt
            FROM historie_ordre ho 
            LEFT JOIN historie_ordrelinje AS hol ON hol.ordrenr = ho.ordrenr 
            WHERE ho.uid = ? AND
            ho.deleted IS NULL AND 
            hol.artikkelnr NOT IN( $refstring ) 
            ORDER BY ho.tidspunkt DESC
            LIMIT 1
         ", $userid );
         
         if( $res->count() ) {

            list( $time ) = $res->fetchRow();

            if( isset( $time ) ) {

               return date( 'Y-m-d', strtotime( '+ 12 MONTH', strtotime( $time ) ) );
               
            }

         }

         return null;
         
      }


      /**
       * Get refids of all subscription types
       * 
       * @return array
       */
      static function subscriptionRefIds() {

         $res = DB::query( "
         SELECT 
            product_id 
         FROM 
            subscription_types
      " );


         $refids = array();

         while( list( $refid ) = $res->fetchRow() ) {

            if( isset( $refid ) ) {
               $refids []= $refid;
            }

         }

         return $refids;

      }
      
   }
   
   

?>