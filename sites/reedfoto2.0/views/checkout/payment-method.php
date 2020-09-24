<?php

   /**
    * Set delivery method
    * 
    *
    */

   import( "website.cart" );
   config( 'website.stores' );
   
   class CheckoutPaymentMethod extends WebPage implements IView {
      
      protected $template = 'checkout.payment-method';
      
      public function Execute() {
         
         $portal = Dispatcher::getPortal();
         if( is_null($portal) || $portal == "" ) {
            $portal = 'EF-997';
         }
                  
         $deliverymethod = (int)$_POST["delivery-method"];
         
         $cart = new Cart();
         $cartdelivery = $cart->getDeliveryType();
        
         if( !$deliverymethod ){
            $deliverymethod = $cartdelivery['refid'];
         }
         
         if( $portal == 'FC-001' ){   
            $fotoclick = Dispatcher::getCustomAttr( 'fotoclick' );
            $orgstoreid = "Fotoclick" . ":" .$fotoclick['id'];
         }
         else{
            $orgstoreid = isset( $_POST['delivertostore'] ) ? $_POST['delivertostore'] : null;
         }

         if( !is_null( $orgstoreid ) ) {
            $storegroupdata = explode( ':', $orgstoreid );
            $storegroup = reset( $storegroupdata );
            $storeid = end( $storegroupdata );
            $tmpstores = Settings::Get( 'storedelivery', $storegroup );
            $paymentmethodwhitelist = $tmpstores['paymentmethodwhitelist'];
            //$storedeliveryid = Settings::Get( 'storedelivery', 'drefid' );
            $prefid = $tmpstores['prefid'];
         }
         

         
         if( !$deliverymethod > 0 && !$cart->serviceProductsOnly() ) {
            relocate( '/checkout/delivery-method' );
         }
         

         
         // If the cart has other products than services
         if( !$cart->serviceProductsOnly() ) {

            if( !$cart->setDeliveryType( $deliverymethod ) ) {
               relocate( "/checkout/delivery-method" );
            } else {
               $delivertype = $cart->getDeliveryType();
               if( isset( $storeid ) && $delivertype['artnr'] == Settings::Get( 'storedelivery', 'drefid' ) ) {
                  $cart->setStore( $orgstoreid );
               } else {
                  $cart->unsetStore();
               }
               
               $cart->save();
               
            }
            
         }
         
         $options = $this->availablePayment($cart);
         
         $options["payment_options"] = $this->setPresetPaymentOption( $options["payment_options"] );
         
         /*if( count( $options['payment_options'] ) > 0 ) {

            // If user has choosen to deliver to a store then we need to check the whitelist
            // for available paymentmethods.
            if( isset( $storeid ) && ( $storedeliveryid == $deliverymethod ) ) {

               if( count( $paymentmethodwhitelist ) ) {
                  foreach( $options['payment_options'] as $key => $tmppaymentoption ) {
                     if( !in_array( $tmppaymentoption['artnr'], $paymentmethodwhitelist ) ) unset( $options['payment_options'][$key] );
                  }
               }
               
            } else { // If the user has choosen regular delivery then we need to remove payment in store
               foreach ($options['payment_options'] as $key => $tmppaymentoption) {
               	if( $tmppaymentoption['artnr'] == $prefid ) unset( $options['payment_options'][$key] );
               }
            }
            
         }*/
         
         /*if( $options["financedeficit"] ) {
            $this->financedeficit = true;
         }
         */
         if( count( $options["payment_options"] ) == 1 ){            
            $cart->setPaymentType( key( $options["payment_options"] ) );
            $cart->save();
            relocate("/checkout/confirm-order");
         }
         
         $this->paymentoptions = $options["payment_options"];
         
      }
      
      /*
      private function getPaymentWhitelist( $portal ) {
         $tmp = Settings::Get( 'storedelivery', $portal );
         if( isset( $tmp['paymentmethodwhitelist'] ) ) {
            
         }
      }*/
      
      private function availablePayment( $cart ){
         
         $regionid = WebsiteHelper::region();
         $this->cart = $cart->enum();         
         $options = $cart->getDeliveryAndPaymentOptions();
         if( $cart->serviceProductsOnly() ) {
            unset( $options['payment_options'][6] );
            unset( $options['payment_options'][358] );
         }
         
         $tempoptions = array();        
         foreach ( $options['payment_options'] as $id => $option ){
            
             if( !$this->cart['deliverytype']['artnr'] ){
               $tempoptions[$id] = $option;
            }
            else{
               $payment_artnr = DB::query( "SELECT artnr FROM region_payment WHERE paymentid  = ? AND regionid = ?", $option['refid'], $regionid )->fetchSingle();
               
               if( DB::query( "SELECT regionid FROM delivery_payment_map WHERE regionid  = ? AND delivery = ? AND payment = ?", $regionid, $this->cart['deliverytype']['artnr'], $payment_artnr )->fetchSingle() ){
                  $tempoptions[$id] = $option;
               }
            }

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
      
   }


?>