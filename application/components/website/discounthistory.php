<?php

   model( 'user.discounthistory' );
   import( 'website.discountcampaign' );

   class DiscountHistory extends DBUserDiscountHistory {


      public function asArray() {

         return array(
            'id' => $this->id,
            'userid' => $this->user_id,
            'campaingid' => $this->campaignid,
            'code' => $this->code,
            'used' => $this->used,
            'orderid' => $this->ordernr,
            'amount' => $this->amount,
            'type' => $this->type,
            'activated' => $this->activated,
         );

      }


      public function isActiveMultiple( $code ) {

         $currenttime = date( 'Y-m-d H:i:s' );
         $res = DB::query( "SELECT id FROM discount_campaigns WHERE type=2 AND code=? AND active=1 AND stop>? AND one_time=0", $code, $currenttime );
         $id = $res->fetchRow();
         
         if( count( $id ) > 0 ) {
            $id = (int) $id[0];
         } else {
            $id = 0;
         }

         if( $id > 0 ) {
            return $id;
         }
         
         return false;
         

      }

      /*public function isActiveUnique( $code, $uid ) {

         $currenttime = date( 'Y-m-d H:i:s' );
         $ids = DB::query( "SELECT discount_campaign_id as id FROM discount_campaign_history WHERE user_id=? AND code=? AND activated IS NULL AND used IS NULL", $uid, $code )->fetchAll();

         if ( count( $ids ) == 1 ) {

            $id = $ids[ 0 ][ 'id' ];

            if ( DB::query( "SELECT count(*) FROM discount_campaigns WHERE type=3 AND id=? AND active=1 AND stop>? AND one_time=1", $id, $currenttime )->fetchSingle() ) {

               return true;

            }

         }
         

         return false;

      }*/

      
      public function isUniqueActive( $code, $uid ) {
         
         $currenttime = date( 'Y-m-d H:i:s' );
         $res = DB::query( "SELECT dc.id FROM discount_campaigns AS dc WHERE dc.type=2 AND dc.code=? AND dc.active=1 AND dc.stop>? AND dc.one_time=1", $code, $currenttime );
         
         if( $res->count() > 0 ) {

            $id = $res->fetchRow();
            if( count( $id ) > 0 ) {
               
               $id = (int) $id[0];
               return $id;
               
            }
            
         }
         
         return false;
         
      }
      
      
      public function activateMultiple( $code, $uid ) {

         $campaign = $this->fetchCampaign( 2, $code );

         if ( is_array( $campaign ) ) {

            $currenttime = date( 'Y-m-d H:i:s' );
            $discounttype = 2;
            $dcid = $campaign[0]->id;
            $dcuid = $uid;
            $dccode = $code;
            $activated = $currenttime;
            $portalid = Dispatcher::getPortalId();
            $portalid = empty( $portalid ) ? 0 : $portalid;

            /* If user hasn't activated discount before then do it now if it exists */
            $res = DB::query( "SELECT id, activated FROM discount_campaign_history WHERE discount_campaign_id=? AND user_id=? AND used IS NULL ORDER BY id desc LIMIT 1", $dcid, $dcuid );
            
            list( $rowid, $prevactivated ) = $res->fetchRow();
            $rowid = (int) $rowid;
            
            if( $rowid == 0 && $this->isCampaignPortal( $dcid, $portalid ) && empty( $prevactivated )  ) {

               $newDiscount = new DBUserDiscountHistory();
               $newDiscount->campaignid = $dcid;
               $newDiscount->userid = $dcuid;
               $newDiscount->used = 0;
               $newDiscount->code = $dccode;
               $newDiscount->activated = $activated;
               $newDiscount->type = 2;
               $newDiscount->save();
               return true;

            } else if( $rowid > 0 && $this->isCampaignPortal( $dcid, $portalid ) ) { // Found old activated code  //&& empty( $activated ) ) {
               
               return true;
               
            }

         }

         return false;

      }

      public function activateUnique( $code, $uid, $id ) {

         $res = DB::query( 'SELECT id, used, activated FROM discount_campaign_history WHERE discount_campaign_id=? AND user_id=? ORDER BY id DESC LIMIT 1', $id, $uid );
         if( $res->count() > 0 ) {
            
            list( $rowid, $used, $activated ) = $res->fetchRow();
            
            if( !empty( $used ) ) {
               return false;
            }
            
            if( isset( $activated ) && !empty( $activated ) ) {
               
               return true;
               
            } else {
               
               DB::query( 'UPDATE discount_campaign_history SET activated=? WHERE id=? AND user_id=? AND discount_campaign_id=?', date( 'Y-m-d H:i:s' ), $rowid, $uid, $id );
               return true;
               
            }
            
         } else {
            
            $campaign = $this->fetchCampaign( 2, $code );
            if ( is_array( $campaign ) ) {

               $currenttime = date( 'Y-m-d H:i:s' );
               $discounttype = 2;
               $dcid = $campaign[0]->id;
               $dcuid = Login::userId();
               $dccode = $code;
               $activated = $currenttime;
               $portalid = Dispatcher::getPortalId();
               $portalid = empty( $portalid ) ? 0 : $portalid;
               
               if( $this->isCampaignPortal( $dcid, $portalid ) ) {

                  $newDiscount = new DBUserDiscountHistory();
                  $newDiscount->campaignid = $dcid;
                  $newDiscount->userid = $dcuid;
                  $newDiscount->used = 0;
                  $newDiscount->code = $dccode;
                  $newDiscount->activated = $activated;
                  $newDiscount->type = 2;
                  $newDiscount->save();
                  
               }
               
               return true;
            }
            return false;
         }
      
      }
      
      
      private function fetchCampaign( $type = 0, $code = '' ) {

         if ( $type && $code ) {

            $campaigns = new DiscountCampaign();
            $campaign = $campaigns->collection( array( 'id' ), array( 'type' => $type, 'code' => $code ) )->fetchAllAs( 'DBUserDiscountCampaign' );
            return $campaign;

         }

         return false;

      }

      public function isCampaignPortal( $cid = 0, $pid = 0 ) {

         if ( DB::query( "SELECT count(*) FROM discount_campaign_portals WHERE discount_campaign_id=? AND portal_id=?", $cid, $pid )->fetchSingle() ) {

            return true;

         }

         return false;

      }

      public function getCampaign() {

         return new DiscountCampaign( $this->campaignid );

      }
      
      static function fromIdAndUser( $discountid, $userid ) {
         
         $res = DB::query( "
            SELECT 
               id 
            FROM 
               discount_campaign_history 
            WHERE 
               discount_campaign_id = ? AND 
               user_id = ?
            ORDER BY id DESC LIMIT 1; 
         ", $discountid, $userid );
         
         if( $res->count() > 0 ) {
            
            list( $id ) = $res->fetchRow();
            return new DBUserDiscountHistory( $id );
            
         }
         
         return null;
         
      }

   }

?>