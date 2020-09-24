<?PHP
   
   model( 'site.product.accessory' );
   
   class ProductAccessory extends DBProductAccessory {
      
      public function asArray() {
         
         if( $this->accessoryproductid > 0 ) {
            $product = new Product( $this->accessoryproductid );
            $product = $product->asArray();
         } else {
            $product = false;
         }
         
         if( $this->onlyoptionid > 0 ) {
            $option = new ProductOption( $this->onlyoptionid );
            $option = $option->asArray();
         } else {
            $option = false;
         }
         
         return array(
            'id' => $this->accessoryid,
            'onlyoption' => $option,
            'product' => $product,
            'minquantity' => $this->minquantity,
            'maxquantity' => $this->maxquantity,
            'created' => $this->created,
            'createdby' => User::getNameFromUid( $this->userid ),
            'updated' => $this->created,
            'updatedby' => User::getNameFromUid( $this->userid ),
         );
         
      }
      
   }

?>