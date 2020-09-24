<?php

   /**
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'user.discountcampaign' );
   model( 'site.discountquantum' );

   class CartDiscount {
      
      
      /**
       * Get any valid discounts activated and 
       * containing current refid.
       *
       * @param integer $userid
       * @param integer $refid
       * @return array
       */
      static function getActivatedDiscounts( $userid = 0, $refid = 0 ) {
         
         $dhs = new DiscountHistory();
         $discount = array();
         $now = date( 'Y-m-d H:i:s' );
         
         foreach( $dhs->collection( array( 'id' ), array( 'user_id' => Login::userId(), 'type' => 2, 'activated' => array( 'IS', 'NOT NULL' ), 'used' => array( 'IS', 'NULL' ) ), 'activated DESC' )->fetchAllAs( 'DiscountHistory' ) as $dh ) {
            
            if( $dh->isCampaignPortal( $dh->discount_campaign_id, Dispatcher::getCustomAttr( 'portalid' ) ) ) {
               
               $quantum = DBDiscountQuantum::fromCampaignIdAndrefId( $dh->discount_campaign_id, $refid );
               if( is_array( $quantum ) && count( $quantum ) > 0 ) {
                  $tmp['history'] = $dh->asArray();   
                  $tmp['quantum'] = $quantum;
                  $campaign = new DBUserDiscountCampaign( $dh->discount_campaign_id );
                  $tmp['campaign'] = $campaign->asArray();
               
                  if( $tmp['campaign']['start'] < $now && $tmp['campaign']['stop'] > $now ) {
                     $discount []= $tmp;
                  }
                  unset( $tmp );
               }
            }
            
         }
         
         return $discount;
         
      }
      
      
      
      static function getLatestActivatedDiscount() {
         
         $dhs = new DiscountHistory();
         $discount = array();
         $now = date( 'Y-m-d H:i:s' );
         
         foreach( $dhs->collection( array( 'id' ), array( 'user_id' => Login::userId(), 'type' => 2, 'activated' => array( 'IS', 'NOT NULL' ), 'used' => array( 'IS', 'NULL' ) ), 'activated DESC', 1 )->fetchAllAs( 'DiscountHistory' ) as $dh ) {
            
            if( $dh->isCampaignPortal( $dh->discount_campaign_id, Dispatcher::getCustomAttr( 'portalid' ) ) ) {
          
               $quantum = DBDiscountQuantum::fromCampaignId( $dh->discount_campaign_id );
               
               if( is_array( $quantum ) && count( $quantum ) > 0 ) {
                  
                  return $quantum;
                  
               }
               
            }
            
         }
         
      }
      
      
      
      
      /**
       * Get the discount unit price
       *
       * @param array $discounts
       * @param integer $quantity
       * @param float $unitPrice
       * @return float
       */
      static function unitPrice( $discounts, $quantity, $unitPrice ) {

         if( is_array( $discounts ) && count( $discounts ) > 0 ) {
            $currDiscount = self::getDiscount( $quantity, $discounts );

            if( $currDiscount > 0 ) {
               
               $discountAmount = ( $unitPrice * $currDiscount );
               $unitPrice = round( $unitPrice - $discountAmount, 2);
               return array(
                  'unitprice' => $unitPrice,
                  'unitdiscount' => $discountAmount,
                  'changed' => true,
               );
            }
         }
         
         return array(
            'unitprice' => $unitPrice,
            'changed' => false,
         );
         
      }
      
      
      
      /**
       * Get the discount based on quantity and 
       * quantum for product
       *
       * @param integer $quantity
       * @param array $discount
       * @return float
       */
      static function getDiscount( $quantity, $discount ) {
         
         $newDiscount = 0;
         $discount = $discount[0];
         
         if( isset( $discount['quantum'] ) ) {
            
            $quantums = $discount['quantum'];
            
            foreach( $quantums as $quantumData ) {
               if( $quantumData['min'] < $quantity ) {
                  $newDiscount = $quantumData['discount'];
               } else {
                  return $newDiscount;
               }
               
            }
            
         }
         
         return $newDiscount;
         
      }
      
      
      
      /**
       * Get the correct quantum based on quantity
       *
       * @param integer $quantity
       * @param array $quantums
       * @return float
       */
      static function evalQuantum( $quantity, $quantums ) {

         $newdiscount = 0;
         foreach( $quantums as $quantumdata ) {
            
            if( $quantumdata['min'] <= $quantity ) {

               $newdiscount = $quantumdata['discount'];
               
            }
               
         }
         
         return $newdiscount;
         
      }
      
   }


?>