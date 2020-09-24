<?php

   /**
    * Complete checkout by emulating cart and placing order.
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.cart' );
   import( 'website.product' );
   import( 'website.user' );
   import( 'website.emulatedcart' );
   config( 'website.countries' );
   config( 'website.license' );
   import( 'website.order.default' );
   
   class CheckoutComplete extends WebPage implements IView {

      protected $template = '';
      
public function Execute() {
         
         //
         // Set the order comments here
         //
         $comment = addslashes( $_POST["comment"] );
         
         $cart = new Cart();
         
         if( strlen($comment) > 0 ){
            $cart->setComment($comment);
            $cart->save();
         }
         
         #EmulatedCart::setMessage( $comment );
         
         
         
         // If no deliverytype is given previously
         // then we need to update with one.
         // Needed by EF 2.5
         if( $cart->serviceProductsOnly() ) {
            $cart->setDeliveryType( 1 );
            $cart->save();
         }
         
         $cartarray = $cart->asArray();
         
         
         // Check if cart is empty
         if( $cart->getTotalItems() == 0 ) {
            
            relocate( '/cart' );
            
         }
         
         //$this->emulateOldCart( $cartarray );
         //$this->setCustomerInfo( $cartarray );
         
         // Need this to pass through old order system.
         // Get it from user i flogged, otherwise create new user
         // or check user from email.
         // Currently not fixed.
         
         
         // We need to set a shopuid for purchases when not logged in
         // This is an EF < 2.8 adaptation. Sad sad sad!!!
         /*if( !Login::isLoggedIn() ) {
            
            if( isset( $cartarray["contactinfo"]["email"] ) ) {
               
               $user = User::fromUsernameAndPortal( $cartarray["contactinfo"]["email"] );
               EmulatedCart::setShopUID( $user->userid );
               
            }
            
         } else {
         
            EmulatedCart::setShopUID( Login::userid() );
            
         }*/
         
         UserSessionArray::clearItems( 'purchased_cart' );
         UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
         
         //Cart::clear();
         //$cart->clear();
         
         /*if( !EmulatedCart::hasShopUID() ) {
            
            relocate( '/cart' );   
            
         }*/
         
         $userorder = new UserOrder();
         if( $orderid = $userorder->executeOrder() ) {
         
            relocate( '/checkout_gratis/finished/' . $orderid );
            #util::Debug( $orderid );
            die();
            
         } else {
            
            util::Debug( "Failed!" );
            
         }
         
         
         //relocate( '/do_order_complete.php' );
         
      }
      
      
      private function setCustomerInfo( $cart ) {
         
         
      }
      
      /**
       * Put things in an array that EF 2.5 understands
       *
       * @param array $cart
       * @return array
       * 
       */
      private function emulateOldCart( $cart ) {

         $emulatedcart = array();
         $emulatedcart["ordrelinje"] = array();
         $totalWeight = 0.00;
         $totalImages = 0;
         $productionmethod = array();
         $correctionmethod = array();
         $paperquality = array();
         $regularProduct = false;
         
         if( count( $cart["items"] ) > 0 ) {
            
            $itemtypes = array_keys( $cart["items"] );

            foreach( $itemtypes as $producttype ) {
               
               switch( $producttype ) {
               
                  // Print products such as 10x13 etc
                  case 'prints':
                     $printproducts = $cart["items"]["prints"];
                     // Loop throug all print products
                     if( count( $printproducts ) > 0 ) {
                        foreach( $printproducts as $prodno => $proddata ) {
                           $emulatedcart = $this->setupPrintOrderRow( $prodno, $proddata, $emulatedcart );
                           /*if( isset( $cart['discount'][$prodno] ) ) {
                              $emulatedcart = $this->setupDiscount( $prodno, $proddata, $cart, $emulatedcart );
                           }*/
                        }
                     }
                     break;
                  
                  case 'enlargements':
                     $enlargementproducts = $cart["items"]["enlargements"];

                     // Loop throug all print products
                     if( count( $enlargementproducts ) > 0 ) {
                        foreach( $enlargementproducts as $prodno => $proddata ) {
                           // Uses same format as prints
                           $emulatedcart = $this->setupPrintOrderRow( $prodno, $proddata, $emulatedcart );
                        }
                     }
                     break;
                     
                  // Production methods such as Fill-in, fit-in etc
                  case 'productionmethod':
                     
                     $emulatedcart = $this->setupPrintMethod( $producttype, $cart["items"]["productionmethod"], $emulatedcart, $cart );
                     break;
                     
                  // Correction method such as professional, automatic etc
                  case 'correctionmethod':
                     $emulatedcart = $this->setupPrintMethod( $producttype, $cart["items"]["correctionmethod"], $emulatedcart, $cart );
                     break;
                     
                  // DP etc
                  case 'paperquality':
                     $emulatedcart = $this->setupPrintMethod( $producttype, $cart["items"]["paperquality"], $emulatedcart, $cart );
                     break;
                     
                  // Products through old gift editor
                  case 'gifts':
                     
                     $gifts = $cart["items"]["gifts"];
                     $emulatedcart = $this->setupGiftOrderRows( $gifts, $emulatedcart );
                     
                     break;
                     
                  // Products through mediaclip,
                  // Such as photobooks
                  case 'mediaclip':
                     
                     $mediaclip = $cart["items"]["mediaclip"];
                     $emulatedcart = $this->setupMediaclipOrderRows( $mediaclip, $emulatedcart );
                     
                     break;
                     
                  // Accessories
                  case 'goods':
                     $goods = $cart['items']['goods'];
                     $emulatedcart = $this->setupGoods( $goods, $emulatedcart );
                     break;
                     
                  case 'subscription':
                     $subscription = $cart['items']['subscription'];
                     
                     // Using goods array. Should also work for subscription
                     $emulatedcart = $this->setupGoods( $subscription, $emulatedcart );
                     break;
                     
                  default:
                     break;
               
               }
               
            }
            
            $emulatedcart = $this->setupNewDiscount( $cart, $emulatedcart );
            $emulatedcart = $this->setupMainCart( $cart, $emulatedcart );
            $this->setContactInfo( $cart );
            $this->setDeliveryInfo( $cart );
            
            
         }
         
         // Set EF 2.5 specific cart
         EmulatedCart::setCart( $emulatedcart );
         
         // Does the cart only contain service products?
         // Flag it for old EF order system

         // Load the cart to object
         $tmpcart = new Cart();

         if( $tmpcart->serviceProductsOnly() ) {
            EmulatedCart::setServiceProductsOnly( true );
         }
         
      }
      
      
      private function setupNewDiscount( $cart, $emulatedcart ) {
         
         if( !empty( $cart['discount']['final'] ) && !empty( $cart['discount']['info']['id'] ) ) {
            
            $cid = $cart['discount']['info']['id'];
            
            $emulatedcart['discount_id'] = $cid;
            
            $articleinfo = array();
            $articleinfo['articleid'] = array();
            
            $emulatedcart['discount'][$cid] = array(
               'id' => $cart['discount']['info']['id'],
               'name' => $cart['discount']['info']['name'],
               'description' => null,
               'type' => $cart['discount']['info']['type'],
               'active' => $cart['discount']['info']['active'],
               'start' => $cart['discount']['info']['start'],
               'stop' => $cart['discount']['info']['stop'],
               'discount' => 0,
               'discount_type' => 0,
               'code' => $cart['discount']['info']['code'],
            );
            
             
            
            $emulatedcart['discount'][$cid]['articleinfo'] = array();
            $emulatedcart['discount'][$cid]['articleinfo']['articleid'] = array();
            
            if( count( $cart['discount']['products'] ) > 0 ) {
               foreach( $cart['discount']['products'] as $refid ) {
                  $emulatedcart['discount'][$cid]['articleinfo']['articleid'] []= $refid;
               }
            }
            
            $totaldiscount = 0.00;
            
            if( count( $cart['discount']['final'] ) > 0 ) {
               foreach( $cart['discount']['final'] as $key => $values ) {
                  $refid = $values['refid'];
                  $emulatedcart['discount_quantities'][$refid] = array(
                     'quantity' => $values['quantity'],
                  );
                  
                  $emulatedcart['discount_artnr_price'][$refid] = array(
                     'price' => $values['totaldiscount'],
                  );
                  
                  $totaldiscount += $values['totaldiscount'];
                  
               }
            }
            
            if( !isset( $emulatedcart['discount_price'] ) ) {
               $emulatedcart['discount_price'] = 0.00;
            }
            
            
            $emulatedcart['discount_price'] = $totaldiscount; 
            
         }
         
         return $emulatedcart;
         
      }
      
      
      
      private function setupDiscount( $prodno, $proddata, $cart, $emulatedcart ) {
         
         $data = $cart['discount'][$prodno];
         $campaign = $data['discount'][0]['campaign'];
         $quantums = $data['discount'][0]['quantum'];
         $userdata = $data['discount'][0]['history'];
         $quantity = $cart['discount'][$prodno]['quantity'];
         
         $emulatedcart['discount_id'] = $campaign['id'];
         
         $articleinfo = array();
         $articleinfo['articleid'] = array();
         
         if( !isset( $emulatedcart['discount'][$campaign['id']] ) ) {
            $emulatedcart['discount'][$campaign['id']] = array(
               'id' => $campaign['id'],
               'name' => $campaign['title'],
               'description' => null,
               'type' => $userdata['type'],
               'active' => $campaign['active'],
               'start' => $campaign['start'],
               'stop' => $campaign['stop'],
               'discount' => $campaign['discount'],
               'discount_type' => $campaign['discount_type'],
               'code' => $userdata['code'],
            );
         }
         
         
         if( !isset( $emulatedcart['discount'][$campaign['id']]['articleinfo'] ) ) {
            $emulatedcart['discount'][$campaign['id']]['articleinfo'] = array();
            $emulatedcart['discount'][$campaign['id']]['articleinfo']['articleid'] = array();
         }
         
         $emulatedcart['discount'][$campaign['id']]['articleinfo']['articleid'] []= $proddata['refid'];
         
         $emulatedcart['discount_quantities'][$proddata['refid']] = array(
            'quantity' => $cart['discount'][$prodno]['quantity'],
         );
         
         $emulatedcart['discount_artnr_price'][$proddata['refid']] = array(
            'price' => CartDiscount::getDiscount( $quantity, $data['discount'] ),
         );
         
         if( !isset( $emulatedcart['discount_price'] ) ) {
            $emulatedcart['discount_price'] = 0.00;
         }
         $emulatedcart['discount_price'] += Price::format( ( $emulatedcart['discount_artnr_price'][$proddata['refid']]['price'] * $cart['discount'][$prodno]['quantity'] ) );
         
         return $emulatedcart;
         
      }
      
      
      
      /**
       * Setup goods order lines
       *
       * @param unknown_type $goods
       * @param unknown_type $emulatedcart
       */
      private function setupGoods( $goods, $emulatedcart ) {
         
         if( count( $goods ) > 0 ) {
            
            foreach( $goods as $cartproductoptionid => $cartproduct ) {
               
               $productoption = ProductOption::fromProdNo( $cartproductoptionid );
               if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {
                  
                  if( isset( $cartproduct['set'] ) ) {
                     EmulatedCart::CDDVDOrder( $cartproduct['set'] );  
                  }
                  
                  $tags = explode( " ", $productoption->tags );
                  
                  // Product description added to emulated cart
                  $emulatedcart['ordrelinje'][$productoption->refid]['ordredata']['text'] = $cartproduct['title'];
                  if( isset( $cartproduct['set'] ) ) {
                     $emulatedcart['ordrelinje'][$productoption->refid]['ordredata']['number'] = $cartproduct['set']['totalitemquantity'];
                  } else {
                     $emulatedcart['ordrelinje'][$productoption->refid]['ordredata']['number'] = $cartproduct['quantity'];
                  }
                  $emulatedcart['ordrelinje'][$productoption->refid]['ordredata']['unit_price'] = $cartproduct['unitprice'];
                  $emulatedcart['ordrelinje'][$productoption->refid]['ordredata']['total_price'] = $cartproduct['price'];
                  $emulatedcart["ordrelinje"][$productoption->refid]['ordredata']["functionality"] = array(
				        "remove" => remove_offer_image,
			         );
			         
			         if( in_array( 'giftcard', $tags ) ) {
                     $emulatedcart["giftcardpurchase"][$productoption->refid]['qty'] = $cartproduct['quantity'];
                     if( isset( $cartproduct['giftcard']['print'] ) ) {
                        $emulatedcart["giftcardpurchase"][$productoption->refid]['print'] = true;
                     }
                  }
                  
			         if( isset( $cartproduct['set'] ) ) {
                     $emulatedcart['image_offer'][$productoption->refid] = $cartproduct['set']['totalitemquantity'];
			         } else {
			            $emulatedcart['image_offer'][$productoption->refid] = $cartproduct['quantity'];
			         }
                  
               }
               
            }
            
         }
         
         return $emulatedcart;
         
      }
      
      
      
      /**
       * Emulate an order line for old cart system
       *
       * @param array $gifts
       * @param array $emulatedcart
       * @return array
       */
      private function setupGiftOrderRows( $gifts, $emulatedcart ) {
         
         if( count( $gifts ) > 0 ) {

            foreach( $gifts as $prodno => $product ) {

               if( count( $product ) > 0 ) {

                  foreach( $product as $templateid => $productdata ) {

                     if( count( $productdata['license'] ) > 0 ) {
                        
                        $licenserefid = Settings::Get( 'license', 'refid' );
                        $result = $this->addLicenseFee( $productdata, $emulatedcart["ordrelinje"][$licenserefid]["ordredata"] );
                        $emulatedcart["ordrelinje"][$licenserefid]["ordredata"] = $result;
                        $emulatedcart['license'][$licenserefid] = $result;
                        
                     }
                     
                     // All images and qty for this product
                     $refid = $productdata["refid"];
            
                     // Product description added to emulated cart
                     $emulatedcart["ordrelinje"][$refid.'_'.$templateid]["ordredata"]["text"]          = $productdata['product']["title"];
                     
                     // Need to check this one and how it is used? 
                     $emulatedcart["ordrelinje"][$refid.'_'.$templateid]["ordredata"]["options"]       = 0;
                     
                     $emulatedcart["ordrelinje"][$refid.'_'.$templateid]["ordredata"]["number"]        = $productdata['quantity'];
                     $emulatedcart["ordrelinje"][$refid.'_'.$templateid]["ordredata"]["unit_price"]    = $productdata["unitprice"];
                     $emulatedcart["ordrelinje"][$refid.'_'.$templateid]["ordredata"]["total_price"]   = $productdata["price"];
                     $emulatedcart["ordrelinje"][$refid.'_'.$templateid]["ordredata"]["functionality"] = array(
				           "change" => change_image,
				           "remove" => remove_image,
				           "produce" => produce_image,
				           "export" => export_image
			            );
            

			            // Try setting up options if they exist
			            $options = $productdata['product']['options'];
                     if( count( $options ) > 0 ) {
                     
                        foreach( $options as $option ) {
                           
                           if( $option['id'] == $productdata['optionid']) {
                              
                              if( !is_null( $option['refsubid'] ) && ( $option['refsubid'] != 0 ) ) {
                                 $refsubid = $option['refsubid'];
                                 $refsubid = explode( '-', $refsubid );
                                 if( count( $refsubid ) > 1 ) {
                                    $mainalternative = reset( $refsubid );
                                    $subalternative = end( $refsubid );
                                 }
                                 
                              }
                              
                           }
                        
                        }
                     
                     }
                     
			            // Add options to emulated cart
                     if( isset( $mainalternative ) && isset( $subalternative ) ) {

                        $emulatedcart["mal_order"][$refid][$templateid]['options'][$mainalternative] = $mainalternative;
                        $emulatedcart["mal_order"][$refid][$templateid]['copies'][$subalternative] = $productdata['quantity'];
                        $emulatedcart["mal_order"][$refid][$templateid]['text'] = 0;
                        
                     } else {

                        $emulatedcart["mal_order"][$refid][$templateid] = array(
                           "copies" => $productdata["quantity"],
                           "text" => 0
                        );
                        
                     }
                     
                     if( isset( $productdata['redeyeremoval'] ) && is_array( $productdata['redeyeremoval'] ) ) {
                        $emulatedcart["mal_order"][$refid][$templateid]['redeye'] = 1;
                     }

                  }

               }

            }

         }
         
         return $emulatedcart;
         
      }
      
      
      
      /**
       * Emulate the old cart symstem for mediaclip products
       *
       * @param array $mediaclip
       * @param array $emulatedcart
       * @return array
       */
      private function setupMediaclipOrderRows( $mediaclip, $emulatedcart ) {
         
         if( count( $mediaclip ) > 0 ) {

            foreach( $mediaclip as $prodno => $product ) {
               
               
              

               if( count( $product ) > 0 ) {

                  foreach( $product as $projectid => $productdata ) {                     

                     if( count( $productdata['license'] ) > 0 ) {
                        
                        $licenserefid = Settings::Get( 'license', 'refid' );
                        $result = $this->addLicenseFee( $productdata, $emulatedcart["ordrelinje"][$licenserefid]["ordredata"] );
                        $emulatedcart["ordrelinje"][$licenserefid]["ordredata"] = $result;
                        $emulatedcart['license'][$licenserefid] = $result;
                        
                     }
                     
                     // All images and qty for this product
                     $refid = $productdata["refid"];
            
                     // Product description added to emulated cart
                     $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["text"]          = $productdata['usertitle'].'<splitter>'.$productdata['referenceid'];
                     
                     // Need to check this one and how it is used? 
                     if( isset( $productdata['redeyeremoval'] ) && is_array( $productdata['redeyeremoval'] ) ) {
                        $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["options"]    = "Redeye correction";
                        $redeye = $productdata['redeyeremoval']['refid'];
                     }else $redeye = 0;
                     
                     $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["number"]        = $productdata['quantity'];
                     $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["unit_price"]    = $productdata["unitprice"];
                     $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["total_price"]   = $productdata["price"];
                     $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["functionality"] = array(
				           "change" => change_mp,
				           "remove" => remove_mp,
			            );
            
                     $emulatedcart["mp"][$refid][$projectid] = array(
                        'copies' => $productdata['quantity'],
                        'text'   => $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["text"],
                        'redeye' => $redeye
                      );
                        
                     $emulatedcart["mp_text"][$refid][$projectid] = $emulatedcart["ordrelinje"][$refid.'_'.$projectid]["ordredata"]["text"];
                     
                     
                     // Setup order row for extra pages
                     if( isset( $productdata["extrapages"]['quantity'] ) ) {
                        
                        $epRefId = $productdata["extrapages"]["refid"];
                        
                        $emulatedcart["ordrelinje"][$epRefId."_".$projectid]["ordredata"]["text"]        = $productdata["extrapages"]['product']["title"];
                        $emulatedcart["ordrelinje"][$epRefId."_".$projectid]["ordredata"]["number"]      = $productdata["extrapages"]["quantity"];
                        $emulatedcart["ordrelinje"][$epRefId."_".$projectid]["ordredata"]["unit_price"]  = $productdata["extrapages"]["unitprice"];
                        $emulatedcart["ordrelinje"][$epRefId."_".$projectid]["ordredata"]["total_price"] = $productdata["extrapages"]["price"];
                        
                        $emulatedcart["mp"][$epRefId][$projectid] = array(
                           'copies' => $productdata["extrapages"]["quantity"],
                           'text'   => $productdata["extrapages"]["quantity"]
                           );
                        $emulatedcart["mp_text"][$epRefId][$projectid] = $productdata["extrapages"]["quantity"];
                        
                     }

                  }

               }

            }


         }
         
         return $emulatedcart;
         
      }
      
      
      private function setupDeliveryType( $proddata, $emulatedcart ) {
         
         $emulatedcart["deliveryinfo"] = $proddata["refid"];

         return $emulatedcart;
         
      }
      
      
      /**
       * Emulate an order row for production. correctionmethods
       * or for paper quality. Needs to be some hardcoded artnrs
       * here for EF < 2.8 to work.
       *
       * @param array $proddata
       * @param array $emulatedcart
       * @return array
       * 
       */
      private function setupPrintMethod( $method, $proddata, $emulatedcart, $cart ) {
         
         $refid = $proddata["refid"];
         $hasPrints = false;
         $hasEnlargements = false;
         $addedMethod = false;
         
         if( count( $cart["items"]["prints"] ) > 0 ) {
            $hasPrints = true;
         }
         if( count( $cart["items"]["prints"] ) > 0 ) {
            $hasEnlargements = true;
         }
         
         
         switch( $method ) {
            
            case 'productionmethod':
               
               if( $hasPrints ) {
                  $emulatedcart["production_method"] = $refid;
                  $addedMethod = true;
               }
               break;
               
               
            case 'correctionmethod':
               if( $hasPrints || $hasEnlargements ) {
                  if( $refid == 351 ) {
                     $emulatedcart["correction"] = 1;
                  } else if( $refid == 350 ) {
                     $emulatedcart["correction"] = 2;
                  } else if( $refid == 352 ) {
                     $emulatedcart["correction"] = 3;
                  }
                  $addedMethod = true;
               }
               break;
               
               
            case 'paperquality':
               if( $hasPrints ) {
                  if( $refid == 10 ) { // Supreme
                     $emulatedcart["paper_quality"] = 1;
                  } else if( $refid == 11 ) { // Matt
                     $emulatedcart["paper_quality"] = 3;
                  } else if( $refid == 12 ) { // DP
                     $emulatedcart["paper_quality"] = 2;
                  }
                  $addedMethod = true;
               }
               break;
            default:
               break;
            
         }
         
         if( $addedMethod ) {
            $emulatedcart["ordrelinje"][$refid]["ordredata"]["text"] = $proddata['title'];
            $emulatedcart["ordrelinje"][$refid]["ordredata"]["number"] = $proddata['quantity'];
            $emulatedcart["ordrelinje"][$refid]["ordredata"]["unit_price"] = $proddata["unitprice"];
            $emulatedcart["ordrelinje"][$refid]["ordredata"]["total_price"] = $proddata["price"];
         }
         
         return $emulatedcart;
         
      }
        
      
      
      private function addLicenseFee( $proddata, $oldlicensedata = array() ) {
         
         $licenserefid = Settings::Get( 'license', 'refid' );
         $productoption = ProductOption::fromRefId( $licenserefid );
         
         if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {
         
            if( count( $oldlicensedata ) > 0 ) {
   
               if( count( $proddata['license'] ) > 0 ) {
                  
                  $licenserefid = Settings::Get( 'license', 'refid' );
                  
                  foreach( $proddata['license'] as $imageid => $values ) {
                     
                     $oldlicensedata['unit_price'] += $values['totalfee'];
                     $oldlicensedata['total_price'] += $values['totalfee'];
                     $oldlicensedata['images'][$proddata['optionid']][$imageid] += $values['totalfee'];
                     
                  }
                  
               }
               
               return $oldlicensedata;
               
            } else {
               
               if( count( $proddata['license'] ) > 0 ) {
                  
                  $usedimages = array();
                  $price = 0;
                  
                  foreach( $proddata['license'] as $imageid => $values ) {
                     $price += $values['totalfee'];
                     $usedimages[$imageid] = $values['totalfee'];
                  }
                  
                  return array(
                     'text' => $productoption->title,
                     'number' => 1,
                     'unit_price' => $price,
                     'total_price' => $price,
                     'images' => array( $proddata['optionid'] => $usedimages ),
                  );
               }
               
            }
            
         }
         
      }
      
         
      
      
      /**
       * Creates a valid print order row for EF < 2.8
       *
       * @param string $prodno
       * @param array $proddata
       * @param array $emulatedcart
       * @return array $emulatedcart
       * 
       */
      private function setupPrintOrderRow( $prodno, $proddata, $emulatedcart ) {
         
         if( count( $proddata['license'] ) > 0 ) {
            $licenserefid = Settings::Get( 'license', 'refid' );
            $result = $this->addLicenseFee( $proddata, $emulatedcart["ordrelinje"][$licenserefid]["ordredata"] );
            $emulatedcart["ordrelinje"][$licenserefid]["ordredata"] = $result;
            $emulatedcart['license'][$licenserefid] = $result;
         }
         
         // All images and qty for this product
         $images = $proddata["images"];
         $refid = $proddata["refid"];

         // Product description added to emulated cart
         $emulatedcart["ordrelinje"][$refid]["ordredata"]["text"]          = $proddata['product']["title"];
         $emulatedcart["ordrelinje"][$refid]["ordredata"]["number"]        = $proddata['quantity'];
         $emulatedcart["ordrelinje"][$refid]["ordredata"]["unit_price"]    = $proddata["unitprice"];
         $emulatedcart["ordrelinje"][$refid]["ordredata"]["total_price"]   = $proddata["price"];

         if( count( $images ) > 0 ) {
            foreach( $images as $imageid => $qty ) {
               $emulatedcart["images"][$refid][] = array(
               "antall" => $qty,
               "images" => array(
               $imageid,
               )
               );
   
               // Add the to the lot of EF
               $emulatedcart["ordrelinje"][$refid]["ordredata"]["lot"] []= array(
               $imageid
               );
   
            }
         }
         
         
         // Possible free items to emulated cart
         if( isset( $proddata['credit'] ) ) {
            
            config( 'website.cart' );
            
            // Need this to emulate free items in old cart
            $freeItemRefId = Settings::get( 'cart', 'freeitemrefid' );
            $freeItemRefId = $freeItemRefId['refid'];
            $combinedRefId = $freeItemRefId.'_'.$refid;
            
            $emulatedcart["ordrelinje"][$combinedRefId]["ordredata"]["text"] = $proddata['credit']['text'];
			   $emulatedcart["ordrelinje"][$combinedRefId]["ordredata"]["number"] = ( $proddata['credit']['quantity'] * -1 );
				$emulatedcart["ordrelinje"][$combinedRefId]["ordredata"]["unit_price"] = $proddata['unitprice'];
				$emulatedcart["ordrelinje"][$combinedRefId]["ordredata"]["total_price"] = $proddata['unitprice'] * $emulatedcart["ordrelinje"][$combinedRefId]["ordredata"]["number"];
         
         }
         
         if( isset( $proddata['discount'] ) ) {
            
            config( 'website.cart' );
            
            $discountRefId = Settings::get( 'cart', 'discountrefid' );
            $discountRefId = $discountRefId['refid'];
            
            $discount = $proddata['discount'];
            
         }
         
         return $emulatedcart;
         
      }
      
      /**
       * Setup things in cart that aren't product dependent
       *
       * @param array $cart
       * @param array $emulatedcart
       * 
       */
      private function setupMainCart( $cart, $emulatedcart ) {

         // Get choosen delivery and paymentoptions
         $deliverytype = $cart["deliverytype"]["refid"];
         $paymenttype = $cart["paymenttype"]["refid"];
         
         // All other stuff not yet emulated
         $emulatedcart["weight"]                = $cart["totalweight"];
         $emulatedcart["deliveryinfo"]          = $deliverytype;
         $emulatedcart["paymentinfo"]           = $paymenttype;
         $emulatedcart['deliveryinfo_changed']  = 1;
         $emulatedcart['paymentinfo_changed']  = 1;
         $emulatedcart["version"]               = 2;
         $emulatedcart["price"]                 = $cart["totalprice"];
         $emulatedcart['giftcard']              = $cart['giftcard'];
         
         return $emulatedcart;
         
      }
      
      
      /**
       * Setup the necessary SESSION data for delivery
       *
       */
      private function setDeliveryInfo( $cart ) {
         
         if( Login::isLoggedIn() ) {
            
            $user = new User( Login::userid() );
           
            if( $user->isLoaded() && $user instanceof User ) {
               $tmp = array();
               $tmp["email"] = $user->getUsername();
               $tmp["fname"] = $cart["deliveryinfo"]["firstname"];
               $tmp["ename"] = $cart["deliveryinfo"]["lastname"];
               $tmp["address1"] = $cart["deliveryinfo"]["address"];
               $tmp["pcode"]   = $cart["deliveryinfo"]["zipcode"];
               $tmp["parea"] = $cart["deliveryinfo"]["city"];
               $tmp["country"] = $this->getCountryId( $cart["deliveryinfo"]["country"] );
               
               // Set the info for old cart
               EmulatedCart::setDeliveryInfo( $tmp );
               
            } else {
               
               return false;
               
            }
            
         } else {
            
            $user = User::fromUsernameAndPortal( $cart["deliveryinfo"]["email"], Dispatcher::getPortal() );
            
            if( $user->isLoaded() && $user instanceof User ) {
               
               $tmp = array();
               $tmp["email"] = $user->getUsername();
               $tmp["fname"] = $cart["deliveryinfo"]["firstname"];
               $tmp["ename"] = $cart["deliveryinfo"]["lastname"];
               $tmp["address1"] = $cart["deliveryinfo"]["address"];
               $tmp["pcode"]   = $cart["deliveryinfo"]["zipcode"];
               $tmp["parea"] = $cart["deliveryinfo"]["city"];
               $tmp["country"] = $this->getCountryId( $cart["deliveryinfo"]["country"] );
               
               // Set the info for old cart
               EmulatedCart::setDeliveryInfo( $tmp );
               
               return true;
               
            } else {
               
               //$user = $this->createNewUser();
               //util::Debug( "Need to create new tmpuser" );
               //die();
               
            }
            
         }
         
         
      }
      
      
      
      /**
       * Setup the necessary SESSION data for delivery
       *
       */
      private function setContactInfo( $cart ) {
         
         if( Login::isLoggedIn() ) {
            
            $user = new User( Login::userid() );
            if( $user->isLoaded() && $user instanceof User ) {
               $tmp = array();
               $tmp["email"] = $user->getUsername();
               $tmp["fname"] = $cart["contactinfo"]["firstname"];
               $tmp["ename"] = $cart["contactinfo"]["lastname"];
               $tmp["address1"] = $cart["contactinfo"]["address"];
               $tmp["pcode"]   = $cart["contactinfo"]["zipcode"];
               $tmp["parea"] = $cart["contactinfo"]["city"];
               $tmp["country"] = $this->getCountryId( $cart["contactinfo"]["country"] );
               
               EmulatedCart::setContactInfo( $tmp );
               
            } else {
               
               return false;
               
            }
            
         } else {
            
            $user = User::fromUsernameAndPortal( $cart["contactinfo"]["email"] );
            
            if( $user->isLoaded() && $user instanceof User ) {
               
               $tmp["email"] = $user->getUsername();
               $tmp["fname"] = $cart["contactinfo"]["firstname"];
               $tmp["ename"] = $cart["contactinfo"]["lastname"];
               $tmp["address1"] = $cart["contactinfo"]["address"];
               $tmp["pcode"]   = $cart["contactinfo"]["zipcode"];
               $tmp["parea"] = $cart["contactinfo"]["city"];
               $tmp["country"] = $this->getCountryId( $cart["contactinfo"]["country"] );
               
               EmulatedCart::setContactInfo( $cart );
               
               return true;
               
            } else {
               
               //$user = $this->createNewUser();
               util::Debug( "Need to create new tmpuser" );
               die();
               
            }
            
         }
         
      }
      
      
      /**
       * Get the corresponding country id from a country 2char ISO code
       *
       * @param string $countryCode
       * @return unknown
       */
      private function getCountryId( $countryCode = 'NO' ) {
         
         $countries = Settings::getSection( 'countries' );
         return $countries[$countryCode]['id'];
         
      }
      
   }
      

?>