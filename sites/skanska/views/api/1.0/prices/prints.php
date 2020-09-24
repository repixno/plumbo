<?PHP
   
   import( 'pages.json' );
   
   class PrintPrices extends JSONPage implements NoAuthRequired, IView {
      
      public function get() {
         
         $price = 0;
         $numimages = 0;
         $motives = array();
         
         foreach( $_POST['orderitem'] as $prodno => $images ) {
            
            $quantity = array_sum( $images );
            
            foreach( $images as $imageid => $count ) {
               if( $count > 0 ) {
                  $motives[$imageid] = true;
               }
            }
            
            if( $quantity > 0 ) {
               
               switch( $prodno ) {
                  
                  case '0001':
                  case '0419':
                     $numimages += $quantity;
                     break;
                  
               }
               
               $productoption = ProductOption::fromProdNo( $prodno );
               if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {
                  // All's ok. Keep on trucking.
                  $unitprice = (float) $productoption->getPrice( $quantity );
                  $price += $unitprice * $quantity;
               }
               
            }
            
         }
         
         $nummotives = count( $motives );
         
         if( isset( $_POST['white-borders'] ) ) {
            
            $prodno = sprintf( '%04d', $_POST['white-borders'] );
            $unitprice = ProductOption::priceFromProdNo( $prodno, $numimages );
            $price += $unitprice * $numimages;
            
         } else
         if( isset( $_POST['productionmethod'] ) ) {
            $prodno = sprintf( '%04d', $_POST['productionmethod'] );
            $unitprice = ProductOption::priceFromProdNo( $prodno, $numimages );
            $price += $unitprice * $numimages;
         }
         
         if( isset( $_POST['paperquality'] ) ) {
            $prodno = sprintf( '%04d', $_POST['paperquality'] );
            $unitprice = ProductOption::priceFromProdNo( $prodno, $numimages );
            $price += $unitprice * $numimages;
         }
         
         if( isset( $_POST['correctionmethod'] ) ) {
            $prodno = sprintf( '%04d', $_POST['correctionmethod'] );
            $unitprice = ProductOption::priceFromProdNo( $prodno, $nummotives );
            $price += $unitprice * $nummotives;
         }
         
         $this->price = round( $price, 2 );
         $this->result = true;
         $this->message = 'OK';
         
         return true;
         
      }
      
   }
   
?>