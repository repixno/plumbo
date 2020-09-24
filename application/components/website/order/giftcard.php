<?php


   /**
    * 
    * Component for Giftcard
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   model( 'site.giftcard' );
   
   class Giftcard extends DBGiftcard {
      
      /**
       * Try loading a giftcard from code
       *
       * @param string $code
       * @return Giftcard or boolean false
       * 
       */
      static function fromCode( $code ) {
         
         if( strlen( $code ) > 0 ) {
            
            $giftcardid = DB::query( "
               SELECT
                  giftcardid
               FROM
                  giftcard 
               WHERE 
                  code = ? AND
                  value > 0
               ORDER BY
                  giftcardid DESC
               LIMIT 1
            ", $code )->fetchSingle();
            
            if( isset( $giftcardid ) && $giftcardid > 0 ) {
               
               return new Giftcard( $giftcardid );
               
            }
            
         }
         
         return false;
         
      }
      
      
      public function asArray() {
         
         $productoption = ProductOption::fromRefId( $this->refid );
         $product = new Product( $productoption->productid );
         
         return array(
            'giftcardid' => $this->giftcardid,
            'buyerid' => $this->buyerid,
            'value' => $this->value,
            'refid' => $this->refid,
            'description' => $this->description,
            'code' => $this->code,
            'product' => $product->asArray(),
         );
         
      }
      
   }


?>