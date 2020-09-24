<?php

   model( 'user.discountcampaign' );
   model( 'site.discountquantum' );

   class DiscountCampaign extends DBUserDiscountCampaign {


      public function asArray() {

         return array(
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'start' => $this->start,
            'stop' => $this->stop,
            'active' => $this->active,
            'code' => $this->code,
            'created' => $this->created,
            'onetime' => $this->one_time
         );

      }

      static function fromCode( $code ) {

         try {

            return DBDiscountQuantum::fromFieldValue(
               array(
                  'code' => $code
               ),
               'DiscountCampaign'
            );

         } catch( Exception $e ) {

            return false;

         }

      }

      public function getDiscounts() {

         $quantums = new DBDiscountQuantum();

         $ret = array();
         foreach ( $quantums->collection( array( 'id' ), array( 'discount_campaign_id' => $this->id ) )->fetchAllAs( 'DBDiscountQuantum' ) as $quantum ) {

            $ret[] = $quantum;

         }
         
         return $ret;

      }
      
      
      static function enumRefId( $id ) {
         
         $result = array();
         $res = DB::query( "SELECT distinct(article_id) FROM discount_campaign_quantums WHERE discount_campaign_id = ?", $id );
         while( list( $refid ) = $res->fetchRow() ) {
            
            $result []= $refid;
            
         }
         
         return $result;
         
      }


   }

?>