<?PHP
   
   model( 'site.basket' );
   
   class Basket extends DBBasket {
      
      public function getBasket() {
         
         try {
            return @unserialize( $this->fieldGet( 'basket' ) );
         } catch ( Exception $e ) {
            return Array();
         }
      }
      
      public function setBasket( Array $basket ) {
         
         $this->fieldSet( 'basket', @serialize( $basket ), 9 );
         
      }
      
   }
   
?>