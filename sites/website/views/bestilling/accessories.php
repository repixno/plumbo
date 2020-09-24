<?PHP
   
   /**
    * Get accessories based on what user is purchasing
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'session.usersessionarray' );
   import( 'website.image' );
   import( 'website.cart' );
   import( 'website.uploadedimagesarray' );
   import( 'website.product.accessory' );
   config( 'website.accessories' );
   
   class OrderprintsAccessories extends Webpage implements IView {
      
      protected $template = 'bestilling.accessories';
      
      public function Execute( $productid = null ) {
         
         $productoption = ProductOption::fromRefId($productid);
         
         
         $product = new Product($productoption->productid);
         
         
         Util::Debug( $product->asArray() );
         
         
         $this->item = $product->asArray();
         
         
         Util::Debug( $this->item );
         
      }
      
      public function addPrintsToCart( $cart ) {
         
         //die('test');
         

         $printOrder = UserSessionArray::getItems( "printorder" );
         $printOrder = reset( $printOrder );
         $tmpOrder = array();
         $prodQty = array();
         $updated = false;
         $prevUploads = UserSessionArray::getItem( 'prev_uploaded_images', 0 );
         
         // Parse the print order to get print quantity
         // for the different products
         if( count( $printOrder["imageobjects"] ) > 0 ) {
            
            foreach( $printOrder["imageobjects"] as $imageId => $imageData ) {
               
               $imageQuantity = $imageData["quantity"];
               $crop = $imageData['cropratio'];
               
               
               if( count( $imageQuantity ) > 0 ) {

                  $updated = true;
                  
                  foreach( $imageQuantity as $prodno => $qty ) {
                     
                     $prevUploads[$imageId] = 1;
                     $tmpOrder[$prodno][$imageId] = $qty;
                     $prodQty[$prodno] += $qty;
                     
                  }
               }

               
               if ( count( $crop ) > 0){
                  foreach ( $crop as $cropping ){
                     if( is_array( $cropping['cropcoordinates'] ) ){
                        $tmpOrder[$cropping['article']]['crop'][$imageId] = $cropping['cropcoordinates'];
                     }
                  }
               }
               
            }
            
            
            UserSessionArray::clearItems( 'prev_uploaded_images' );
            UserSessionArray::addItem( 'prev_uploaded_images', $prevUploads );
            
         }
         
         
         if( count( $prodQty ) > 0 ) {

            foreach( $prodQty as $prodno => $qty ) {
               
               $cart->addItem( $prodno, $qty, array( "images" => $tmpOrder[$prodno] ) );
               
            }
            
         }
         
         
         // Reset all order attributes
         $cart->removePrintAttribute( 'productionmethod' );
         $cart->removePrintAttribute( 'correctionmethod' );
         $cart->removePrintAttribute( 'paperquality' );
         
         // Add the production-, correctionmethods and paperquality to cart
         if( isset( $printOrder["productionmethod"] ) ) {
            //Cart::addAttribute( $printOrder["productionmethod"] );
            $cart->addPrintAttribute( $printOrder["productionmethod"] );
         }
         
         if( isset( $printOrder["correctionmethod"] ) ) {
            //Cart::addAttribute( $printOrder["correctionmethod"] );
            $cart->addPrintAttribute( $printOrder["correctionmethod"] );
         } 
         
         if( isset( $printOrder["paperquality"] ) ) {
            //Cart::addAttribute( $printOrder["paperquality"] );
            $cart->addPrintAttribute( $printOrder["paperquality"] );
         } 
         
         
         UserSessionArray::clearItems( "printorder" );
         UserSessionArray::clearItems( "choosenimages" );
         
         UploadedImagesArray::clear();

         $cart->save();
         
         return $cart;
         
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
            
         } else {
         
            $printOrder = reset( $printOrder );
            $prodnos = array();
            
            
            // Setup all product data for template. Use product tagged as print
            $res = DB::query( "
               SELECT prodno 
               FROM site_product_option 
               WHERE tags 
               LIKE( '%enlargement%' )" );
            
            while( list( $prodno ) = $res->fetchRow() ) {
   
               $quantity = $quantitydata[$prodno];
               
               if( count( $printOrder["imageobjects"] ) > 0 ) {
               
                  foreach( $printOrder["imageobjects"] as $imageid => $imagedata ) {            
                     
                     try {
                        $image = new Image( $imageid );
                        if( !$image->isLoaded() || !$image instanceof Image ) break;
                     } catch( Exception $e ) {
                        continue;
                     }
                     
                     if( !isset( $printOrder["imageobjects"][$imageid] ) ) continue;
                     
                     if( $quantity > 0 ) {

                        $printOrder["imageobjects"][$imageid]["quantity"][$prodno] = $quantity;
                        
                     } else {
                        
                        unset( $printOrder["imageobjects"][$imageid]["quantity"][$prodno] );
                        
                     }
                     
                  }
               
               }
               
            }
            
            if( $quantitydata['0001'] > 0 ){
               
               $quantity = $quantitydata['0001'];
                  
               if( count( $printOrder["imageobjects"] ) > 0 ) {
               
                  foreach( $printOrder["imageobjects"] as $imageid => $imagedata ) {
                     
                     if( $quantitydata['std_choice'] == 'auto'){
                        $prodno = $imagedata['artnr'];
                     }
                     else{
                        $prodno = $quantitydata['std_choice'];
                     }   
                     
                     try {
                        $image = new Image( $imageid );
                        if( !$image->isLoaded() || !$image instanceof Image ) break;
                     } catch( Exception $e ) {
                        continue;
                     }
                     
                     if( !isset( $printOrder["imageobjects"][$imageid] ) ) continue;
                     
                     if( $quantity > 0 ) {

                        $printOrder["imageobjects"][$imageid]["quantity"][$prodno] = $quantity;
                        
                     } else {
                        
                        unset( $printOrder["imageobjects"][$imageid]["quantity"][$prodno] );
                        
                     }
                     
                  }
               
               }
               
            }
         
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
         UserSessionArray::addItem( "printorder", $printOrder );
         
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
      
   }
   
?>