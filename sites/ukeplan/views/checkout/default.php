<?php

   /**
    * Checkout cart. Translate the new cart 
    * to old EF structure.
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.helper' );
   import( 'website.product' );
   import( 'website.cart' );
   import( 'session.usersessionarray' );
   import( 'website.user' );
   config( 'website.countries' );
   config( 'website.stores' );

   class Checkout extends Webpage implements IView {
      
      protected $template = 'checkout.index';
      
      public function Execute() {
         
         if( isset( $_POST['email'] ) ) {
            
            $refUser = User::fromUsernameAndPortal( $_POST['email'], Dispatcher::getPortal() );
            if( $refUser->isLoaded() && $refUser instanceof User ) {
               
               $this->email = $_POST['email'];
               $this->registered = true;
               
            } else {
               
               $this->email = $_POST['email'];
               $this->registered = false;
               
            }
            
         } else {
         
            if( Login::isLoggedIn() ) {
               $user = new User( Login::userid() );
            } else {
               $user = new User();
            }
            
            $this->user = $user->asArray();
            $countries = Settings::getSection( "countries" );
            $countryres = array();
            
            if( count( $countries ) > 0 ) {
               
               foreach( $countries as $iso => $data ) {
                  
                  $countryres[] = array( 
                     "name"   => $data["name"],
                     "iso"    => $iso,
                  );
                  
               }
               
               $this->countries = $countryres;
               
            }
            
            
            
            if( Dispatcher::getPortal() == 'ST-001' ){
               
               model( 'order.leverpostei' );
               
               $cart = new Cart();
               $cartarray = $cart->asArray();
              // Util::Debug( $cartarray['items'] );
              // die();
               
               if( !$cartarray['items'] ){
                  relocate( '/' );
               }
               
               $lokkid =  Session::get( 'stabburet-lokkid' );
               $leverpostei = new DBLeverpostei( $lokkid );
               $this->lokk  = array(
                                     'id' => $leverpostei->id,
                                     'thumbid' => $leverpostei->thumbid,
                                     'imageid' => $leverpostei->imageid,
                                     'imagepos' => $leverpostei->imagepos,
                                     'name' => $leverpostei->name,
                                     'year' => $leverpostei->year,
                                     'malsize' => $leverpostei->malsize,
                                     );

               //$productarray = array( 3328, 3326, 3324, 3322, 3320, 3318,3334 );
               $productarray = array( 3322, 3320, 3318 ,3334, 3326, 3347 );
               $productarray_mobile = array( 3320, 3318 ,3334, 3326 );
               $product = array();
               $product_mobile = array();
               foreach ( $productarray as $ret ){
                  $productcontainer = new Product( $ret );             
                  $product[] = $productcontainer->asArray();
               }
               foreach ( $productarray_mobile as $ret ){
                  $productcontainer = new Product( $ret );             
                  $product_mobile[] = $productcontainer->asArray();
               }
               $this->sections = array(
                  'products' => $product,
                  'products_mobile' => $product_mobile
               );
            
            }
      
            $user = new User( Login::userid() );
            $userdata = $user->asArray();
            $userdata["sms"] = $user->smsServices();
            $efcountries = Settings::getSection( 'efcountries' );
            $userdata["country"] = $efcountries[$this->user["country"]]["2char"];
            $this->user = $userdata;
         }
         
         $this->updateCart( $_POST );
         
         
         /* --------------DELIVERY OPTIONS -------------------*/
         
         $cart = new Cart();
         $cart->removeDeliveryType();
         $cart->save();
         
         $options = $cart->getDeliveryAndPaymentOptions();
         
         $options["delivery_options"] = $this->setPresetDeliveryOption( $options["delivery_options"] );
         
         
         if( count( $options["delivery_options"] ) == 1 ){
            $cart->setDeliveryType( key( $options["delivery_options"] ) );
         }

         // Get the current portal
         $portal = Dispatcher::getPortal();
         if( is_null( $portal ) || $portal == "" ) {
            $portal = 'EF-997';
         }

         // Need to set the stores available
         $groups = $this->getStoreGroups( $portal ); // Get the groups for this portal
         $stores = $this->getStores( $groups ); // Get the stores for theese groups
         $storedeliveryrefid = $this->getStoreDeliveryRefid(); // Get the deliveryrefid for this portal
         
         if( count( $stores ) > 0 ) {
            $this->storechains = $stores;
            $this->storedeliveryrefid = $storedeliveryrefid;
            
         } else {
            $this->storechains = array();
            unset( $options["delivery_options"][$storedeliveryrefid] );
         }
         
         
         // Set the preset deliverytype if only one exists
         /*if( count( $options["delivery_options"] ) == 1 ){
            $cart->setDeliveryType( key( $options["delivery_options"] ) );
            relocate( '/checkout' );
         }*/

        if( $cart->checkfreeshipping() ){ 
            foreach ( $options['delivery_options'] as $key=>$delivery ){  
               $options['delivery_options'][$key]['price'] = '00.00';  
            }
        }
         
        
         
         // Set the template variable
         if( count( $options['delivery_options'] ) > 0 ) {
            $this->deliveryoptions = $options["delivery_options"];
         } else {
            $cart->removeDeliveryType();
            $cart->save();
         }
         
         
         /* --------------PAYMENT OPTIONS--------------- */
         
         $options = $this->availablePayment( $cart );
         


         
         $options["payment_options"] = $this->setPresetPaymentOption( $options["payment_options"] );
         $options["delivery_options"] = $this->setPresetDeliveryOption( $options["delivery_options"] );
         
         $this->paymentoptions = $options["payment_options"];
         
         foreach( $options['delivery_options'] as $id => $option ) {
            if( $option['isPreset'] == true ) $this->presetdeliverycost = $option['price'];
         }
         
         foreach( $options['payment_options'] as $id => $option ) {
            if( $option['isPreset'] == true ) $this->presetpaymentcost = $option['price'];
         }
         
         // If cart only has service products then no delivery method is needed.
         if( $cart->serviceProductsOnly() ) {
            $cart->removeDeliveryType();
            $cart->recalculateTotals();
            $cart->save();
            
            $this->serviceproduct = true;
            
         }
         
         //util::debug( $options );
         //die();
         
         
      }
      
      public function cartUpdate() {
         
         $this->updateCart( $_POST );
         
         $redir = Session::pipe( 'cart_redirecturl');
         
         if($redir != '/cart/mediaclip/accessories'){
            $redir = '/cart/?from=cartupdate';
         }
         relocate( $redir );
         
      }
      
      /**
       * Update the cart with new product quantities
       *
       * @param array $postdata
       */
      private function updateCart( $postdata ) {
         
         if( isset( $postdata['gifts'] ) || isset( $postdata['mediaclip'] ) || isset( $postdata['goods'] ) || isset( $postdata['ukeplan'] ) || isset( $postdata['merkelapp'] )  ) {
            
            $cart = new Cart();
            $cartarray = $cart->asArray();
            
            if( isset( $postdata['gifts'] ) ) {
               
               $gifts = $postdata['gifts'];
               
               foreach( $gifts as $prodno => $products ) {

                  if( count( $products ) ) {
                     
                     foreach( $products as $referenceid => $quantity ) {

                        if( $cartarray['items']['gifts'][$prodno][$referenceid]['quantity'] != $quantity ) {
                           $cart->setItemQuantity( $prodno, $quantity, $referenceid );
                        }
                        
                     }
                     
                  }
                  
               }
               
            }
            
            if( isset( $postdata['mediaclip'] ) ) {
               $mediaclip = $postdata['mediaclip'];
                          
               
               foreach( $mediaclip as $prodno => $products ) {
                  
                  if( count( $products ) ) {
                     
                     foreach( $products as $referenceid => $quantity ) {
               
                       if( $cartarray['items']['mediaclip'][$prodno][$referenceid]['quantity'] != $quantity ) {
                           $cart->setItemQuantity( $prodno, $quantity, $referenceid );
                        }
                        
                       if(strpos($referenceid, 'redeye') > 0){
                          $referenceid = ereg_replace("[^0-9]", "", $referenceid );
                           $cart->setRedeye( $prodno, $referenceid );
                       }
                       
                       
                        
                     }

                  }
               }
               
            }
            
           if( isset( $postdata['ukeplan'] ) ) {
               $ukeplan = $postdata['ukeplan']; 
               foreach( $ukeplan as $prodno => $products ) {
                  
                  if( count( $products ) ) {
                     
                     foreach( $products as $referenceid => $quantity ) {
               
                       if( $cartarray['items']['mediaclip'][$prodno][$referenceid]['quantity'] != $quantity ) {
                           $cart->setItemQuantity( $prodno, $quantity, $referenceid );
                        }
                        
                       if(strpos($referenceid, 'redeye') > 0){
                          $referenceid = ereg_replace("[^0-9]", "", $referenceid );
                           $cart->setRedeye( $prodno, $referenceid );
                       }
                       
                       
                        
                     }

                  }
               }
               
            }
            
           if( isset( $postdata['merkelapp'] ) ) {
               $merkelapp = $postdata['merkelapp']; 
               foreach( $merkelapp as $prodno => $products ) {
                  
                  if( count( $products ) ) {
                     
                     foreach( $products as $referenceid => $quantity ) {
               
                       if( $cartarray['items']['merkelapp'][$prodno][$referenceid]['quantity'] != $quantity ) {
                           $cart->setItemQuantity( $prodno, $quantity, $referenceid );
                        }  
                     }

                  }
               }
               
            }
            
            if( isset( $postdata['goods'] ) ) {
               
               $goods = $postdata['goods'];
               
               foreach( $goods as $prodno => $quantity ) {
                  
                  if( $cartarray['items']['goods'][$prodno]['quantity'] != $quantity ) {
                     
                     if( $quantity > 0 ) {
                        $cart->setItemQuantity( $prodno, $quantity );
                     }
                     
                  }
                  
               }
               
            }
            
            $cart->save();
            $this->cart = $cart->asArray();
            
         }
         
      }
      
      
      /**
       * Get the delivery type with lowest cost as preset
       *
       * @param array $options
       */
      private function setPresetDeliveryOption( $options = array() ) {
         if( count( $options ) > 0 ) {
            
            foreach( $options as $id => $data ) {

               if( !isset( $choosenOption ) ) {
                  $choosenOption = $id;
                 
               } else {
                  
                  $cart = new Cart();
                  $deliverytype = $cart->getDeliveryType();
                  $refid = $deliverytype['refid'];
                  
                  if( $data["price"] < $options[$choosenOption]["price"] && $data['artnr'] != DELIVERYTYPE_STORE ) {
                  //if( $data["price"] < $options[$choosenOption]["price"] ) {
                     $choosenOption = $id;
                  } /*else if( $data['artnr'] == DELIVERYTYPE_STORE && $refid == $data['artnr'] ) {
                      $choosenOption = $id;
                  }*/
               }
               
            }
            
            
            //die();
            
         }
         
         
         
         if( !empty( $choosenOption ) ) {
            $options[$choosenOption]["isPreset"] = true;
         }
         
         return $options;
         
      }
      
      
      private function availablePayment( $cart ){
         
         $regionid = WebsiteHelper::region();
         $tmpcart = $cart->enum();         
         $options = $cart->getDeliveryAndPaymentOptions();
         
         if( $cart->serviceProductsOnly() ) {
            unset( $options['payment_options'][6] );
            unset( $options['payment_options'][358] );
         }
         
         $tempoptions = array();        
         foreach ( $options['payment_options'] as $id => $option ){
            $tempoptions[$id] = $option;
             /*if( !$tmpcart['deliverytype']['artnr'] ){
               $tempoptions[$id] = $option;
            }
            else{
               $payment_artnr = DB::query( "SELECT artnr FROM region_payment WHERE paymentid  = ? AND regionid = ?", $option['refid'], $regionid )->fetchSingle();
               
               if( DB::query( "SELECT regionid FROM delivery_payment_map WHERE regionid  = ? AND delivery = ? AND payment = ?", $regionid, $tmpcart['deliverytype']['artnr'], $payment_artnr )->fetchSingle() ){
                  $tempoptions[$id] = $option;
               }
            }*/

         }
         
         $options['payment_options'] = $tempoptions;
         return $options;
         
      }
      
      
      /**
       * Get the payment type with lowest cost as preset
       *
       * @param array $options
       */
      private function setPresetPaymentOption( $options = array() ) {
         
         if( count( $options ) > 0 ) {
            
            foreach( $options as $id => $data ) {

               if( !isset( $choosenOption ) ) {
                  $choosenOption = $id;
                 
               } else {
                  if( $data["price"] < $options[$choosenOption]["price"] ) {
                     $choosenOption = $id;
                  }
               }
               
            }
            
         }
         
         
         if( !empty( $choosenOption ) ) {
            $options[$choosenOption]["isPreset"] = true;
         }
         
         return $options;
         
      }
      
      
      /**
       * Get the groups registered with this portal
       *
       * @param string $portal
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getStoreGroups( $portal ) {
         
         $groups = array();
         $tmp = Settings::Get( 'storedelivery', 'storegroup' );
         $groups = $tmp[$portal];
         return $groups;
         
      }
      
      /**
       * Get the delivery refid for the current portal
       *
       * @param string $portal
       * @return string
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getStoreDeliveryRefid() {
         
         return Settings::Get( 'storedelivery', 'drefid' );
         
      }
      
      /**
       * Get the array of stores available
       * for delivery.
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getStores( $groups ) {
         
         $stores = array();
         if( count( $groups ) > 0 ) {
            
            foreach( $groups as $group ) {
               
               $tmp = Settings::Get( 'storedelivery', $group );
               $tmp = $tmp['stores'];
               
               foreach( $tmp as $tmpstore ) {
                  
                  if( !isset( $stores[$group] ) ) $stores[$group] = array();
                  $tmpstore['id'] = $group.":".$tmpstore['id'];
                  $newstore = array( 'chain' => $group, 'store' => $tmpstore );
                  $stores[$group]['chain'] = $group;
                  $stores[$group]['stores'] []= $newstore;
                  
               }
               
            }
            
         }
         
         return $stores;
         
      }
      
   }


?>