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
      
      protected $template = 'order-prints.accessories';
      
      public function Execute() {
        
         if( Dispatcher::getPortal() == 'FC-001' || Dispatcher::getPortal() == 'MY-KID' ) relocate( "/cart" );
         
         $cart = new Cart();
         
         $this->addPrintsToCart( $cart );
         
         $accessories = Settings::GetSection( 'accessories', array() );
         
         $dbaccessories = $commondbaccessories = array();
         $collection = new ProductAccessory();
         foreach( $collection->collection(array('accessoryid'))->fetchAllAs('ProductAccessory') as $accessory ) {
            $key = sprintf( '%s|%s|%s|%s', $accessory->productid, $accessory->minquantity, $accessory->maxquantity, $accessory->onlyoptionid );
            if( $accessory->productid > 0 ) {
               $dbaccessories[$key]['productid'] = $accessory->productid;
               $dbaccessories[$key]['minquantity'] = $accessory->minquantity;
               $dbaccessories[$key]['maxquantity'] = $accessory->maxquantity;
               $dbaccessories[$key]['limitoption'] = $accessory->onlyoptionid > 0 ? array( $accessory->onlyoptionid ) : array();
               $dbaccessories[$key]['products'][$accessory->accessoryproductid] = true;
            } else {
               #$commondbaccessories[$accessory->accessoryproductid] = true;
            }
         }
         
         foreach( $dbaccessories as $record ) {
            $record['products'] = array_keys( $record['products'] );
            $accessories['prints'][] = $record;
            $accessories['all'][] = $record;
         }
         
         $commonaccessories = array_keys( $commondbaccessories );
         
         $items = $cart->enum();
         $items = isset( $items['items'] ) ? $items['items'] : array();
         
         $associatedproducts = array();
         
         if( isset( $items ) && count( $items ) ) {
            
            $allrules = array();
            if( isset( $accessories ) && count( $accessories ) )
            foreach( $accessories as $section => $accessoryrules ) {
               foreach( $accessoryrules as $accessoryrule ) {
                  $allrules[$section][$accessoryrule['productid']][] = $accessoryrule;
               }
            }
            
            foreach( $items as $section => $sectionitems ) {
               
               switch( $section ) {
                  
                  case 'gifts':
                  case 'mediaclip':
                  case 'ukeplan':
                     
                     $section = 'all';
                     
                     foreach( $sectionitems as $innerloop ) {
                        
                        foreach( $innerloop as $sectionitem ) {
                           
                           if( isset( $allrules[$section][$sectionitem['product']['id']] ) )
                           foreach( $allrules[$section][$sectionitem['product']['id']] as $accessoryrule ) {
                              
                              $count = $sectionitem['quantity'];
                              if( ( ( $count <= $accessoryrule['maxquantity'] && (int) $accessoryrule['maxquantity'] )
                                    || !$accessoryrule['maxquantity'] ) &&
                                  ( ( $count >= $accessoryrule['minquantity'] && (int) $accessoryrule['minquantity'] )
                                    || !$accessoryrule['minquantity'] )
                              ) {
                                 
                                 if( !count( $accessoryrule['products'] ) ) continue;
                                 
                                 if( $sectionitem['optionid'] > 0 && count( $accessoryrule['limitoption'] ) > 0 ) {
                                    
                                    if( !in_array( $sectionitem['optionid'], $accessoryrule['limitoption'] ) ) continue;
                                    
                                 }
                                 
                                 foreach( $accessoryrule['products'] as $productid ) {
                                    
                                    try {
                                       
                                       $product = new Product( $productid );
                                       $associatedproducts[$productid] = $product->asArray();
                                       
                                    } catch ( Exception $e ) {}
                                    
                                 }
                                 
                              }
                              
                           }
                           
                        }
                        
                     }
                     
                     break;
                     
                  case 'prints':
                     
                     foreach( $sectionitems as $sectionitem ) {
                        
                        if( isset( $allrules[$section][$sectionitem['product']['id']] ) )
                        foreach( $allrules[$section][$sectionitem['product']['id']] as $accessoryrule ) {
                           
                           #util::Debug( $sectionitem['quantity'] ); die();
                           
                           $count = $sectionitem['quantity'];
                           if( ( ( $count <= $accessoryrule['maxquantity'] && (int) $accessoryrule['maxquantity'] )
                                 || !$accessoryrule['maxquantity'] ) &&
                               ( ( $count >= $accessoryrule['minquantity'] && (int) $accessoryrule['minquantity'] )
                                 || !$accessoryrule['minquantity'] )
                           ) {
                              
                              if( !count( $accessoryrule['products'] ) ) continue;
                              
                              if( $sectionitem['optionid'] > 0 && count( $accessoryrule['limitoption'] ) > 0 ) {
                                 
                                 if( !in_array( $sectionitem['optionid'], $accessoryrule['limitoption'] ) ) continue;
                                 
                              }
                              
                              foreach( $accessoryrule['products'] as $productid ) {
                                 
                                 try {
                                    
                                    $product = new Product( $productid );
                                    $associatedproducts[$productid] = $product->asArray();
                                    
                                 } catch ( Exception $e ) {}
                                 
                              }
                              
                           }
                           
                        }
                        
                     }
                     
                     break;
                  
               }
               
            }
            
         }
         
         while( count( $associatedproducts ) < 99 && count( $commonaccessories ) > 0 ) {
            
            try {
               
               $productid = array_shift( $commonaccessories );
               $product = new Product( $productid );
               $associatedproducts[$productid] = $product->asArray();
               
            } catch ( Exception $e ) {}
            
         }
         

         
         $this->associatedproducts = array_values( $associatedproducts );
         if( !count( $associatedproducts ) ) {
            relocate( '/cart' );
         }
         
         
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