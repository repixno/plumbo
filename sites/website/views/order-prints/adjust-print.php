<?PHP
   
   /**
   *
   * Crop customers images that has wrong ratio
   * 
   *
   * @author Tor Inge Lovland <tor.inge@eurofoto.no>
   *
   */
   
   import( 'website.image' );
   import( 'session.usersessionarray' );
   import( 'website.product' );
   import( 'website.uploadedimagesarray' );
   import( 'website.cart' );
   
   
   
   class OrderprintsCropImages extends UserPage implements IView {
      
      protected $template = 'order-prints.crop-images';

      public function Execute( ) {

         //$choosenImages = UserSessionArray::getItem( "choosenimages", 0 );
         
         //$printOrder = UserSessionArray::getItems( "printorder" );
         
         
         //$cart = new Cart();
         $crop_count = 0;
         if( count( $_POST ) > 0 ) {
         
            $this->updatePrintOrder( $_POST );
         
         }
         
         $printOrder = UserSessionArray::getItems( "printorder" );
         
         
         foreach ( $printOrder[0]["imageobjects"] as $print  ){
            if($print['cropratio']){
               $crop_count++;
               break;
            }
         }
         
         if( $crop_count > 0){
            $this->printorder = $printOrder;
         }else{
            relocate( '/order-prints/accessories' );
         }


          
      }
      
      
       /**
       * Update the print order with the quantities coming
       * from choose-quantity
       *
       * @param array $quantitydata
       */
      public function updatePrintOrder( $quantitydata = array() ) {
         
         $printOrder = UserSessionArray::getItems( "printorder" );
         
         if( count( $quantitydata["orderitem"] ) ) {
            
            $printOrder = array( $this->convertToPrintOrder( $printOrder, $quantitydata["orderitem"] ) );
            $printOrder = reset( $printOrder );
            
         }
         
         if( isset( $quantitydata["productionmethod"] ) ) {
            $printOrder["productionmethod"] = $quantitydata["productionmethod"];
         }
         if( isset( $quantitydata["white-borders"] ) ) {
            $printOrder["productionmethod"] = $quantitydata["white-borders"];
         }
         if( isset( $quantitydata["paperquality"] ) ) {
            $printOrder["paperquality"] = $quantitydata["paperquality"];
         }
         if( isset( $quantitydata["correctionmethod"] ) ) {
            $printOrder["correctionmethod"] = $quantitydata["correctionmethod"];
         }
         
         UserSessionArray::clearItems( "printorder" );
         UserSessionArray::addItem( "printorder", $printOrder);
         
      }
      
      
      /**
       * Convert the data from eachprint into the correct format for a print order
       *
       * @param unknown_type $printOrder
       * @param unknown_type $orderitems
       */
      private function convertToPrintOrder( $printOrder, $orderitems ) {

         $printOrder = $printOrder[0];
         
         if( count( $orderitems ) > 0 ) {

            foreach( $orderitems as $prodno => $images ) {
               
               if( count( $images ) > 0 ) {
                  
                  foreach( $images as $imageid => $qty ) {
                     
                     // Add the quantity for this prouct or remove the product completely
                     if( isset( $printOrder["imageobjects"][$imageid] ) ) {
                        if( $qty > 0 ) {
                           $printOrder["imageobjects"][$imageid]["quantity"][$prodno] = (int) $qty;
                           
                           $imageRatio = $printOrder["imageobjects"][$imageid]['ratio'];
                           
                           if( !isset( $imageRatio )){
                              $imageRatio = $printOrder["imageobjects"][$imageid]['x'] / $printOrder["imageobjects"][$imageid]['y'];   
                           }
                           $productRatio = $this->productRatio($prodno);

                           if( !isset( $printOrder["imageobjects"][$imageid]['orientation'] ) ){
                              $imageOriantation = $printOrder["imageobjects"][$imageid]['x'] / $printOrder["imageobjects"][$imageid]['y'];  
                              if( $imageOriantation >= 1){
                                 $productRatioImage = "landscape";
                              }else{
                                 $productRatioImage = "portrait";
                              }
                              $productRatio =  $productRatio[ $productRatioImage ];
                           }
                           else{
                              $productRatio =  $productRatio[ $printOrder["imageobjects"][$imageid]['orientation'] ];
                           }
                           $ratioArray = array( $imageRatio, $productRatio );
                           if( (max( $ratioArray ) - min( $ratioArray )) > 0.05 ){
                              $printOrder["imageobjects"][$imageid]["cropratio"][$prodno] = array(
                                                                                           'ratiodiff' => max( $ratioArray ) - min( $ratioArray ),
                                                                                           'article'   => $prodno
                                                                                             );   
                           }
                        } else {
                           unset( $printOrder["imageobjects"][$imageid]["quantity"][$prodno] );                           
                        }  
                     }  
                  } 
                  
                 
               }  
            }

         }
         
         return $printOrder;
         
      }
      
      public function productRatio( $prodno ){
         

         $ratio['0001'] = array(
               'landscape' => '1.5',
               'portrait'  => '0.66'
         );
         $ratio['0419'] = array(
               'landscape' => '1.33',
               'portrait'  => '0.75'
         );
         $ratio['7345'] = array(
               'landscape' => '1',
               'portrait'  => '1'
         );
         $ratio['0014'] = array(
               'landscape' => '1',
               'portrait'  => '1'
         );
         $ratio['0003'] = array(
               'landscape' => '1.25',
               'portrait'  => '0.80'
         );         
         $ratio['0002'] = array(
               'landscape' => '1.33',
               'portrait'  => '0.75'
         );
         $ratio['0439'] = array(
               'landscape' => '1.5',
               'portrait'  => '0.66'
         );
         $ratio['0005'] = array(
               'landscape' => '1.33',
               'portrait'  => '0.75'
         );
          $ratio['0534'] = array(
               'landscape' => '1.5',
               'portrait'  => '0.66'
         );
         
         
         return $ratio[$prodno];
         
      }
      
      
   }



?>