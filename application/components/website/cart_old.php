<?php
   
   import( 'session.usersessionarray' );
   
   /**
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'website.product' );
   
   class Cart {
      
      //private static $lines = array();
      static function asArray() {
         
         return Cart::enum();
         
      }
      
      
      
      static function resetPrice() {
         
         $cart = UserSessionArray::getItems( "cart" );
         $cart = $cart[0];
         
         $cart["totalprice"] = 0;
         
         UserSessionArray::clearItems( "cart" );
         UserSessionArray::addItem( "cart", $cart );
         
      }
      
      
      
      /**
       * Unset an attribute in the cart
       *
       * @param string $key
       */
      static function removeAttribute( $key ) {
         
         $cart = self::enum();
         
         unset( $cart["items"][$key] );
         UserSessionArray::clearItems( "cart" );
         UserSessionArray::addItem( "cart", $cart );
         
      }
      
      static function addAttribute( $prodno ) {
         
         $prodno = (string) $prodno;
         
         $productoption = ProductOption::fromProdNo( $prodno );
         if( !$productoption->isLoaded() || !$productoption instanceof ProductOption ) return false;
         
         // Specific things for old EF < 3.0
         $tags = $productoption->tags;
         $tags = explode( " ", $tags );
         
         // Remove old production method and add a new one
         if( in_array( "productionmethod", $tags ) ) {
            $productType = "productionmethod";
            $totalQuantity = self::getPrintQuantity();
         }

         // Remove old correction method and add a new one
         if( in_array( "correctionmethod", $tags ) ) {
            $productType = "correctionmethod";
            $totalQuantity = self::getUniqueImagesQuantity();
         }
            
         // Remove an old paper quality and a a new one
         if( in_array( "paperquality", $tags ) ) {
            $productType = "paperquality";
            $totalQuantity = self::getPrintQuantity();
         }
         
         // Dont add attribute if there are no images
         if( $totalQuantity > 0 ) {
         
            $product = new Product( $productoption->productid );
               
            // Get the saved cart content
            $cart = UserSessionArray::getItems( 'cart' );
            $cart = $cart[0];
            
            $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
            $totalProductPrice = ( $totalQuantity * $unitPrice );
            
            unset( $cart["items"][$productType] );
            
            // Add data unique for this product
            $cart["items"][$productType]['title'] = $product->title;
            $cart["items"][$productType]['prodno'] = $prodno;
            $cart["items"][$productType]['quantity'] = $totalQuantity;
            $cart["items"][$productType]['unitprice'] = $unitPrice;
            $cart["items"][$productType]['price'] = $totalProductPrice;
            $cart["items"][$productType]['refid'] = $productoption->refid;
            $cart["items"][$productType]['body'] = $product->body;
            $cart["items"][$productType]['ingress'] = $product->ingress;
            
            // Add data fro whole cart
            $cart["totalprice"] += $totalProductPrice;//self::getTotalPrice( $cart );
               
            // Save the new cart
            UserSessionArray::clearItems( "cart" );
            UserSessionArray::addItem( "cart", $cart );
         
         }
         
      }
      
      
      static function addItem( $prodno = '', $quantity = 0, $attributes = array() ) {

         $prodno = (string) $prodno;
         $quantity = (int) $quantity;
         $totalPrice = 0;
         
         try {
            
            $productoption = ProductOption::fromProdNo( $prodno );
            if( !$productoption->isLoaded() || !$productoption instanceof ProductOption ) {

               throw new Exception( "Product doesn't exist" );
               
            }
            
            // Specific things for old EF < 3.0
            $tags = $productoption->tags;
            $tags = explode( " ", $tags );
            
            
            if( in_array( "print", $tags ) ) {
               $productType = "prints";
            }
            if( in_array( "enlargement", $tags ) ) {
               $productType = "enlargements";
            }
            
            $product = new Product( $productoption->productid );
            
            // Get the saved cart content
            $cart = UserSessionArray::getItems( 'cart' );
            $cart = $cart[0];
            
            
            // Add number of unique items to cart
            $totalQuantity = (int) $cart["items"][$productType][$prodno]['quantity'] + (int) $quantity;
            $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
            $totalProductPrice = ( $totalQuantity * $unitPrice );
            
            
            $cart["items"][$productType][$prodno]['title'] = $product->title;
            $cart["items"][$productType][$prodno]['quantity'] = $totalQuantity;
            $cart["items"][$productType][$prodno]['unitprice'] = $unitPrice;
            $cart["items"][$productType][$prodno]['price'] = $totalProductPrice;
            $cart["items"][$productType][$prodno]['unitweight'] = $productoption->getUnitWeight();
            $cart["items"][$productType][$prodno]['weight'] = ( $cart["items"][$productType][$prodno]['unitweight'] * $cart["items"][$productType][$prodno]['quantity'] );
            $cart["items"][$productType][$prodno]['refid'] = $productoption->refid;
            $cart["items"][$productType][$prodno]['body'] = $product->body;
            $cart["items"][$productType][$prodno]['ingress'] = $product->ingress;
            
            switch( $productType ) {
               
               case 'prints': // Merge images if necessary
                  $cart = self::setupImages( $cart, $productType, $prodno, $attributes );
                  break;
               case 'enlargements': // Merge images if necessary
                  $cart = self::setupImages( $cart, $productType, $prodno, $attributes );
                  break;
               default:
                  break;
               
            }
            
            $cart["totalprice"] += $totalProductPrice;//self::getTotalPrice();
            $cart["totalitems"] += 1;//self::getTotalItems();
            $cart["totalweight"] += $cart["items"][$productType][$prodno]['weight'];//self::getTotalWeight();
            
            
            UserSessionArray::clearItems( "cart" );
            UserSessionArray::addItem( "cart", $cart );
            
            
         } catch( Exception $e ) {
            
            return array( "result" => false, "message" => $e->getMessage() );
            
         }
         
         return count( $cart );
         
      }
      
      
      /**
       * Get the total number of prints ordered
       *
       * @return integer
       */
      static function getPrintQuantity() {
         
         $tmpcart = self::enum();
         $totalPrintQuantity = 0;
         
         if( count( $tmpcart["items"]["prints"] ) > 0 ) {
            
            foreach( $tmpcart["items"]["prints"] as $product ) {

               $totalPrintQuantity += $product["quantity"];      
               
            }
            
         }
         
         
         return $totalPrintQuantity;
         
      }
      
      
      static function getUniqueImagesQuantity() {
         
         $tmpcart = self::enum();
         $totalImageQuantity = 0;
         $images = array();
         
         
         if( count( $tmpcart["items"]["prints"] ) > 0 ) {
            
            foreach( $tmpcart["items"]["prints"] as $product ) {

               if( count( $product["images"] ) > 0 ) {
                  
                  foreach( $product["images"] as $imageid => $quantity ) {
                     
                     $images[$imageid] = 1;
                     
                  }
                  
               }
               
            }
            
         }
         
         if( count( $tmpcart["items"]["enlargements"] ) > 0 ) {
            
            foreach( $tmpcart["items"]["enlargements"] as $product ) {

               if( count( $product["images"] ) > 0 ) {
                  
                  foreach( $product["images"] as $imageid => $quantity ) {
                     
                     $images[$imageid] = 1;
                     
                  }
                  
               }
               
            }
            
         }
         
         return count( $images );
         
      }
      
      
      static function setupImages( $cart, $productType, $prodno, $attributes ) {
         
         if( isset( $cart["items"][$productType][$prodno]['images'] ) ) {

            if( isset( $cart["items"][$productType][$prodno]["images"] ) && isset( $attributes["images"] ) ) {

               foreach( $attributes["images"] as $key => $value ) {

                  if( isset( $cart["items"][$productType][$prodno]["images"][$key] ) ) {
                     $cart["items"][$productType][$prodno]["images"][$key] += $attributes["images"][$key];
                  } else {
                     $cart["items"][$productType][$prodno]["images"][$key] = $attributes["images"][$key];
                  }

               }

            }

         } else {

            $cart["items"][$productType][$prodno]['images'] = $attributes["images"];

         }
         
         return $cart;
         
      }
      
      
      
      /**
       * Return the total price of all products in cart
       *
       * @return float
       * 
       */
      static function getTotalPrice() {
         
         $cart = self::enum();
         $totalPrice = 0;

         if( count( $cart["items"] ) > 0 ) {

            foreach( $cart["items"] as $productType => $data ) {
                  
               if( count( $cart["items"][$productType] ) > 0 ) {

                  foreach( $cart["items"][$productType] as $prodno => $proddata ) {
                     
                     $totalPrice += $$proddata["price"];
                     
                  }
                  
               }
               
            }
            
         }
         
         return $totalPrice;
            
      }
      
      
      static function getTotalItems() {
         
         $cart = self::enum();
         $totalItems = 0;
         
         if( count( $cart["items"] ) > 0 ) {
            
            foreach( $cart["items"] as $productType => $data ) {
               
               if( $productType != "productionmethod" && $productType != "correctionmethod" && $productType != "paperquality" ) {
                  
                  if( count( $cart["items"][$productType] ) > 0 ) {
                     
                     foreach( $cart["items"][$productType] as $prodno => $productdata ) {
                        
                        $totalItems++;
                        
                     }
                     
                  }
                  
               }
               
            }
            
         }
         
         return $totalItems;
         
      }
      
      
      /**
       * Get the total weight of items in cart
       *
       * @param array $cart
       * @return float
       * 
       */
      static function getTotalWeight() {
         
         $cart = self::enum();
         return $cart["totalweight"];
         
      }
      
      
      /**
       * Get available delivery options from given weight
       *
       * @param float $weight
       * @return array
       * 
       */
      static function getDeliveryOptions() {
         
         $regid = WebsiteHelper::region();
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
               "price"  => $price,
            );
            
         }
         
         return $deliveryoptions;
         
      }
      

      /**
       * Get available paymentoptions depending on users
       * regionid
       *
       * @param integer $regid
       * @return array
       */
      static function getPaymentOptions( $regid = 1 ) {
         
         import( 'website.paymenttype' );
         import( 'website.deliverytype' );
         
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
               "price"        => $price,
            );
            
         }
         
         return $paymentoptions;
         
      }
      
      
      /**
       * Get the available payment and delivery options
       * depending on weight, region and then map them against each other
       * to get the valid options for an order.
       *
       * @param array $cart
       * @param float $weight
       * @param float $price
       * @return array
       */
      static function getDeliveryAndPaymentOptions() {
         
         $cart = self::enum();
         $weight = self::getTotalWeight();
         $price = self::getTotalPrice();
         
         $not_updated = 0;
         $regionid = WebsiteHelper::region();
         $deliverydata = self::getDeliveryOptions();
         $paymentdata = self::getPaymentOptions( $regionid );
         
         $availableDelivery = array();
         $n = count($deliverydata);
            
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
         
         if($forcecart){
            $cart["deliveryinfo"] = $tmpdata["deliveryinfo"];
            $cart["paymentinfo"] = $tmpdata["paymentinfo"];
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


         
         if( !Login::isLoggedIn() ) {

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
                  $options["payment_options"][$availablePayment[$payment[$i]]] = array( "price" => $paymentoption->getPrice(), "name" => $paymentoption->getTitle() );
               }
               else{
                  $paymentoption = PaymentType::fromRefId( $data["paymentid"] );
                  $options["payment_options"][$availablePayment[$payment[$i]]] = array( "price" => $paymentoption->getPrice(), "name" => $paymentoption->getTitle() );
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
               $options["delivery_options"][$showdelivery[$delivery[$i]]] = array( "price" => $deliveryoption->getPrice(), "name" => $deliveryoption->getTitle() );
            }
            else{
               $deliveryoption = DeliveryType::fromRefId( $data["deliveryid"] );
               $options["delivery_options"][$showdelivery[$delivery[$i]]] = array( "price" => $deliveryoption->getPrice(), "name" => $deliveryoption->getTitle() );
            }
            
            
         }
         

         /*
         * If delivery type not changed by user, then we we choose
         * the cheapest means of shipping for user
         */
         if( !$cart['deliveryinfo_changed'] ) { // Set in do_order_change_delivery_method.php

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

                  $cart["deliveryinfo"] = $lowest; // Finally, set deliveryinfo to the cheapest one

               }

            }

         }
         
         return $options;
         
      }
      
      
      
      static function count() {
         
         $count = 0;
         
         $cart = UserSessionArray::getItems( 'cart' );
         
         foreach( $cart as $prodno => $product ) {
            $count += $product['quantity'];
            
         }
         return $count;
         
      }
      
      /**
       * Remove a product from cart
       *
       * @param integer $prodno
       */
      static function removeItem( $prodno = '' ) {
         
         $prodno = (string) $prodno;
         $removedPrice = 0.00;
         $cart = self::enum();
         
         
         if( count( $cart["items"] ) ) {
            
            foreach( $cart["items"] as $producttype => $products ) {

               if( isset( $cart["items"][$producttype][$prodno] ) ) {
                  
                  unset( $cart["items"][$producttype][$prodno] );
                  
               }
               
               
               // Need to remove all production methods etc if cart
               // doesn't have any more prnt products.
               if( $producttype == 'prints' ) {
                  
                  if( count( $cart["items"]["prints"] ) == 0 ) {

                     unset( $cart["items"]["prints"] );
                     
                     if( isset( $cart["items"]["productionmethod"] ) ) {   
                        
                        unset( $cart["items"]["productionmethod"] );
                        
                     }
                     
                     if( isset( $cart["items"]["correctionmethod"] ) ) {
                        
                        unset( $cart["items"]["correctionmethod"] );   
                        
                     }
                     
                     if( isset( $cart["items"]["paperquality"] ) ) {

                        unset( $cart["items"]["paperquality"] );
                        
                     }
                     
                  }
                  
               }
               
            }
            
            
            self::save( $cart );
            
            $cart = self::recalculatePrintMethods( $cart );
            $cart["totalprice"] = self::recalculateTotalPrice();
            $cart["totalitems"] = self::getTotalItems();
            $cart["totalweight"] = self::getTotalWeight();
         
            // Empty old cart and save the new one
            self::save( $cart );
            
         }
         
      }
      
      
      
      static function recalculatePrintMethods( $cart ) {
         
         $printitems = $cart["items"]["prints"];
         
         if( count( $printitems ) == 0 ) {
            
            unset( $cart["items"]["productionmethod"] );
            unset( $cart["items"]["correctionmethod"] );
            unset( $cart["items"]["paperquality"] );
            return $cart;
               
         } else {
            
            
            if( isset( $cart["items"]["productionmethod"] ) ) {
               
               $productionmethod = $cart["items"]["productionmethod"];
               $prodno = $productionmethod["prodno"];
               
               unset( $cart["items"]["productionmethod"] );
               self::save( $cart );
               self::removeAttribute( 'productionmethod' );
               self::addAttribute( $prodno );
               $cart = self::enum();
               
            }
            
            if( isset( $cart["items"]["correctionmethod"] ) ) {
               
               $method = $cart["items"]["correctionmethod"];
               $prodno = $method["prodno"];
               
               unset( $cart["items"]["correctionmethod"] );
               self::save( $cart );
               self::removeAttribute( 'correctionmethod' );
               self::addAttribute( $prodno );
               $cart = self::enum();
               
            }
            
            if( isset( $cart["items"]["paperquality"] ) ) {
               
               $method = $cart["items"]["paperquality"];
               $prodno = $method["prodno"];
               
               unset( $cart["items"]["paperquality"] );
               self::save( $cart );
               self::removeAttribute( 'paperquality' );
               self::addAttribute( $prodno );
               $cart = self::enum();
               
            }
            
         }
         
         util::Debug( $cart );
         
      }
      
      
      
      
      /**
       * Recalculate the cart price
       *
       * @return float
       * 
       */
      static function recalculateTotalPrice() {

         $cart = self::enum();
         $totalPrice = 0.00;
         $otherPrice = 0.00;
         
         if( count( $cart["items"] ) > 0 ) {
            
            $items = $cart["items"];
            
            foreach( $items as $producttype => $products ) {

               if( !in_array( $producttype, array( "productionmethod", "correctionmethod", "paperquality" ) ) ) {
                  
                  if( count( $products ) > 0 ) {
                  
                     foreach( $products as $prodno => $proddata ) {
                     
                       if( isset( $proddata["price"] ) ) {
                        
                           $totalPrice = $totalPrice + $proddata["price"];
                        }
                     
                     }
                  
                  }
                  
               } else {
                  
                  $totalPrice += $items[$producttype]["price"];
                  
               }
               
            }
            
         }

         return $totalPrice;        
         
      }
      
      
      
      static function save( $cart ) {
         
         //UserSessionArray::clearItems( "cart" );
         self::clear();
         UserSessionArray::addItem( "cart", $cart );
         
      }
      
      
      /**
       * Return the lines of cart
       *
       */
      static function enum() {
      
         $cart = UserSessionArray::getItems( 'cart' );
         return $cart[0];
         
      }
      
      
      
      static function clear() {
         
         UserSessionArray::clearItems( 'cart' );
         
      }
      
      
      /**
       * Get the number of prints currently in cart
       * 
       * @return integer
       *
       */
      static function getImageQuantity() {
         
         $cart = UserSessionArray::getItems( "cart" );
         $cart = reset( $cart );
         $totalQuantity = 0;
         
         if( isset( $cart["items"]["prints"] ) && count( $cart["items"]["prints"] ) > 0 ) {
            
            foreach( $cart["items"]["prints"] as $image => $data ) {
               
               if( isset( $data["images"] ) && count( $data["images"] ) ) {
                  
                  foreach( $data["images"] as $qty ) {
                     
                     $totalQuantity += $qty;
                     
                  }
                  
               }
               
            }
            
         }
         
         return $totalQuantity;
         
      }
      
      
      /**
       * Set the contact info of this order
       *
       * @param array $contact
       */
      static function setContactInfo( $contact = array() ) {
         
         $cart = self::enum();
         $cart["contactinfo"] = $contact;
         UserSessionArray::clearItems( 'cart' );
         UserSessionArray::addItem( 'cart', $cart );
         
      }
      
      
      /**
       * Set the delivery address of order
       *
       * @param array $delivery
       */
      static function setDeliveryInfo( $delivery = array() ) {
         
         $cart = self::enum();
         $cart["deliveryinfo"] = $delivery;
         UserSessionArray::clearItems( 'cart' );
         UserSessionArray::addItem( 'cart', $cart );
         
      }
      
      
      /**
       * Set the choosen delivery type by user
       *
       * @param integer $id
       * @return boolean
       * 
       */
      static function setDeliveryType( $id ) {

         import( 'website.deliverytype' );
         $deliverytype = DeliveryType::fromRefId( $id );
         if( !$deliverytype->isLoaded() || !$deliverytype instanceof DeliveryType ) return false;
         
         $cart = self::enum();
         $cart["deliverytype"] = $deliverytype->asArray();
         
         UserSessionArray::clearItems( "cart" );
         UserSessionArray::addItem( "cart", $cart );
         
         return true;
         
      }
      
      
      /**
       * Return the choosen delivery type by user
       *
       * @return array
       * 
       */
      static function getDeliveryType() {
         
         $cart = Cart::enum();
         return $cart["deliverytype"];
         
      }
      
      
      /**
       * Set the choosen payment type by user
       *
       * @param integer $id
       * @return boolean
       * 
       */
      static function setPaymentType( $id ) {

         import( 'website.paymenttype' );
         $paymenttype = PaymentType::fromRefId( $id );
         if( !$paymenttype->isLoaded() || !$paymenttype instanceof PaymentType ) return false;
         
         $cart = self::enum();
         $cart["paymenttype"] = $paymenttype->asArray();
         
         UserSessionArray::clearItems( "cart" );
         UserSessionArray::addItem( "cart", $cart );
         
         return true;
         
      }
      
      
      /**
       * Return the choosen payment type by user
       *
       * @return array
       * 
       */
      static function getPaymentType() {
         
         $cart = Cart::enum();
         return $cart["paymenttype"];
         
      }
      
   }
   
   
   
   
?>