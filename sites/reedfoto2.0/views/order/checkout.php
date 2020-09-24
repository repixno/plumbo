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
      
      protected $template = 'order.checkout';
      
      public function Execute() {
         
         Util::setSSL();
         
         $this->identifiervalue = $_SESSION['identifiervalue'];
         
         Util::Debug( $this->identifiervalue );
         
         /* --------------DELIVERY OPTIONS -------------------*/
         
         $cart = new Cart();
        
        if( Dispatcher::getPortal() == 'RF-002' ){
            //creditcar 78
            //$paymentmethod = 1;
            //$deliverymethod = 1;
            
            $cartarray = $cart->asArray();
            
            
            
            
            if( $cartarray['cartprice'] == 0 ){
               $paymentmethod = 1;
               $deliverymethod = 1;
               $price = 0;
            }else{
               //creditcard 78
               //faktura 692
               $paymentmethod = 692;
               $deliverymethod = 1;
               $this->showpayment = 1;
            }
            $cart->setTrackingcode( $this->identifiervalue );
            $cart->setDeliveryType( $deliverymethod, 0 );
            $cart->setPaymentType( $paymentmethod );
            
            foreach( $cartarray['items'] as $key=>$item ){
               if( $key != 'digital' ){
                  $cart->setDeliveryType( $deliverymethod, 49 );
                  break;
               }
            }
            

         }
         
         
         $cart->save();

         
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
            unset( $options['payment_options'][473] );
            unset( $options['payment_options'][474] );
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