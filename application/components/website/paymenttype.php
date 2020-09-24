<?php

   /**
    * Load payment option from old EF
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'site.paymenttype' );

   class PaymentType extends DBPaymentType  {

      protected $regionid = 1;
      protected $artnr = 0;
      protected $minvalue = 0;
      protected $maxvalue = 0;
      protected $price = 0;
      
      
      /**
       * Create the object from an EF refid
       *
       * @param integer $refid
       * @return object
       */
      static function fromRefId( $refid ) {
         
         $object = PaymentType::fromFieldValue( array( 'refid'=> $refid ), 'PaymentType' );
         if( $object instanceof PaymentType && $object->isLoaded() ) {
            return $object;
         } else {
            throw new SecurityException( 'This is not a payment option or is not loaded.' );
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
            'minvalue'  => $this->minvalue,
            'maxvalue'  => $this->maxvalue,
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
               min_value AS minvalue,
               max_value AS maxvalue,
               price
            FROM 
               region_payment
            WHERE 
               paymentid = ?", $this->refid );
         
         list( $regionid, $artnr, $minvalue, $maxvalue, $price ) = $res->fetchRow();
         
         if( !$price ){
            $price = DB::query( "SELECT price FROM site_product_price WHERE productid = ?" , $this->id )->fetchSingle();
         }
         
         $this->regionid = $regionid;
         $this->artnr = $artnr;
         $this->minvalue = $minvalue;
         $this->maxvalue = $maxvalue;
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