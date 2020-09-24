<?php

   /**
    * Load delivery option from old EF
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'site.deliverytype' );

   class DeliveryType extends DBDeliveryType  {

      protected $regionid = 1;
      protected $artnr = 0;
      protected $weight = 0;
      protected $price = 0;
      
      
      /**
       * Create the object from an EF refid
       *
       * @param integer $refid
       * @return object
       */
      static function fromRefId( $refid ) {
         
         $object = DeliveryType::fromFieldValue( array( 'refid'=> $refid ), 'DeliveryType' );
         
         if( !$object ){
            $refid = DB::query("SELECT deliverytype FROM site_delivery WHERE id = ? ", 2 )->fetchSingle();
            $object = new PaymentType($refid);
         }
         
         if( $object instanceof DeliveryType && $object->isLoaded() ) {
            return $object;
         } else {
            throw new SecurityException( 'This is not a delivery option or is not loaded.' );
         }
         
      }

      
      /**
       * Return array representation of object
       *
       * @return array
       */
      public function asArray() {
         
         return array(
            'id'     => $this->id,
            'title'     => $this->title,
            'refid'     => $this->refid,
            'regionid'  => $this->regionid,
            'artnr'     => $this->artnr,
            'weight'    => $this->weight,
            'price'     => $this->price,
         );
         
      }
      
      
      /**
       * Load the payment data from old EF.
       *
       */
      public function isLoaded() {

         $res = DB::query( "
            SELECT 
               regionid,
               artnr,
               weight,
               price
            FROM 
               region_delivery
            WHERE 
               deliveryid = ?", $this->refid );
         
         list( $regionid, $artnr, $weight, $price ) = $res->fetchRow();
         
         if( !$price ){
            $price = DB::query( "SELECT price FROM site_delivery WHERE id = ?" , $this->refid )->fetchSingle();
         }
         
         $this->regionid = $regionid;
         $this->artnr = $artnr;
         $this->weight;
         $this->price = $price;
         
         return parent::isLoaded();
         
      }
      
      
      /**
       * Get the price of this payment option
       *
       * @return unknown
       */
      public function getPrice() {
         
         return $this->price;
         
      }
      
      
      public function getArtnr() {
         return $this->artnr;
      }
      
   }


?>