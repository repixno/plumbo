<?php

   import( 'pages.json' );
   config( 'website.countries' );


   class APICheckoutEnumPaymentMethods extends JSONPage implements NoAuthRequired, IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'delivery-method' => VALIDATE_INTEGER,
                  'countryid'       => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      public function Execute() {

         $portal = Dispatcher::getPortal();
         if( is_null($portal) || $portal == "" ) {
            $portal = 'EF-997';
         }
         
         $cart = new Cart();
         $cartdelivery = $cart->getDeliveryType();
         
         $deliverymethod = isset( $_POST['delivery-method'] ) ? $_POST['delivery-method'] : null;
         $countryid = isset( $_POST['countryid'] ) ? $_POST['countryid'] : null;
         
         
         if( !$deliverymethod > 0 && !$cart->serviceProductsOnly() ) {
            $this->result = false;
            $this->message = 'Missing delivery method';
            return false;
         }
         
         // If the cart has other products than services
         if( !$cart->serviceProductsOnly() ) {

            if( !$cart->setDeliveryType( $deliverymethod ) ) {
               relocate( "/checkout_gratis" );
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
         
         $options = $this->availablePayment( $cart, $countryid );
         $options["payment_options"] = $this->setPresetPaymentOption( $options["payment_options"] );
         
         $this->paymentoptions = $options["payment_options"];
         $this->result = true;
         $this->message = 'OK';
         return true;
         
      }
      
      
      /**
       * Get the available payment methods
       *
       * @param array $cart
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function availablePayment( $cart, $countryid ){
         
         if( !isset( $countryid ) ) {
          
            $regionid = WebsiteHelper::region();
              
         } else {
         
            $countries = Settings::GetSection( 'countries' );
            $countryid = $countries[$countryid]['id'];
         
            $regionid = $this->region( $countryid );
            
         }
         
         $tmpcart = $cart->enum();         
         $options = $this->getDeliveryAndPaymentOptions( $regionid, $countryid );
         
         if( $cart->serviceProductsOnly() ) {
            unset( $options['payment_options'][6] );
            unset( $options['payment_options'][358] );
         }
         
         $tempoptions = array();        
         foreach ( $options['payment_options'] as $id => $option ){
            
             if( !$tmpcart['deliverytype']['artnr'] ){
               $tempoptions[$id] = $option;
            }
            else{
               $payment_artnr = DB::query( "SELECT artnr FROM region_payment WHERE paymentid  = ? AND regionid = ?", $option['refid'], $regionid )->fetchSingle();
               
               if( DB::query( "SELECT regionid FROM delivery_payment_map WHERE regionid  = ? AND delivery = ? AND payment = ?", $regionid, $tmpcart['deliverytype']['artnr'], $payment_artnr )->fetchSingle() ){
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
      
      
      
      /**
       * Get the region from given countryid
       *
       * @param unknown_type $countryid
       * @return unknown
       */
      private function region( $countryid ) {
         
         $user = new User( Login::userid() );
         
         $local_regionid= array();
      	$local_regionid[160][0] = 1;
      	$local_regionid[160][1] = 3;

      	if( $user->isLoaded() && $user instanceof User ){

      	   /*if( !$user->country ){
      	      $user->country = Dispatcher::getCustomAttr( 'countryid' );
      		}*/
      		
      		$portalkode = Dispatcher::getPortal();
      		if( !$portalkode ) {
      			$portalkode = "EF-997";
      		}

      		
      		// Need to move this to dispather domainmap
      		// Faster than putting it in db
      		$portalid = Dispatcher::getCustomAttr( 'portalid' );
      		if(!is_numeric($portalid)){
      			
      		   $portalid = 0;
      		   
      		}
      		
      		if ( $regionid = $local_regionid[$countryid][$portalid] ) {	# Hardcoded most requested query, ugly-bugly but db is strained
      			
      		   
      		} else {

      		   $data = DB::query( "
      		      SELECT region.regionid 
      		      FROM region_nations, region 
      		      WHERE region_nations.regionid = region.regionid AND 
      		      nationid=? AND 
      		      portalid=?
      		   ", $countryid, $portalid );
      		   
      		   list( $regionid ) = $data->fetchRow();
      		   
      		   //$data = sql_allExec("egionid=region.regionid and nationid=".$kundedata["country"]." and portalid=$portalid;");
      			//$regionid = $data[0]['regionid'];
      			
      		}
      		
      	} else {
      	   
      	  $countryid = Dispatcher::getCustomAttr( 'countryid' );
      	  $portalcode = Dispatcher::getPortal();
      	  
      	  if( empty( $portalcode ) ) {
      			$portalcode = "EF-997";
      	  }
      	  
      	  // Need to move this to dispather domainmap
      	  // Faster than putting it in db
      	  $portalid = Dispatcher::getCustomAttr( 'portalid' );
      	  if(!is_numeric($portalid)){
      	     $portalid = 0;
      	  }
      	  
      	  if ( $regionid = $local_regionid[$countryid][$portalid] ) {	# Hardcoded most requested query, ugly-bugly but db is strained
      			
      		   
      	  } else {

      	     $data = DB::query( "
      		      SELECT region.regionid 
      		      FROM region_nations, region 
      		      WHERE region_nations.regionid = region.regionid AND 
      		      nationid=? AND 
      		      portalid=?
      		  ", $countryid, $portalid );
      		   
      		  list( $regionid ) = $data->fetchRow();
      		   
      	  }
      	   
      	}
      	
      	$cached_regionid = $regionid;
      	
      	return $regionid;
         
      }
      
      
      /**
       * Get available delivery options from given weight
       *
       * @return array
       * 
       */
      private function getDeliveryOptions( $regid ) {
         
         $res = DB::query( "
            SELECT 
               deliveryid,
               regionid,
               artnr,
               weight,
               name,
               description,
               price
            FROM 
               region_delivery 
            WHERE 
               regionid=?
            ORDER BY artnr,weight", $regid
         );
         
         $deliveryoptions = array();
         
         while( list( $deliveryid, $regionid, $artnr, $weight, $name, $description, $price ) = $res->fetchRow() ) {
            
            $deliveryoptions []= array(
               "deliveryid" => $deliveryid,
               "regionid"  => $regionid,
               "artnr"     => $artnr,
               "weight"    => $weight,
               "name"   => $name,
               "description"  => $description,
               "price"  => round( $price, 2 ),
            );
            
         }
         
         return $deliveryoptions;
         
      }
      
      
      /**
       * Get available paymentoptions depending on users
       * regionid
       *
       * @return array
       */
      private function getPaymentOptions( $regid ) {
         
         $paymentoptions = array();
         
         $res = DB::query( "
            SELECT
               paymentid,
               regionid, 
               artnr,
               name,
               description,
               min_value,
               max_value,
               price
            FROM 
               region_payment
            WHERE
               regionid=?
            ORDER BY
               artnr,
               min_value
         ", $regid );
         
         while( list( $paymentid, $regionid, $artnr, $name, $description, $minvalue, $maxvalue, $price ) = $res->fetchRow() ) {
            
            $paymentoptions []= array(
               "paymentid"    => $paymentid,
               "regionid"     => $regionid,
               "artnr"        => $artnr,
               "name"         => $name,
               "description"  => $description,
               "minvalue"     => $minvalue,
               "maxvalue"     => $maxvalue,
               "price"        => round( $price, 2 ),
            );
            
         }
         
         return $paymentoptions;
         
      }
      
      
      /**
       * Get the available payment and delivery options
       * depending on weight, region and then map them against each other
       * to get the valid options for an order.
       *
       * @return array
       * 
       */
      public function getDeliveryAndPaymentOptions( $regionid, $countryid ) {
         
         $cart = new Cart();
         $tmpcart = $cart->enum();
         
         $weight = $cart->getTotalWeight();
         $price = $cart->getTotalPrice();
         
         $not_updated = 0;
         $deliverydata = $this->getDeliveryOptions( $regionid );
         $paymentdata = $this->getPaymentOptions( $regionid );
         
         $availableDelivery = array();
         $n = count( $deliverydata );
            
         for($i=0;$i<$n;$i++){
               
            if( $deliverydata[$i]["weight"] < $weight){

               $availableDelivery[$deliverydata[$i]["artnr"]] = $deliverydata[$i]["deliveryid"];
                  
            }
               
         }
         

         //Find payment options for this price
         $availablePayment = array();
         $n = count( $paymentdata );
            
         for($i=0;$i<$n;$i++){
            
            if( $paymentdata[$i]["minvalue"] <= $price && $paymentdata[$i]["maxvalue"] >= $price ){
               
               $availablePayment[$paymentdata[$i]["artnr"]] = $paymentdata[$i]["paymentid"];
               
            }

            // Hack to get only creditcard for not logged in purchases
            if( !Login::isLoggedIn() && $paymentdata[$i]["paymentid"] != 78 ) {

               unset( $available_payment[$paymentdata[$i]["artnr"]] );

            }
         }
            
         
         $paymentarts = array_keys( $availablePayment );
         $paymentid = $availablePayment[$paymentarts[0]];
            
         $n = count( $paymentarts );
         for( $i=0; $i<$n; $i++ ) {
               
            if( isset( $paymentarts[$i] ) ) {

               $res = DB::query( "
                  SELECT 
                     regionid,
                     delivery,
                     payment
                  FROM 
                     delivery_payment_map 
                  WHERE 
                     regionid = ? AND
                     payment = ?
               ", $regionid, $paymentarts[$i] );
                  
               while( list( $regid, $delivery, $payment ) = $res->fetchRow() ) {
               
                  $map []= array(
                     "regionid"  => $regid,
                     "delivery"  => $delivery,
                     "payment"   => $payment,
                  );
                  
               }
                  
            }
               
            if( count( $map ) ) {

               $paymentid = $availablePayment[$paymentarts[$i]];
               break;
                  
            }
               
         }
            
         
         $deliveryart = $map[0]["delivery"];
         $deliveryid = $availableDelivery[$deliveryart];
         $not_updated = 1;
         $tmpdata["deliveryinfo"] = $deliveryid;
         $tmpdata["paymentinfo"] = $paymentid;
         
         $tmpcart = $cart->enum();
         
         if($forcecart){
            $tmpcart["deliveryinfo"] = $tmpdata["deliveryinfo"];
            $tmpcart["paymentinfo"] = $tmpdata["paymentinfo"];
         }


         /*
         * Need to think this one through carefully
         */
         /*if( isSubscriptionOnlyOrder( $cart ) ) {

            if( count( $available_payment ) > 0 ) {

               foreach( $available_payment as $key => $values ) {

                  if( $key == 394 || $key  == 393 ) {
                     unset( $available_payment[$key] );
                  }

               }

            }

         }*/


         // Only allow creditcard as payment option in theese cases:
         // Some product in cart tagged as creditcard only or
         // user is not logged in.
         
         if( $cart->creditcard() || $cart->isOnlyStampOrder( true ) ) {

            if( count( $availablePayment ) > 0 ) {
               
               foreach( $availablePayment as $key => $values ) {

                  if( $key == 394 || $key  == 393 ) {
                     unset( $availablePayment[$key] );
                  }

               }

            }

         }
         

         //Store available payment options for the checkout pages
         $payment = array_keys( $availablePayment );
         $n = count( $payment );

         $options["payment_options"] = array();
         $options["delivery_options"] = array();
         
         $showdelivery = array();

         for( $i=0; $i<$n; $i++ ) {
            
            $res = DB::query( "
               SELECT 
                  paymentid,
                  regionid,
                  artnr,
                  name,
                  description,
                  min_value AS minvalue,
                  max_value AS maxvalue,
                  price
               FROM 
                  region_payment 
               WHERE 
                  regionid = ? AND
                  paymentid = ?
            ", $regionid, $availablePayment[$payment[$i]] );
            $data = $res->fetchAssoc();
            
            $res = DB::query( "
               SELECT 
                  regionid,
                  delivery,
                  payment
               FROM 
                  delivery_payment_map 
               WHERE 
                  regionid = ? AND
                  payment = ?
            ", $regionid, $data["artnr"] );
            
            while( list( $regid, $tmpdelivery, $tmppayment ) = $res->fetchRow() ) {
               
               $map []= array(
                  "regionid"  => $regid,
                  "delivery"  => $tmpdelivery,
                  "payment"   => $tmppayment,
               );
               
            }

            if( count( $map ) ) {
               
               if( $tmpdata["paymentinfo"] ==  $availablePayment[$payment[$i]] ) {
                  $paymentoption = PaymentType::fromRefId( $data["paymentid"] );
                  $options["payment_options"][$availablePayment[$payment[$i]]] = array( 
                     "id" => $paymentoption->id,
                     "refid" => $paymentoption->refid,
                     "price" => $paymentoption->getPrice(), 
                     "name" => $paymentoption->getTitle(),
                     "title" => $paymentoption->title,
                     'artnr' => $paymentoption->artnr,
                     #"body" => $paymentoption->body,
                     #"ingress" => $paymentoption->ingress,
                  );
               }
               else{
                  $paymentoption = PaymentType::fromRefId( $data["paymentid"] );
                  $options["payment_options"][$availablePayment[$payment[$i]]] = array( 
                     "id" => $paymentoption->id,
                     "refid" => $paymentoption->refid,
                     "price" => $paymentoption->getPrice(), 
                     "name" => $paymentoption->getTitle(),
                     "title" => $paymentoption->title,
                     'artnr' => $paymentoption->artnr,
                     #"body" => $paymentoption->body,
                     #"ingress" => $paymentoption->ingress,
                  );
               }
               
            }
            
            
            
            $n2 = count( $map );
            for( $a=0; $a < $n2; $a++){
               
               if( $availableDelivery[$map[$a]["delivery"]] ) {
                  $showdelivery[$map[$a]["delivery"]] = $availableDelivery[$map[$a]["delivery"]];
               }
            }
         }
         
         
         $delivery = array_keys( $showdelivery );
         $n = count( $delivery );
         
         for( $i=0; $i < $n; $i++ ) {
            
            $res = DB::query( "
               SELECT 
                  deliveryid,
                  regionid,
                  artnr,
                  weight,
                  name,
                  description,
                  price
               FROM
                  region_delivery
               WHERE
                  regionid = ? AND
                  deliveryid = ?
            ", $regionid, $showdelivery[$delivery[$i]] );
            
            $data = $res->fetchAssoc();
            
            if( $tmpdata["deliveryinfo"] == $showdelivery[$delivery[$i]] ) {
               $deliveryoption = DeliveryType::fromRefId( $data["deliveryid"] );
               $options["delivery_options"][$showdelivery[$delivery[$i]]] = array( 
                  "id" => $deliveryoption->id,
                  "refid" => $deliveryoption->refid,
                  "price" => $deliveryoption->getPrice(), 
                  "name" => $deliveryoption->getTitle(),
                  "title" => $deliveryoption->title,
                  'artnr' => $deliveryoption->artnr,
                  #"body" => $deliveryoption->body,
                  #"ingress" => $deliveryoption->ingress,
               );
            }
            else{
               $deliveryoption = DeliveryType::fromRefId( $data["deliveryid"] );
               $options["delivery_options"][$showdelivery[$delivery[$i]]] = array( 
                  "id" => $deliveryoption->id,
                  "refid" => $deliveryoption->refid,
                  "price" => $deliveryoption->getPrice(), 
                  "name" => $deliveryoption->getTitle(),
                  "title" => $deliveryoption->title,
                  'artnr' => $deliveryoption->artnr,
                  #"body" => $deliveryoption->body,
                  #"ingress" => $deliveryoption->ingress,
               );
            }
            
            
         }
         

         /*
         * If delivery type not changed by user, then we we choose
         * the cheapest means of shipping for user
         */
         if( !$tmpcart['deliveryinfo_changed'] ) { // Set in do_order_change_delivery_method.php

            if( $options["delivery_options"] ) {

               $didkeys = array_keys( $options["delivery_options"] );

               if( $didkeys ) {

                  foreach( $didkeys as $di ) { // Go through all available available methods

                     if( !$lowest ) {

                        $lowest = $di;

                     } else {

                        if( $options["delivery_options"][$lowest]['price'] > $options["delivery_options"][$di]['price'] ) {

                           $lowest = $di;
                        }

                     }

                  }

                  $tmpcart["deliveryinfo"] = $lowest; // Finally, set deliveryinfo to the cheapest one

               }

            }

         }
         
         return $options;
         
      }
      
      
   }


?>