<?php

   /**
    * Cart container
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'session.usersessionarray' );
   import( 'website.product' );
   import( 'website.paymenttype' );
   import( 'website.deliverytype' );
   import( 'website.basket' );
   import( 'website.credit' );
   import( 'website.discounthistory' );
   import( 'website.discount.cart' );
   import( 'website.price' );
   import( 'website.order.giftcard' );
   
   class Cart {
      
      private $totalItems = 0;
      private $totalWeight = 0.00;
      private $totalPrice = 0.00;
      private $cartPrice = 0.00;
      
      private $items = array();
      private $productionmethod = array();
      private $correctionmethod = array();
      private $paperquality = array();
      
      private $contactInfo = array();
      private $deliveryInfo = array();
      
      private $deliveryType;
      private $paymentType;
      private $comment;
      // Array of prodno that are in cart
      // and that are only purchasable by creditcard
      private $creditCardOnly = array();
      
      private $freeShippingArray = array();
      
      // Contains prodno of services in cart
      private $services = array();
      
      private $discount = array();
      
      private $version = 2;
      
      private $credits = array();
      
      private $giftcard;
      
      private $storeid;
      
      private $klarnaid;
      
      private $trackingcode;
      
      /**
       * Load and setup the cart object
       *
       */
      public function __construct() {
         
         // Load cart
         $cart = UserSessionArray::getItem( 'cart', 0 );
         
         // reset a flag
         $cartnotfoundandnotloaded = false;
         
         // if we're logged in, try restoring from database
         if( Login::isLoggedIn() && ( isset( $_GET['reloadbasket'] ) || !isset( $cart['totalitems'] ) || $cart['totalitems'] == 0 ) ) {
            
            try {
               
               $basket = new Basket( Login::userid() );
               if( $basket instanceof Basket && $basket->isLoaded() ) {
                  
                  /*
                  if( Login::userid() == 444047 ) {
                     
                     util::Debug( $basket );
                     die();
                     
                  }
                  */
                  $cart = $basket->basket;
                  if( !isset( $cart['items'] ) ) {
                     
                     $cart = array();
                     $cartnotfoundandnotloaded = true;
                     
                  }
                  
               }
               
            } catch( Exception $e ) {
               
               $cart = array();
               
            }
            
         }
         
         // set cart from session data
         if( isset( $cart["items"] ) ) {
            $this->items = $cart["items"];
         }
         
         // Methods and quality for print production
         if( isset( $this->items["productionmethod"] ) ) {
            $this->productionmethod = $this->items["productionmethod"];
         }
         if( isset( $this->items["correctionnmethod"] ) ) {
            $this->correctionmethod = $this->items["correctionmethod"];
         }
         if( isset( $this->items["paperquality"] ) ) {
            $this->paperquality = $this->items["paperquality"];
         }
         
         
         // Info about user and shipping
         if( isset( $cart["deliveryinfo"] ) ) {
            $this->deliveryInfo = $cart["deliveryinfo"];
         }
         if( isset( $cart["contactinfo"] ) ) {
            $this->contactInfo = $cart["contactinfo"];
         }
         
         
         // Shipping and handling
         if( isset( $cart["deliverytype"] ) ) {
            $this->deliveryType = $cart["deliverytype"];
         }
         if( isset( $cart["paymenttype"] ) ) {
            $this->paymentType = $cart["paymenttype"];
         }
         
         
         // Price, Weight and tot. Items
         if( isset( $cart["totalprice"] ) ) {
            $this->totalPrice = $cart["totalprice"];
         }
         if( isset( $cart["cartprice"] ) ) {
            $this->cartPrice = $cart["cartprice"];
         }
         if( isset( $cart["totalitems"] ) ) {
            $this->totalItems = $cart["totalitems"];
         }
         if( isset( $cart["totalweight"] ) ) {
            $this->totalWeight = $cart["totalweight"];
         }
         
         // Only purchase with creditcard
         if( isset( $cart["creditcardonly"] ) ) {
            $this->creditCardOnly= $cart["creditcardonly"];
         }
         
         if( isset( $cart["freeshipping"] ) ) {
            $this->freeShippingArray = $cart["freeshipping"];
         }
         
         // Services in cart
         if( isset( $cart["services"] ) ) {
            $this->services = $cart["services"];
         }
         
         // Products that use discount in cart
         if( isset( $cart["discount"] ) ) {
            $this->discount = $cart["discount"];
         }
         
         if( isset( $cart['credits'] ) ) {
            $this->credits = $cart['credits'];
         }
         if( isset( $cart['klarnaid'] ) ) {
            $this->klarnaid = $cart['klarnaid'];
         }
         // Products that use discount in cart
         if( isset( $cart['version'] ) ) {
            $this->version = $cart['version'];
         }
         
         if( isset( $cart["comment"] ) ) {
            $this->comment = $cart["comment"];
         }
         
         if( isset( $cart["trackingcode"] ) ) {
            $this->trackingcode = $cart["trackingcode"];
         }
         
         if( isset( $cart['giftcard'] ) ) $this->giftcard = $cart['giftcard'];
         
         if( isset( $cart['storeid'] ) ) $this->storeid = $cart['storeid'];
         
         $this->validate();
         
         if( $cartnotfoundandnotloaded ) {
            
            $this->save();
            
         }
         
      }
      
      private function validateCart() {
         
         if( count( $this->items ) > 0 ) {
            
            $allprodnos = array();
            foreach( $this->items as $group => $items ) {
               
               foreach( $items as $key=>$product ) {
                  
               	if( !isset( $product['prodno'] ) ) continue;
                  $allprodnos[$product['prodno']] = true;
                  
                  /*foreach( $product as $kake=>$ret ){
                     if( !$ret['prodno'] ){
                        unset( $this->items[$group][$key][$kake] );
                     }
                  }*/
                  
                  if( $group == 'prints'){
                     if( $product['quantity'] == 0 ) {
                        unset($this->items[$group][$key]);
                        
                        $this->save();
                        
                     }
                     
                  }
                  
                  

                  
                  try{
                     if( is_array( $product ) ){
                        $productoption = new ProductOption( $product['optionid'] );
                        $realprice = $productoption->getPrice( $product['quantity'] );
                        if( $product['unitprice']  != $realprice ){
                           $this->items[$group][$key]['unitprice'] = $realprice;
                           $this->items[$group][$key]['price'] = $product['quantity'] * $realprice ;
                           $this->recalculateTotals();
                        }
                     }
                  }catch( Exception $e ){
                     mail( 'tor.inge@eurofoto.no', "feil med oppdatering av pris" , $e->getMessage() );
                  }
               }
            }
            
            foreach( $this->creditCardOnly as $prodno ) {
               if( !isset( $allprodnos[$prodno] ) ) {
                  unset( $this->creditCardOnly[array_search($prodno, $this->creditCardOnly)] );
               }
            }
            
            foreach( $this->services as $prodno ) {
               if( !isset( $allprodnos[$prodno] ) ) {
                  unset( $this->services[array_search($prodno, $this->services)] );
               }
            }
            
         }


      }
      
      /**
       * Return an array representation of the cart
       *
       * @return array
       * 
       */
      public function asArray() {
         
         $this->validateCart();
         
         return array(
            'items'           => $this->items,
            'deliverytype'    => $this->deliveryType,
            'paymenttype'     => $this->paymentType,
            'contactinfo'     => $this->contactInfo,
            'deliveryinfo'    => $this->deliveryInfo,
            'totalprice'      => round( $this->totalPrice, 2 ),
            'cartprice'       => round( $this->cartPrice, 2 ),
            'totalweight'     => round( $this->totalWeight, 3 ),
            'totalitems'      => (int) $this->totalItems,
            'creditcardonly'  => $this->creditCardOnly,
            'freeshipping'    => $this->freeShippingArray,
            'services'        => $this->services,
            'discount'        => $this->discount,
            'credits'         => $this->credits,
            'giftcard'        => $this->giftcard,
            'comment'         => $this->comment,
            'storeid'         => $this->storeid,
            'klarnaid'        => $this->klarnaid,
            'trackingcode'    => $this->trackingcode
         );
         
      }
      
      
      public function enum() {
         
         return $this->asArray();
         
      }
      
      
      public function updateBasket() {
         
         if( Login::isLoggedIn() ) {
            
            try {
               
               $basket = new Basket( Login::userid() );
               if( $basket instanceof Basket && $basket->isLoaded() ) {
                  $basket->basket = $this->asArray();
                  $basket->save();
               }
               
            } catch( Exception $e ) {
               
               $basket = new Basket();
               $basket->uid = Login::userid();
               $basket->basket = $this->asArray();
               $basket->save();
               
            }
            
         }
         
      }
      
      /**
       * Store cart in user session
       *
       */
      public function save() {
         
         UserSessionArray::clearItems( 'cart' );
         UserSessionArray::addItem( 'cart', $this->asArray() );
         
         $this->updateBasket();
         
      }
      
      
      /**
       * Completly clear the cart of everything
       *
       */
      public function clear() {
         
         unset( $this->totalItems );
         unset( $this->totalWeight );
         unset( $this->totalPrice );
         unset( $this->cartPrice );
         unset( $this->items );
         unset( $this->productionmethod );
         unset( $this->correctionmethod );
         unset( $this->paperquality );
         unset( $this->contactInfo );
         unset( $this->deliveryInfo );
         unset( $this->deliveryType );
         unset( $this->paymentType );
         unset( $this->prints );
         unset( $this->enlargements );
         unset( $this->discount );
         unset( $this->services );
         unset( $this->creditCardOnly );
         unset( $this->freeShippingArray );
         unset( $this->credits );
         unset( $this->klarnaid );
         unset( $this->giftcard );
         unset( $this->storeid);
         
         UserSessionArray::clearItems( "cart" );
         
         $this->updateBasket();
         
      }
      
      
      /**
       * Enumerate the actual products in the cart
       *
       * @return array
       * 
       */
      public function enumItems() {
         
         return $this->items;
         
      }
      
      
      /**
       * Sets cart delivery info
       *
       * @param array $info
       * 
       */
      public function setDeliveryInfo( $info ) {
         
         $this->deliveryInfo = $info;
         
      }
      
      
      public function setContactInfo( $info ) {
         
         $this->contactInfo = $info;
         
      }
      
      public function setKlarnaid( $klarnaid ){
         $this->klarnaid = $klarnaid;
      }
      
      
      /**
       * Add a print method
       *
       * @param string $prodno
       */
      public function addPrintAttribute( $prodno ) {
         
         $prodno = (string) $prodno;
         
         $productoption = ProductOption::fromProdNo( $prodno );
         if( !$productoption->isLoaded() || !$productoption instanceof ProductOption ) return false;
         
         // Specific things for old EF < 3.0
         $tags = $productoption->tags;
         $tags = explode( " ", $tags );
         
         // Remove old production method and add a new one
         if( in_array( "productionmethod", $tags ) ) {
            $productType = "productionmethod";
            $totalQuantity = $this->getPrintQuantity();
         }

         // Remove old correction method and add a new one
         if( in_array( "correctionmethod", $tags ) ) {
            $productType = "correctionmethod";
            $totalQuantity = $this->getUniqueImagesQuantity();
         }
            
         // Remove an old paper quality and a a new one
         if( in_array( "paperquality", $tags ) ) {
            $productType = "paperquality";
            $totalQuantity = $this->getPrintQuantity();
         }

         
         // Dont add attribute if there are no images
         if( $totalQuantity > 0 ) {
         
            $product = new Product( $productoption->productid );
            $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
            $totalProductPrice = ( $totalQuantity * $unitPrice );
            
            unset( $this->items[$productType] );
            
            // Add data unique for this product
            $this->items[$productType]['title'] = $product->title;
            $this->items[$productType]['prodno'] = $prodno;
            $this->items[$productType]['quantity'] = $totalQuantity;
            $this->items[$productType]['unitprice'] = $unitPrice;
            $this->items[$productType]['price'] = round( $totalProductPrice, 2 );
            $this->items[$productType]['refid'] = $productoption->refid;
            $this->items[$productType]['product'] = $product->asArray();
            
            // Add data fro whole cart
            //$this->totalPrice += $totalProductPrice;
            //$this->cartPrice += $totalProductPrice;
            
            $this->recalculateTotals();
               
         }
         
      }
      
      
      /**
       * Get the type of product from tags
       * 
       * @param string
       * @return string
       *
       */
      private function getProductType( $tags = '' ) {
         
         // Specific things for old EF < 3.0
         // Get productoption tag to differ
         // between different types of products
         //$tags = $productoption->tags;
         $tags = explode( " ", $tags );
         $productType = '';
          
         if( in_array( 'print', $tags ) ) {
            $productType = 'prints';
         }
         if( in_array( 'enlargement', $tags ) ) {
            $productType = 'enlargements';
         }
         if( in_array( 'gift', $tags ) ) {
            $productType = 'gifts';
         }
         if( in_array( 'ukeplan', $tags ) ){
            $productType = 'ukeplan';
         }
         if( in_array( 'mediaclip', $tags ) ) {
            $productType = 'mediaclip';
         }
         if( in_array( 'subscription', $tags ) ) {
            $productType = 'subscription';
         }
         if( in_array( 'goods', $tags ) ) {
            $productType = 'goods';
         }
         if( in_array( 'merkelapp', $tags ) ) {
            $productType = 'merkelapp';
         }
         if( in_array( 'stempel', $tags ) ) {
            $productType = 'stempel';
         }
         if( in_array( 'smilesontiles', $tags ) ) {
            $productType = 'smilesontiles';
         }
         if( in_array( 'textgift', $tags ) ) {
            $productType = 'textgift';
         }
         if( in_array( 'module', $tags ) ) {
            $productType = 'module';
         }
         if( in_array( 'digital', $tags ) ) {
            $productType = 'digital';
         }
         return $productType;
         
      }
      
      
      
      /**
       * Check if product tagged as creditcard only
       * 
       * @param string
       * @return boolean
       *
       */
      private function creditcardProductOption( $tags = '' ) {
         
         $tags = explode( ' ', $tags );
         
         if( in_array( 'creditcardonly', $tags ) ) {
            return true;
         }
         
         return false;
         
      }
      
      
       /**
       * Check if product tagged as freeshipping
       * 
       * @param string
       * @return boolean
       *
       */
      private function freeshipping( $tags = '' ) {
         
         $tags = explode( ' ', $tags );
         
         if( in_array( 'freeshipping', $tags ) ) {
            return true;
         }
         
         return false;
         
      }
      
      
       /**
       * Check if product tagged as service
       * 
       * @param string
       * @return boolean
       *
       */
      private function serviceProductOption( $tags = '' ) {
         
         $tags = explode( " ", $tags );
         
         if( in_array( "service", $tags ) ) {
            return true;
         }
         
         return false;
         
      }
      
      
      
      /**
       * If any prodnos in this array
       * then the cart can only be purchased
       * with creditcart
       *
       * @return boolean
       */
      public function creditcard() {
         
         if( count( $this->creditCardOnly ) > 0 ) {
            return true;
         }
         
         return false;
         
      }
      
      public function checkFreeShipping(){
         
          if( count( $this->freeShippingArray ) > 0 ) {
            return true;
         }
         
         return false;
         
      }
      
      
      
      public function updateCartDiscount() {
         
         $discount = CartDiscount::getLatestActivatedDiscount();
         if( count( $this->items ) > 0 ) {
            
            $items = $this->items;
            foreach( $items as $productType => $products ) {

               switch( $productType ) {
                  
                  case 'prints':
                     
                     if( count( $products ) > 0 ) {
                           
                        foreach( $products as $product ) {
                           
                           $discounts = CartDiscount::getActivatedDiscounts( Login::userid(), $product['refid'] );
                           $unitPrice = ProductOption::priceFromProdNo( $product['prodno'], $product['quantity'] );
                           $pricedata = CartDiscount::unitPrice( $discounts, $product['quantity'], $unitPrice );
                           
                           if( $pricedata['changed'] ) {
                              $unitPrice = $pricedata['unitprice'];

                              $this->discount[$product['prodno']] = array( 
                                 'quantity' => $product['quantity'],
                                 'discount' => $discounts,
                                 'pricedata' => $pricedata,
                              );
                              
                              $oldPrice = $this->items['prints'][$product['prodno']]['price'];
                              $this->totalPrice -= $oldPrice;
                              $this->cartPrice -= $oldPrice;
                              $this->items['prints'][$product['prodno']]['unitprice'] = round( $unitPrice, 2 );
                              $this->items['prints'][$product['prodno']]['price'] = $unitPrice * $product['quantity'];
                              
                              $this->items['prints'][$product['prodno']]['pricedata'] = $pricedata;
                              $this->totalPrice += ( $unitPrice * $product['quantity'] );
                              $this->cartPrice += ( $unitPrice * $product['quantity'] );
                              
                              if( is_array( $discounts ) && count( $discounts ) > 0 ) {
                                 $this->items[$productType][$prodno]['discount'] = $discounts[0];
                                 if( isset( $this->discount[$productoption->prodno] ) ) {
                                    $this->items[$productType][$prodno]['discount']['pricedata'] = $this->discount[$productoption->prodno]['pricedata'];
                                 }
                              }
                              
                              unset( $discounts );
                              unset( $unitPrice );
                              unset( $pricedata );
                              
                           }
                           
                        }
                        
                     }
                  
               }
               
            }
            
         }
         
         
      }
      
      
      /**
       * Add an item to cart
       *
       * @param string $prodno
       * @param integer $quantity
       * @param array $attributes
       * @return array
       */
      public function addItem( $prodno = '', $quantity = 0, $attributes = array() ) {

         $productoption = ProductOption::fromProdNo( (string) $prodno );
         return $this->addItemByProductOptionId( $productoption->id, $quantity, $attributes );
         
      }

      
      
      /**
       * Add an item to cart from given product option id
       *
       * @param integer $productoptionid
       * @param integer $quantity
       * @param array $attributes
       * @return array
       */
      public function addItemByProductOptionId( $productoptionid, $quantity = 1, $attributes = array() ) {

         $quantity = (int) $quantity;
         $totalPrice = 0;
         
         // add the productoptionid to the attributes collection
         $attributes['productoptionid'] = $productoptionid;
         
         try {

            $productoption = new ProductOption( $productoptionid );
            if( !$productoption->isLoaded() || !$productoption instanceof ProductOption ) {

               throw new Exception( "Product doesn't exist" );
               
            }
            
            
            // Specific things for old EF < 3.0
            // Get productoption tag to differ
            // between different types of products
            $productType = $this->getProductType( $productoption->tags );
            $prodno = $productoption->prodno;
            
            $product = new Product( $productoption->productid );
            
            

            if( $this->freeshipping( $productoption->tags ) ){
               $this->freeShippingArray[$prodno] = $prodno;
            }            
            
            // Need to set cart as creditcard only if such a product is added
            if( $this->creditcardProductOption( $productoption->tags ) ) {
                $this->creditCardOnly[] = $prodno;
            }
            
            // Add prodno to services if such a productoption
            if( $this->serviceProductOption( $productoption->tags ) ) {
               $this->services[] = $prodno;
            }
            
            // Check if user has any free items credited
            if( Login::isLoggedIn() ) {
               $credit = Credit::getCreditByUserIdAndRefId( Login::userid(), $productoption->refid );
               $discounts = CartDiscount::getActivatedDiscounts( Login::userid(), $productoption->refid );
            }
            
            // Custom text title support
            // Overrides regular product title
            if( isset( $attributes["title"] ) ) {
               $usertitle = $attributes["title"];
               $title = $product->title;
            } else {
               $title = $product->title;
            }

            switch( $productType ) {
               
               case 'prints': // Merge images if necessary
                  // Add number of unique items to cart
                  $totalQuantity = (int) $this->items[$productType][$prodno]['quantity'] + (int) $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
                  
                  //$this->totalPrice -= $this->items[$productType][$prodno]['price'];
                  //$this->cartPrice -= $this->items[$productType][$prodno]['price'];
                  $totalProductPrice = ( $totalQuantity * $unitPrice );

                  $this->setupImages( $productType, $prodno, $attributes );
                  
                  if( !isset( $this->items[$productType][$prodno]['quantity'] ) ) {
                     $this->totalItems += 1;
                  }
                  
                  $this->items[$productType][$prodno]['prodno'] = $productoption->prodno;
                  $this->items[$productType][$prodno]['optionid'] = $productoptionid;
                  $this->items[$productType][$prodno]['quantity'] = $totalQuantity;
                  $this->items[$productType][$prodno]['unitprice'] = $unitPrice;
                  $this->items[$productType][$prodno]['price'] = round( $totalProductPrice, 2 );
                  $this->items[$productType][$prodno]['unitweight'] = round( $productoption->getUnitWeight(), 3 );
                  $this->items[$productType][$prodno]['weight'] = round( ( $this->items[$productType][$productoption->prodno]['unitweight'] * $this->items[$productType][$productoption->prodno]['quantity'] ), 3 );
                  $this->items[$productType][$prodno]['refid'] = $productoption->refid;
                  $this->items[$productType][$prodno]['currentproductoption'] = $productoption->asArray();
                  $this->items[$productType][$prodno]['product'] = $product->asArray();
                  
                  
                  
                  if( $this->items[$productType][$prodno]['license'] ){
                     $this->items[$productType][$prodno]['license'] = array_merge(  $this->items[$productType][$prodno]['license'], $this->checkLicenses( $productType, $prodno, $attributes ) );
                  }else{
                     $this->items[$productType][$prodno]['license'] = $this->checkLicenses( $productType, $prodno, $attributes );
                  }
                  
                  
                  
                  
                  if( $this->serviceProductOption( $productoption->tags ) ) {
                     $this->items[$productType][$prodno]['service'] = true;
                  }
                  
                  // Update the total price for product
                  /*if( isset( $this->items[$productType][$prodno]['credit']['price'] ) ) {
                     $totalProductPrice -= $this->items[$productType][$prodno]['credit']['price'];
                  }*/
                  
                  break;
               case 'enlargements': // Merge images if necessary
               
                  // Add number of unique items to cart
                  $totalQuantity = (int) $this->items[$productType][$prodno]['quantity'] + (int) $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
                  
                  /*$pricedata = CartDiscount::unitPrice( $discounts, $quantity, $unitPrice );
                  if( $pricedata['changed'] ) {
                     $unitPrice = $pricedata['unitprice'];
                     $this->discount[$productoption->prodno] = array( 
                        'quantity' => $totalQuantity,
                        'discount' => $discounts,
                     ); 
                  }*/

                  //$this->totalPrice -= $this->items[$productType][$prodno]['price'];
                  //$this->cartPrice -= $this->items[$productType][$prodno]['price'];
                  $totalProductPrice = ( $totalQuantity * $unitPrice );
               
                  $this->setupImages( $productType, $prodno, $attributes );
                  
                  if( !isset( $this->items[$productType][$prodno]['quantity'] ) ) {
                     $this->totalItems += 1;
                  }
                  
                  $this->items[$productType][$prodno]['prodno'] = $prodno;
                  $this->items[$productType][$prodno]['optionid'] = $productoptionid;
                  $this->items[$productType][$prodno]['quantity'] = $totalQuantity;
                  $this->items[$productType][$prodno]['unitprice'] = $unitPrice;
                  $this->items[$productType][$prodno]['price'] = round( $totalProductPrice, 2 );
                  $this->items[$productType][$prodno]['unitweight'] = round( $productoption->getUnitWeight(), 3 );
                  $this->items[$productType][$prodno]['weight'] = round( ( $productoption->getUnitWeight() * $totalQuantity ), 3 );
                  $this->items[$productType][$prodno]['refid'] = $productoption->refid;
                  $this->items[$productType][$prodno]['currentproductoption'] = $productoption->asArray();
                  $this->items[$productType][$prodno]['product'] = $product->asArray();
                  $this->items[$productType][$prodno]['license'] = $this->checkLicenses( $productType, $prodno, $attributes );
                  
                  if( $this->serviceProductOption( $productoption->tags ) ) {
                     $this->items[$productType][$prodno]['service'] = true;
                  }
                  
                  break;
                  
               case 'gifts': // Products ordered through old gift editor
                  import( 'website.giftordertemplate' );
                  import( 'website.gifttemplate' );
                  model( 'order.leverpostei' );
                  // The reference id in db
                  $templateref = $attributes["templateorderid"];
                  
                  $templateOrder = new GiftOrderTemplate($templateref);
                  $image = new Image($templateOrder->bid);
                  $gifttemplate = new GiftTemplate( $templateOrder->malid );
                  
                  $totalQuantity = $this->getTotalItemQuantity( $productType, $prodno );
                  $totalQuantity += $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
                  
                  $this->updateItemPrices( $unitPrice, $productType, $prodno );
                  
                  $totalProductPrice = ( $quantity * $unitPrice );
                  
                  $this->items[$productType][$prodno][$templateref]['prodno'] = $prodno;
                  $this->items[$productType][$prodno][$templateref]['optionid'] = $productoptionid;
                  $this->items[$productType][$prodno][$templateref]['referenceid'] = $templateref;
                  $this->items[$productType][$prodno][$templateref]['quantity'] = $quantity;
                  $this->items[$productType][$prodno][$templateref]['unitprice'] = $unitPrice;
                  $this->items[$productType][$prodno][$templateref]['price'] = round( $totalProductPrice, 2 );
                  $this->items[$productType][$prodno][$templateref]['unitweight'] = round( $productoption->getUnitWeight(), 3 );
                  $this->items[$productType][$prodno][$templateref]['weight'] = round( ( $productoption->getUnitWeight() * $quantity ), 3 );
                  $this->items[$productType][$prodno][$templateref]['refid'] = $productoption->refid;
                  $this->items[$productType][$prodno][$templateref]['currentproductoption'] = $productoption->asArray();
                  $this->items[$productType][$prodno][$templateref]['product'] = $product->asArray();
                  $this->items[$productType][$prodno][$templateref]['license'] = $this->checkLicenses( $productType, $prodno, array( 'referenceid' => $templateref, 'quantity' => $quantity ) );
                  $this->items[$productType][$prodno][$templateref]['image'] = $image->getThumbnail();
                  $this->items[$productType][$prodno][$templateref]['malid'] = $gifttemplate->asArray();
                  
                  if( $this->serviceProductOption( $productoption->tags ) ) {
                     $this->items[$productType][$prodno][$templateref]['service'] = true;
                  }
                  
                  if( $attributes["name"] && $attributes["font"] ) {
                     $this->items[$productType][$prodno][$templateref]['attributes'] = array(
                           'name'   => $attributes["name"] ,
                           'font'   => $attributes["font"]
                        );
                  }
                  
                  if( $attributes["fontcolor"] ) {
                     
                     $this->items[$productType][$prodno][$templateref]['attributes']['fontcolor'] = $attributes["fontcolor"];
                     
                  }
                  
                  
                  // Product to be treated with red eye removal
                  if( isset( $attributes["redeyeremoval"] ) && $attributes["redeyeremoval"] == true ) {
                     
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                     
                     if( $rerProductOption->isLoaded() ) {
                        
                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$templateref]['redeyeremoval'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $rerProductOption->price, 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                        
                        // Add price to total price
                        //$this->totalPrice += $rerProductOption->price;
                        //$this->cartPrice += $rerProductOption->price;
                        
                     }
                     
                  }
                    
                  if(  isset( $attributes["upgrade"] )  ){
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'upgrade' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                     
                     if( $rerProductOption->isLoaded() ) {
                        
                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$templateref]['upgrade'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $attributes["upgrade"], 2 ),
                           'unitprice' => round( $attributes["upgrade"], 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                     }
                  }
                                       
   		  if( isset( $attributes["varnish"] ) && $attributes["varnish"] == true ){
                     config( 'website.cart' );
   		     $rerRefId = Settings::get( 'cart', 'varnish' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
   					
                     $totalProductPrice = ( $quantity * $rerProductOption->price );
                       
   		     if( $rerProductOption->isLoaded() ) {
                           
                           // Setup varnish product. Unique per product
                           $rerProduct = new Product( $rerProductOption->productid );
                           $this->items[$productType][$prodno][$templateref]['varnish'] = array(
                              'prodno' => $rerProductOption->prodno,
                              'optionid' => $rerProductOption->id,
                              'refid' => $rerProductOption->refid,
                              'unitprice' => $rerProductOption->price,
                              'price' => round( $totalProductPrice, 2 ),
                              'quantity' => $quantity,
                              'product' => $rerProduct->asArray(),
                           );
                           
                           // Add price to total price
                           //$this->totalPrice += $rerProductOption->price;
                           //$this->cartPrice += $rerProductOption->price;
                           
                        }
   					
   				  }
                  break;
               
               
                 
              case 'ukeplan':
                  
                  $templateid = $attributes["templateorderid"];
                  $projectid = $attributes["projectid"]; 
                  $totalQuantity = $this->getTotalItemQuantity( $productType, $prodno );
                  $totalQuantity += $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
                  
                  $this->updateItemPrices( $unitPrice, $productType, $prodno );
                  
                  $totalProductPrice = ( $quantity * $unitPrice );
               
                  
                  $this->items[$productType][$prodno][$projectid] = array(
                     'templateid' => $templateid,
                     'usertitle' => $usertitle,
                     'optionid' => $productoptionid,
                     'prodno' => $prodno,
                     'referenceid' => $projectid,
                     'quantity' => $quantity,
                     'unitprice' => $unitPrice,
                     'price' => round( $totalProductPrice, 2 ),
                     'unitweight' => round( $productoption->getUnitWeight(), 3 ),
                     'weight' => round( ( $productoption->getUnitWeight() * $quantity ), 3 ),
                     'refid' => $productoption->refid,
                     'currentproductoption' => $productoption->asArray(),
                     'product' => $product->asArray(),
                     'license' => $this->checkLicenses( $productType, $prodno, array( 'referenceid' => $projectid, 'quantity' => $quantity ) )
                  );
                  
                 
                  // Product to be treated with maskit option
                  if( isset( $attributes["maskit"] ) && $attributes["maskit"] == true ) {
                      
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'maskit' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                     
                     if( $rerProductOption->isLoaded() ) {
                        
                        // Setup maskit product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$projectid]['maskit'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $rerProductOption->price, 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                        
                        //file_put_contents( '/var/www/ukeplan/testorderincartiver.txt', "kakakka ->" . serialize($this->items[$productType][$prodno][$templateref]['maskit']) );
                        
                     }
                     
                  }
               
               
               break;
               
               case 'merkelapp':
               case 'stempel':
                  
                  $projectid = $attributes["projectid"];
                  
                  $color = $attributes["color"] ;

                  $totalQuantity = $this->getTotalItemQuantity( $productType, $prodno );
                  $totalQuantity += $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
                  
                  $this->updateItemPrices( $unitPrice, $productType, $prodno );
                  
                  $totalProductPrice = ( $quantity * $unitPrice );
               
                  
                  $this->items[$productType][$prodno][$projectid] = array(
                     'usertitle' => $usertitle,
                     'optionid' => $productoptionid,
                     'prodno' => $prodno,
                     'referenceid' => $projectid,
                     'color' => $color,
                     'quantity' => $quantity,
                     'unitprice' => $unitPrice,
                     'price' => round( $totalProductPrice, 2 ),
                     'unitweight' => round( $productoption->getUnitWeight(), 3 ),
                     'weight' => round( ( $productoption->getUnitWeight() * $quantity ), 3 ),
                     'refid' => $productoption->refid,
                     'currentproductoption' => $productoption->asArray(),
                     'product' => $product->asArray(),
                     'license' => $this->checkLicenses( $productType, $prodno, array( 'referenceid' => $projectid, 'quantity' => $quantity ) )
                  );
                  
               break;
            
            
               case 'smilesontiles':
                  
                  $projectid = $attributes["projectid"];
                  
                  
                  $totalQuantity = $this->getTotalItemQuantity( $productType, $prodno );
                  $totalQuantity += $quantity;
                  $unitPrice = $attributes["price"];
                  
                  //$this->updateItemPrices( $unitPrice, $productType, $prodno );
                  
                  $totalProductPrice = ( $quantity * $unitPrice );
               

                  $this->items[$productType][$prodno][$projectid] = array(
                     'usertitle' => $usertitle,
                     'optionid' => $productoptionid,
                     'prodno' => $prodno,
                     'referenceid' => $projectid,
                     'quantity' => $quantity,
                     'unitprice' => $unitPrice,
                     'price' => round( $totalProductPrice, 2 ),
                     'unitweight' => round( $productoption->getUnitWeight(), 3 ),
                     'weight' => round( ( $productoption->getUnitWeight() * $quantity ), 3 ),
                     'refid' => $productoption->refid,
                     'currentproductoption' => $productoption->asArray(),
                     'product' => $product->asArray(),
                     'projectinfo' => $attributes,
                  );
                  
               break;
            
               case 'textgift':
               case 'module':
               case 'digital':
                  
                  $projectid = count( $this->items[$productType][$prodno] ) + 1;
                  $usertitle = $attributes["name"];
                  
                  $totalQuantity = $this->getTotalItemQuantity( $productType, $prodno );
                  $totalQuantity += $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );
                  $this->updateItemPrices( $unitPrice, $productType, $prodno );
                  $totalProductPrice = ( $quantity * $unitPrice );
                  $this->items[$productType][$prodno][$projectid] = array(
                     'usertitle' => $usertitle,
                     'optionid' => $productoptionid,
                     'prodno' => $prodno,
                     'referenceid' => $projectid,
                     'quantity' => $quantity,
                     'unitprice' => $unitPrice,
                     'price' => round( $totalProductPrice, 2 ),
                     'unitweight' => round( $productoption->getUnitWeight(), 3 ),
                     'weight' => round( ( $productoption->getUnitWeight() * $quantity ), 3 ),
                     'refid' => $productoption->refid,
                     'currentproductoption' => $productoption->asArray(),
                     'product' => $product->asArray(),
                     'attributes' => $attributes,
                  );
                  
               break;
                
               case 'mediaclip':  // Products ordered through mediaclip
                  
                  $projectid = $attributes["projectid"];
                  
                  // Recalculate total quantity
                  $totalQuantity = $this->getTotalItemQuantity( $productType, $prodno );
                  $totalQuantity += $quantity;
                  
                  $unitPrice = ProductOption::priceFromProdNo( $prodno, $totalQuantity );

                  // Update the unit and total price for all items under this product
                  $this->updateItemPrices( $unitPrice, $productType, $prodno );
                  
                  $totalProductPrice = ( $quantity * $unitPrice );
                  
                  $usertitle = explode( '<splitter>', $usertitle );
                  $usertitle = reset( $usertitle );
                  
                  
                  if( date('Y-m-d') > '2016-01-01' && date('Y-m-d') < '2016-06-01' && $this->items['ukeplan'] && !$this->items['mediaclip']  ){
                     //GDSKILTMAI2016  endra til store sia alle andre i db er stor bokstav på
                     //$this->addCartDiscount ( 564 );
                     $unitPrice = 0;
                     $totalProductPrice = 0;
                  }
                  
                  
                  $this->items[$productType][$prodno][$projectid] = array(
                     'usertitle' => $usertitle,
                     'optionid' => $productoptionid,
                     'prodno' => $prodno,
                     'referenceid' => $projectid,
                     'quantity' => $quantity,
                     'unitprice' => $unitPrice,
                     'price' => round( $totalProductPrice, 2 ),
                     'unitweight' => round( $productoption->getUnitWeight(), 3 ),
                     'weight' => round( ( $productoption->getUnitWeight() * $quantity ), 3 ),
                     'refid' => $productoption->refid,
                     'currentproductoption' => $productoption->asArray(),
                     'product' => $product->asArray(),
                     'license' => $this->checkLicenses( $productType, $prodno, array( 'referenceid' => $projectid, 'quantity' => $quantity ) )
                  );
                  
                  
                  if( $this->serviceProductOption( $productoption->tags ) ) {
                     $this->items[$productType][$prodno][$projectid]['service'] = true;
                  }
                  
                  // Setup data for extra pages
                  if( isset( $attributes["extrapages"] ) && $attributes["extrapages"] > 0 ) {
                     
                     import( 'website.mediaclipproductoption' );
                     $mcpo = MediaclipProductOption::fromProdNo( $prodno );
                     $extraPagesRefId = $mcpo->extraPagesRefId();
                     
                     // If the product option is loaded 
                     // Load the product
                     $epProductOption = ProductOption::fromRefId( $extraPagesRefId );
                     if( $epProductOption->isLoaded() && $epProductOption instanceof ProductOption  ) {
                        
                        $epProduct = new Product( $epProductOption->productid );
                        $epProductTotalExtraPages = $quantity * $attributes["extrapages"];
                        $epUnitPrice = ProductOption::priceFromProdNo( $epProductOption->prodno, $epProductTotalExtraPages );
                        $totalProductPrice += $epUnitPrice * $epProductTotalExtraPages;
                        
                        $this->items[$productType][$prodno][$projectid]['extrapages'] = array(
                        
                           'prodno' => $epProductOption->prodno,
                           'optionid' => $epProductOption->id,
                           'refid' => $epProductOption->refid,
                           'uniquequantity' => $attributes["extrapages"],
                           'quantity' => ( $epProductTotalExtraPages ),
                           'unitprice' => $epUnitPrice,
                           'price' => round( $epUnitPrice * $epProductTotalExtraPages, 2 ),
                           'unitweight' => round( $epProductOption->getUnitWeight(), 3 ),
                           'weight' => round( ( $epProductOption->getUnitWeight() * $epProductTotalExtraPages ), 3 ),
                           'product' => $epProduct->asArray()
                        
                        
                        );
                        
                     }
                     
                  }
                  if( $attributes["redeyeremoval"] == true ) {
                     
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                     
                     if( $rerProductOption->isLoaded() ) {
                        
                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$projectid]['redeyeremoval'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $rerProductOption->price, 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                     }
                     
                  }
                  
                  
                  break;
               case 'goods':
                  
                  if( $attributes['set'] == true ) {
                     // Add sets to cart 
                     $totalQuantity = (int) $this->items[$productType][$productoption->prodno]['quantity'] + (int) $quantity;
                     $unitPrice = ProductOption::priceFromProdNo( $productoption->prodno, ((int)$attributes['totalitemquantity'] * $quantity ) ) * $attributes['setitemquantity'];
                     $this->items[$productType][$prodno]['set'] = array(
                        'setitemquantity' => $attributes['setitemquantity'],
                        'totalitemquantity' => $attributes['totalitemquantity'],
                        'type' => $attributes['type'],
                     );
                     
                  } else {
                     // Add number of unique items to cart
                     $totalQuantity = (int) $this->items[$productType][$productoption->prodno]['quantity'] + (int) $quantity;
                     $unitPrice = ProductOption::priceFromProdNo( $productoption->prodno, $totalQuantity );
                  }
                  $totalProductPrice = ( $totalQuantity * $unitPrice );
                  
                  $this->items[$productType][$prodno]['usertitle'] = $usertitle;
                  $this->items[$productType][$prodno]['prodno'] = $productoption->prodno;
                  $this->items[$productType][$prodno]['optionid'] = $productoptionid;
                  $this->items[$productType][$prodno]['quantity'] = $totalQuantity;
                  $this->items[$productType][$prodno]['unitprice'] = $unitPrice;
                  $this->items[$productType][$prodno]['price'] = round( $totalProductPrice, 2 );
                  $this->items[$productType][$prodno]['unitweight'] = round( $productoption->getUnitWeight(), 3 );
                  
                  // Is this a giftcard?
                  $tags = explode( " ", $productoption->tags );
                  if( in_array( 'giftcard', $tags ) ) {
                     $this->items[$productType][$prodno]['giftcard'] = array();
                  }
                  
                  if( $attributes['set'] == true ) {
                     $this->items[$productType][$prodno]['weight'] = round( ( $this->items[$productType][$productoption->prodno]['unitweight'] * $this->items[$productType][$productoption->prodno]['set']['totalitemquantity'] ), 3 );
                  } else {
                     $this->items[$productType][$prodno]['weight'] = round( ( $this->items[$productType][$productoption->prodno]['unitweight'] * $this->items[$productType][$productoption->prodno]['quantity'] ), 3 );
                  }
                  $this->items[$productType][$prodno]['refid'] = $productoption->refid;
                  $this->items[$productType][$prodno]['currentproductoption'] = $productoption->asArray();
                  $this->items[$productType][$prodno]['product'] = $product->asArray();

                  break;
                  
               case 'subscription':

                  // Add number of unique items to cart
                  $totalQuantity = 1;//(int) $this->items[$productType][$productoption->prodno]['quantity'] + (int) $quantity;
                  $unitPrice = ProductOption::priceFromProdNo( $productoption->prodno, $totalQuantity );
                  $totalProductPrice = ( $totalQuantity * $unitPrice );
                  
                  $this->items[$productType][$prodno]['prodno'] = $productoption->prodno;
                  $this->items[$productType][$prodno]['optionid'] = $productoptionid;
                  $this->items[$productType][$prodno]['quantity'] = $totalQuantity;
                  $this->items[$productType][$prodno]['unitprice'] = $unitPrice;
                  $this->items[$productType][$prodno]['price'] = round( $totalProductPrice, 2 );
                  $this->items[$productType][$prodno]['refid'] = $productoption->refid;
                  $this->items[$productType][$prodno]['currentproductoption'] = $productoption->asArray();
                  $this->items[$productType][$prodno]['product'] = $product->asArray();
                  break;
               default:
                  break;
               
            }
                             
          
            
            if( isset( $this->discount['info']['id'] ) ) {
               $this->discount = $this->addCartDiscount( $this->discount['info'] );
            }
            
            $this->recalculateTotals();
            
            
         } catch( Exception $e ) {

            return array( "result" => false, "message" => $e->getMessage() );
            
         }
         
      }
      
      
      
      /**
       * Check license of product
       *
       * @param string $producttype
       * @param string $prodno
       * @param array $attributes
       * @return array
       */
      private function checkLicenses( $producttype, $prodno, $attributes = array() ) {
         
         config( 'website.license' );
         $licenserefid = Settings::Get( 'license', 'refid' );
         $rflicense = Settings::Get( 'license' , 'reedfoto' );
         $rfgrouplicense = Settings::Get( 'license' , 'reedfotogroups' );
         
         $reedfoto = 1;
         
         $group  = DB::query( 'SELECT grouptype FROM article WHERE artnr = ?' ,   $prodno )->fetchSingle();
 
         $licensefee =  $rflicense[$prodno];
         
         if( !$licensefee ){
           $licensefee = $rfgrouplicense[$group]; 
         }
         
         switch( $producttype ) {
            case 'prints':
            case 'enlargements':
               
               $licenseproductoption = ProductOption::fromRefId( $licenserefid );
               $licenseproduct = new Product( $licenseproductoption->productid );
               $totalfee = 0.00;
               $images = array();
                              
               foreach( $attributes['images'] as $key => $quantity ) {
                  
                  
                  if( is_integer($key) ){
                  
                     $image = new Image( $key );
                     
                     if( !$licensefee ){
                        $licensefee = $image->licensefee;
                     }
                     
                     if( $image->licensefee > 0 ) {
                        $images[$image->bid] = array(
                                                     'product' => $licenseproduct->asArray(),
                                                     'type' => $image->licensetype,
                                                     'unitfee' => $licensefee,
                                                     'quantity' => $quantity,
                                                     'totalfee' => ( $licensefee * $quantity ),
                                                     'imageid'  => $image->bid
                                                     );
                        /*$images[$image->bid] = array(
                                                     'id' => $image->bid,
                                                     'product' => $licenseproduct->asArray(),
                                                     'type' => $image->licensetype,
                                                     'unitfee' => $image->licensefee,
                                                     'totalfee' => ( $image->licensefee ) );*/
                     }
                  }
               }
               
               return $images;
               break;
            case 'gifts':
               $giftorder = new GiftOrderTemplate( $attributes['referenceid'] );
               $imageid = $giftorder->imageid;
               $image = new Image( $imageid );
               
               if( !$licensefee ){
                     $licensefee = $image->licensefee;
                  }
               
               $licenseimages = array();
               
               if( $image->licensefee > 0 ) {
                  $licenseproductoption = ProductOption::fromRefId( $licenserefid );
                  $licenseproduct = new Product( $licenseproductoption->productid );
                  //$licenseimages[$imageid] = array( 'product' => $licenseproduct->asArray(), 'type' => $image->licensetype, 'unitfee' => $image->licensefee, 'totalfee' => $image->licensefee );
                  $licenseimages[$imageid] = array(
                                                   'product' => $licenseproduct->asArray(),
                                                   'type' => $image->licensetype,
                                                   'unitfee' => $licensefee,
                                                   'totalfee' => ( $licensefee * $attributes['quantity'] ),
                                                   'quantity' => $attributes['quantity']
                                                   );
               }
               
               return $licenseimages;
               break;
               
            case 'mediaclip':
               import( 'website.projectorder' );
               try{
                  
                  $mediacliporder = new ProjectOrder( $attributes['referenceid'] );
                  
                  $licenseimages = array();
                  
                  if( strlen( $mediacliporder->used_images ) > 0 ) {
                     $projectimages = unserialize( $mediacliporder->used_images );
                     
                     if( is_array( $projectimages ) ) {
                        
                        $res = DB::query( "
                           SELECT 
                              bid,
                              licensetype,
                              licensefee
                           FROM 
                              bildeinfo 
                           WHERE
                              bid IN( ".implode( ',', $projectimages )." ) AND
                              licensefee > 0 
                           ORDER BY 
                              licensefee DESC
                           LIMIT 1
                        " );
                        
                        
                        $licenseproductoption = ProductOption::fromRefId( $licenserefid );
                        $licenseproduct = new Product( $licenseproductoption->productid );
                        
                        while( list( $bid, $licensetype, $fee ) = $res->fetchRow() ) {
                           
                           if( !$licensefee ){
                              $licensefee = $fee;
                           }   

                           $licenseimages[$bid] = array(
                                                        'product' => $licenseproduct->asArray(),
                                                        'type' => $licensetype, 'unitfee' => $licensefee,
                                                        'totalfee' => ( $licensefee * $attributes['quantity'] ),
                                                        'quantity' => $attributes['quantity']
                                                        );
                           //$licenseimages[$licensefee] = array( 'id' => $bid, 'product' => $licenseproduct->asArray(), 'type' => $licensetype, 'unitfee' => $licensefee, 'totalfee' => ( $licensefee ) );  
                        }
                     }
                     
                  }
               
               }catch ( Exception  $e ){
                  
                  //do nothing
                  
               }
               // only enforce one license per basket
               /*if( count( $licenseimages ) > 0 ) {
                  
                  // sort by price
                  ksort( $licenseimages );
                  
                  // pop off the last one
                  $lastitem = array_pop( $licenseimages );
                  $imageid = $lastitem['id'];
                  unset( $lastitem['id'] );
                  $licenseimages = array( $imageid => $lastitem );
                  
               }*/
               
               return $licenseimages;
               break;
               
            default:
               break;
         }
         
         return null;
         
      }
      
      
      
      /**
       * Get the total item quantity for mediaclip and gift products
       */
      private function getTotalItemQuantity( $producttype, $prodno, $excluderefid = null ) {
         
         $totalquantity = 0;
         
         if( count( $this->items[$producttype][$prodno] ) > 0 ) {
            foreach( $this->items[$producttype][$prodno] as $referenceid => $referencedata ) {
               if( $excluderefid != $referenceid ) {
                  $totalquantity += $this->items[$producttype][$prodno][$referenceid]['quantity'];
               }
            }
         }
         
         return $totalquantity;
         
      }
      
      
      
      /**
       * Update all prices for all items of a certain product
       *
       * @param float $unitprice
       * @param string $producttype
       * @param string $prodno
       */
      private function updateItemPrices( $unitprice, $producttype, $prodno ) {
         
         if( count( $this->items[$producttype][$prodno] ) > 0 ) {
            
            
            foreach( $this->items[$producttype][$prodno] as $referenceid => $referencedata ) {
               
               $quantity = $this->items[$producttype][$prodno][$referenceid]['quantity'];
               $this->items[$producttype][$prodno][$referenceid]['unitprice'] = $unitprice;
               $totalitemprice = $unitprice * $quantity;
               $this->items[$producttype][$prodno][$referenceid]['price'] = round( $totalitemprice, 2 );
               
            }
            
         }
         
      }
      
      
      private function updateCredits() {
         
         
         
      }
      
      
      /**
       * Update an item's credit
       *
       * @param array $item
       * @param Credit $credit
       * @return array
       */
      private function updateCredit( $item = array(), Credit $credit, $unitPrice ) {
         
         if( $credit instanceof Credit && $credit->isLoaded() ) {


            $item = array();
            $item['refid'] = $credit->artnr;
            $item['quantity'] = $quantity;
            $item['text'] = $credit->text;
            
            util::debug( $credit );
            die();
            //$this->credit['final'] []= $quantity;
            // We need to check quantity of product
            // and calculate nr of credits to use.
            /*$quantity = $credit->quantity;
            if( $item['quantity'] < $quantity ) {
               $quantity = $item['quantity'];
            }
            
            $item['credit']['quantity']   = $quantity;
            $item['credit']['text']       = $credit->text;
            $item['credit']['price'] = ( $quantity * $unitPrice );
            
            $item['price'] -= $item['credit']['price'];*/
            
         }
         
         return $item;
         
      }
      
      
      /**
       * Change the quantity of an item in cart
       *
       * @param string $prodno
       * @param string $reference
       */
      public function setItemQuantity( $prodno = '', $quantity = 0, $reference = '' ) {
         
         $quantity = max( $quantity, 1 );
         
         $prodno = (string) $prodno;
         $productOption = ProductOption::fromProdNo( $prodno );
         $productType = $this->getProductType( $productOption->tags );
         $price = 0.00;
         $weight = 0.00;
         
         switch( $productType ) {

            case 'prints':
            case 'enlargements':

               if( isset( $this->items[$productType][$prodno] ) ) {
                  
                  // Update quantity for each image on prodno in cart
                  $this->setImageQuantity( $prodno, $quantity );

                  // Set some usable params
                  $numImages = count( $this->items[$productType][$prodno]["images"] );
                  $oldPrice = $this->items[$productType][$prodno]["price"];
                  $oldWeight = $this->items[$productType][$prodno]["weight"];
                  $newQuantity = ( $numImages * $quantity );

                  // Remove weight and price from total cart
                  //$this->totalPrice -= $oldPrice;
                  //$this->cartPrice -= $oldPrice;
                  //$this->totalWeight -= $oldWeight;

                  // Update price and quantity for prodno
                  $this->items[$productType][$prodno]["unitprice"] = ProductOption::priceFromProdNo( $prodno, $newQuantity );
                  $this->items[$productType][$prodno]["quantity"] = $newQuantity;
                  $this->items[$productType][$prodno]["price"] = round( $this->items[$productType][$prodno]["unitprice"] * $newQuantity, 2 );
                  $this->items[$productType][$prodno]["weight"] = round( $this->items[$productType][$prodno]["unitweight"] * $newQuantity, 3 );

                  // Update overall cart price
                  //$this->totalPrice += $this->items[$productType][$prodno]["price"];
                  //$this->cartPrice += $this->items[$productType][$prodno]["price"];
                  //$this->totalWeight += $this->items[$productType][$prodno]["weight"];

                  // Recalculate any production, paper quality and correction methods
                  $this->recalculatePrintMethods();
                  
               }
               break;

            case 'gifts':
            case 'mediaclip':
            case 'ukeplan':
            case 'merkelapp':
            case 'stempel':
            case 'textgift':
            case 'module':
            case 'digital':
               
               if( isset( $this->items[$productType][$prodno][$reference] ) ) {
                  
                  $oldPrice = $this->items[$productType][$prodno][$reference]["price"];
                  $oldWeight = $this->items[$productType][$prodno][$reference]["weight"];
                  
                  // Only applicable for mediaclip ( photobooks mainly )
                  // Remove weight and price for extra pages
                  if( isset( $this->items[$productType][$prodno][$reference]["extrapages"] ) ) {
                     $oldPrice += $this->items[$productType][$prodno][$reference]["extrapages"]["price"];
                     $oldWeight += $this->items[$productType][$prodno][$reference]["extrapages"]["weight"];   
                  }
                  
                  $totalquantity = $this->getTotalItemQuantity( $productType, $prodno, $reference );
                  $totalquantity += $quantity;
                  $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
   
                  $this->updateItemPrices( $newunitprice, $productType, $prodno );
                  
                  // Update price and quantity for prodno
                  $this->items[$productType][$prodno][$reference]["unitprice"] = round( $newunitprice, 2 );//ProductOption::priceFromProdNo( $prodno, $quantity );
                  $this->items[$productType][$prodno][$reference]["quantity"] = $quantity;
                  $this->items[$productType][$prodno][$reference]["price"] = round( $this->items[$productType][$prodno][$reference]["unitprice"] * $quantity, 2 );
                  $this->items[$productType][$prodno][$reference]["weight"] = round( $this->items[$productType][$prodno][$reference]["unitweight"] * $quantity, 3 );
                  
                  
                  if( count( $this->items[$productType][$prodno][$reference]["license"] ) > 0 ) {
                     foreach( $this->items[$productType][$prodno][$reference]["license"] as $licenseimageid => $licensevalues ) {
                        $unitfee = $this->items[$productType][$prodno][$reference]["license"][$licenseimageid]["unitfee"];
                        $totalfee = ( $unitfee * $quantity ); // * $quantity 
                        $type = $this->items[$productType][$prodno][$reference]["license"][$licenseimageid]["type"];
                        $licenseproduct = $this->items[$productType][$prodno][$reference]["license"][$licenseimageid]["product"];
                        
                        $this->items[$productType][$prodno][$reference]["license"][$licenseimageid] = array(
                           'product' => $licenseproduct,
                           'type' => $type,
                           'unitfee' => $unitfee,
                           'totalfee' => $totalfee,
                           'quantity' => $quantity
                        );
                     }
                  }
                  
                  // Only applicable for mediaclip ( photobooks mainly )
                  // Add weight and price for extra pages
                  if( isset( $this->items[$productType][$prodno][$reference]["extrapages"] ) ) {
                     
                     // Import component
                     import( 'website.mediaclipproductoption' );
                     $mcpo = MediaclipProductOption::fromProdNo( $prodno );
                     $extraPagesRefId = $mcpo->extraPagesRefId();
                     
                     // If the product option is loaded 
                     // Load the product
                     $epProductOption = ProductOption::fromRefId( $extraPagesRefId );
                     if( $epProductOption->isLoaded() && $epProductOption instanceof ProductOption  ) {
                        
                        // Calculate the new values
                        $epProduct = new Product( $epProductOption->productid );
                        $epProductTotalExtraPages = $quantity * $this->items[$productType][$prodno][$reference]["extrapages"]["uniquequantity"];
                        $epUnitPrice = ProductOption::priceFromProdNo( $epProductOption->prodno, $epProductTotalExtraPages );
                        $totalProductPrice += $epUnitPrice * $epProductTotalExtraPages;
                        
                        // Setup new values
                        $this->items[$productType][$prodno][$reference]["extrapages"]["quantity"] = ( $quantity * $this->items[$productType][$prodno][$reference]["extrapages"]["uniquequantity"] );
                        $this->items[$productType][$prodno][$reference]['extrapages']['unitprice'] = $epUnitPrice;
                        $this->items[$productType][$prodno][$reference]['extrapages']['price'] = round( $epUnitPrice * $epProductTotalExtraPages, 2 );
                        $this->items[$productType][$prodno][$reference]['extrapages']['unitweight'] = round( $epProductOption->getUnitWeight(), 3 );
                        $this->items[$productType][$prodno][$reference]['extrapages']['weight'] = round( ( $this->items[$productType][$prodno][$reference]['extrapages']['unitweight'] * $epProductTotalExtraPages ), 3 );
                        
                        // Update overall cart price with extra pages
                        //$this->totalPrice += $this->items[$productType][$prodno][$reference]['extrapages']['price'];
                        //$this->cartPrice += $this->items[$productType][$prodno][$reference]['extrapages']['price'];
                        //$this->totalWeight += $this->items[$productType][$prodno][$reference]['extrapages']["weight"];
                        
                     }
                  }
                  
                 if( $attributes["redeyeremoval"] == true ) {
                     
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );

                     if( $rerProductOption->isLoaded() ) {

                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$reference]['redeyeremoval'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $rerProductOption->price, 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                     }
                     
                  }
                  
                  
                  if( isset( $this->items[$productType][$prodno][$reference]["varnish"] ) ) {
                     
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'varnish' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );

                     $totalProductPrice = ( $quantity * $rerProductOption->price );
                     
                     if( $rerProductOption->isLoaded() ) {

                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$reference]['varnish'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'unitprice' => $rerProductOption->price,
                           'price' => round( $totalProductPrice, 2 ),
                           'quantity' => $quantity,
                           'product' => $rerProduct->asArray(),
                        );
                     }
                     
                  }
                  
                  // Update overall cart price
                  //$this->totalPrice += $this->items[$productType][$prodno][$reference]["price"];
                  //$this->cartPrice += $this->items[$productType][$prodno][$reference]["price"];
                  //$this->totalWeight += $this->items[$productType][$prodno][$reference]["weight"];
                  
               }
               break;

            case 'goods':
               
               if( isset( $this->items[$productType][$prodno] ) ) {
                  
                  // Set some usable params
                  $oldPrice = $this->items[$productType][$prodno]["price"];
                  $oldWeight = $this->items[$productType][$prodno]["weight"];
                  //$newQuantity = ( $numImages * $quantity );
                  $newQuantity = $quantity;

                  // Case product is a set of products
                  if( isset( $this->items[$productType][$prodno]['set']['totalitemquantity'] ) ) {
                     $setitemquantity = $this->items[$productType][$prodno]['set']['setitemquantity'];
                     $totalitemquantity = ( $setitemquantity * $newQuantity );
                     
                     // Update price and quantity for prodno
                     $this->items[$productType][$prodno]["unitprice"] = ProductOption::priceFromProdNo( $prodno, (int) $totalitemquantity ) * $setitemquantity;
                     $this->items[$productType][$prodno]["quantity"] = $newQuantity;
                     $this->items[$productType][$prodno]["price"] = round( $this->items[$productType][$prodno]["unitprice"] * $newQuantity, 2 );
                     $this->items[$productType][$prodno]["weight"] = round( $this->items[$productType][$prodno]["unitweight"] * (int) $totalitemquantity, 3 );
                     $this->items[$productType][$prodno]['set']['totalitemquantity'] = $totalitemquantity;
                  } else {
                     // Update price and quantity for prodno
                     $this->items[$productType][$prodno]["unitprice"] = ProductOption::priceFromProdNo( $prodno, $newQuantity );
                     $this->items[$productType][$prodno]["quantity"] = $newQuantity;
                     $this->items[$productType][$prodno]["price"] = round( $this->items[$productType][$prodno]["unitprice"] * $newQuantity, 2 );
                     $this->items[$productType][$prodno]["weight"] = round( $this->items[$productType][$prodno]["unitweight"] * $newQuantity, 3 );
                  }
                  
               }
               break;
            case 'smilesontiles':
               
               if( isset( $this->items[$productType][$prodno][$reference] ) ) {
                  
                  $oldPrice = $this->items[$productType][$prodno][$reference]["price"];
                  $unitprice = $this->items[$productType][$prodno][$reference]["unitprice"];
                  $oldWeight = $this->items[$productType][$prodno][$reference]["weight"];
                  
                  
                  $totalquantity = $this->getTotalItemQuantity( $productType, $prodno, $reference );
                  $totalquantity += $quantity;
                  $newunitprice = $unitprice;
   
                  //$this->updateItemPrices( $newunitprice, $productType, $prodno );
                  
                  // Update price and quantity for prodno
                  $this->items[$productType][$prodno][$reference]["unitprice"] = round( $newunitprice, 2 );//ProductOption::priceFromProdNo( $prodno, $quantity );
                  $this->items[$productType][$prodno][$reference]["quantity"] = $quantity;
                  $this->items[$productType][$prodno][$reference]["price"] = round( $this->items[$productType][$prodno][$reference]["unitprice"] * $quantity, 2 );
                  $this->items[$productType][$prodno][$reference]["weight"] = round( $this->items[$productType][$prodno][$reference]["unitweight"] * $quantity, 3 );
                  
               
                  
                 if( $attributes["redeyeremoval"] == true ) {
                     
                     config( 'website.cart' );
                     $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );

                     if( $rerProductOption->isLoaded() ) {

                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$reference]['redeyeremoval'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $rerProductOption->price, 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                     }
                     
                  }
               
               }
               break;
            default:
               break;

         }
         
         if( isset( $this->discount['info']['id'] ) ) {
            $this->discount = $this->addCartDiscount( $this->discount['info'] );
         }
         
         $this->recalculateTotals();

      }
      
      
        /**
       * Change the redeyeoption of an item in cart
       *
       * @param string $prodno
       * @param string $reference
       */
      public function setRedeye( $prodno = '', $reference = '' ) {
         import( 'website.projectorder' );
         config( 'website.cart' );
         $quantity = max( $quantity, 1 );
         $prodno = (string) $prodno;
         $productOption = ProductOption::fromProdNo( $prodno );
         $productType = $this->getProductType( $productOption->tags );
         $price = 0.00;
         $weight = 0.00;

         switch( $productType ) {

            case 'gifts':
            case 'mediaclip':
               
               if( isset( $this->items[$productType][$prodno][$reference] ) ) {
                  $mc_order = new ProjectOrder($reference);
                  
                   $grouptype = DB::query( "SELECT grouptype FROM article where artnr = ?", $mc_order->article_id )->fetchSingle();
                  
                  if(in_array( $grouptype, Settings::get( 'cart', 'calendar_group' ))){
                     $rerRefId = Settings::get( 'cart', 'redeyeremoval_calendar' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                  }
                  
                  if(in_array( $grouptype, Settings::get( 'cart', 'oppheng_group' ))){
                     $rerRefId = Settings::get( 'cart', 'oppheng' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                  }
                  
                  
                  else{
                     $rerRefId = Settings::get( 'cart', 'redeyeremoval' );
                     $rerRefId = $rerRefId["refid"];
                     $rerProductOption = ProductOption::fromRefId( $rerRefId );
                  }

                     if( $rerProductOption->isLoaded() ) {

                        // Setup red eye removal product. Unique per product
                        $rerProduct = new Product( $rerProductOption->productid );
                        $this->items[$productType][$prodno][$reference]['redeyeremoval'] = array(
                           'prodno' => $rerProductOption->prodno,
                           'optionid' => $rerProductOption->id,
                           'refid' => $rerProductOption->refid,
                           'price' => round( $rerProductOption->price, 2 ),
                           'quantity' => 1,
                           'product' => $rerProduct->asArray(),
                        );
                     }

                 $mc_order->redeye = $rerRefId;
                 $mc_order->save();
                  
               }
               break;
               
            default:
               break;

         }
         
         if( isset( $this->discount['info']['id'] ) ) {
            $this->discount = $this->addCartDiscount( $this->discount['info'] );
         }
         
         $this->recalculateTotals();

      }
      
      /**
       * If a productoption is not valid,f.ex 
       * cant be loaded then we need to be able to remove them
       * from the cart.
       *
       * @param string $prodno
       */
      private function removeAllMatchingProdnos( $prodno ) {

         if( count( $this->items ) > 0 ) {
            
            foreach( $this->items as $producttype => $products ) {
               
               foreach( $products as $compprodno => $item ) {
                  
                  if( $compprodno == $prodno ) {
                     
                     unset( $this->items[$producttype][$prodno] );
                     
                     if( count( $this->items[$producttype] ) == 0 ) {
                        unset( $this->items[$producttype] );
                     }
                     
                  }
                  
               }
               
            }
            
         }
         
      }
      
      
      
      
      /**
       * Remove a product from cart
       *
       * @param integer $prodno
       */
      public function removeItem( $prodno = '', $reference = '' ) {
         
         $prodno = (string) $prodno;
         $productOption = ProductOption::fromProdNo( $prodno );
         
         if( !$productOption instanceof ProductOption || !$productOption->isLoaded() ) {
            
            $this->removeAllMatchingProdnos( $prodno );
            
         }
         
         $productType = $this->getProductType( $productOption->tags );
         $price = 0.00;
         $weight = 0.00;
         $tmprefid = $productOption->refid;
         
         // Remove from creditcardonly check and services
         unset( $this->creditCardOnly[$prodno] );
         unset( $this->services[$prodno] );
         
         switch( $productType ) {
            
            case 'prints':
               $price = $this->items["prints"][$prodno]["price"];
               $weight = $this->items["prints"][$prodno]["weight"];
               unset( $this->items["prints"][$prodno] );
               $this->recalculatePrintMethods();
               break;
               
            case 'enlargements':
               $price = $this->items["enlargements"][$prodno]["price"];
               $weight = $this->items["enlargements"][$prodno]["weight"];
               unset( $this->items["enlargements"][$prodno] );
               break;
               
            case 'textgift':
            case 'module':
            case 'digital':
               
               $price = $this->items[$productType][$prodno][$reference]["price"];
               $weight = $this->items[$productType][$prodno][$reference]["weight"];
               
               unset( $this->items[$productType][$prodno][$reference] );
               if( count( $this->items[$productType][$prodno] ) == 0 ) {
                  unset( $this->items[$productType][$prodno] );
               }
               
               $totalquantity = $this->getTotalItemQuantity( $productType, $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               $this->updateItemPrices( $newunitprice, $productType, $prodno );
               unset( $totalquantity );
               
               brake;
            case 'gifts':
               $price = $this->items["gifts"][$prodno][$reference]["price"];
               if( isset( $this->items["gifts"][$prodno][$reference]['redeyeremoval'] ) ) {
                  $price += $this->items["gifts"][$prodno][$reference]['redeyeremoval']['price'];
               }
               $weight = $this->items["gifts"][$prodno][$reference]["weight"];
               unset( $this->items["gifts"][$prodno][$reference] );
               if( count( $this->items["gifts"][$prodno] ) == 0 ) {
                  unset( $this->items["gifts"][$prodno] );
               }
               
               // TEMPORARY CODE TO ALLOW REMOVAL OF BROKEN GIFTS
               $price = $this->items["gifts"]['0000'][$reference]["price"];
               if( isset( $this->items["gifts"]['0000'][$reference]['redeyeremoval'] ) ) {
                  $price += $this->items["gifts"]['0000'][$reference]['redeyeremoval']['price'];
               }
               $weight = $this->items["gifts"]['0000'][$reference]["weight"];
               unset( $this->items["gifts"]['0000'][$reference] );
               if( count( $this->items["gifts"]['0000'] ) == 0 ) {
                  unset( $this->items["gifts"]['0000'] );
               }
               
               // TEMPORARY CODE TO ALLOW REMOVAL OF MEDIACLIP GIFTS
               $price = $this->items["mediaclip"][$prodno][$reference]["price"];
               $weight = $this->items["mediaclip"][$prodno][$reference]["weight"];
               
               // If there are extra pages on product
               if( isset( $this->items["mediaclip"][$prodno][$reference]["extrapages"] ) ) {
                  $price += $this->items["mediaclip"][$prodno][$reference]["extrapages"]["price"];
                  $weight += $this->items["mediaclip"][$prodno][$reference]["extrapages"]["weight"];
               }
               
               unset( $this->items["mediaclip"][$prodno][$reference] );
               if( count( $this->items["mediaclip"][$prodno] ) == 0 ) {
                  unset( $this->items["mediaclip"][$prodno] );
               }
               
               $totalquantity = $this->getTotalItemQuantity( 'gifts', $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               $this->updateItemPrices( $newunitprice, 'gifts', $prodno );
               unset( $totalquantity );
               
               break;
               
            case 'mediaclip':
               $price = $this->items["mediaclip"][$prodno][$reference]["price"];
               $weight = $this->items["mediaclip"][$prodno][$reference]["weight"];
               
               // If there are extra pages on product
               if( isset( $this->items["mediaclip"][$prodno][$reference]["extrapages"] ) ) {
                  $price += $this->items["mediaclip"][$prodno][$reference]["extrapages"]["price"];
                  $weight += $this->items["mediaclip"][$prodno][$reference]["extrapages"]["weight"];
               }
               
               unset( $this->items["mediaclip"][$prodno][$reference] );
               if( count( $this->items["mediaclip"][$prodno] ) == 0 ) {
                  unset( $this->items["mediaclip"][$prodno] );
               }
               
               
               // Temp fix to remove wrongly tagged mediaclip products
               unset( $this->items["gifts"][$prodno][$reference] );
               if( count( $this->items["gifts"][$prodno] ) == 0 ) {
                  unset( $this->items["gifts"][$prodno] );
               }
               
               
               $totalquantity = $this->getTotalItemQuantity( 'mediaclip', $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               $this->updateItemPrices( $newunitprice, 'mediaclip', $prodno );
               unset( $totalquantity );
               
               break;
            case 'smilesontiles':
               $price = $this->items["smilesontiles"][$prodno][$reference]["price"];
               $weight = $this->items["smilesontiles"][$prodno][$reference]["weight"];
               
               
               unset( $this->items["smilesontiles"][$prodno][$reference] );
               if( count( $this->items["smilesontiles"][$prodno] ) == 0 ) {
                  unset( $this->items["smilesontiles"][$prodno] );
               }
               
               
               $totalquantity = $this->getTotalItemQuantity( 'smilesontiles', $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               //$this->updateItemPrices( $newunitprice, 'smilesontiles', $prodno );
               unset( $totalquantity );
               
               break;
               
            case 'ukeplan':
               $price = $this->items["ukeplan"][$prodno][$reference]["price"];
               $weight = $this->items["ukeplan"][$prodno][$reference]["weight"];
               
               
               unset( $this->items["ukeplan"][$prodno][$reference] );
               if( count( $this->items["ukeplan"][$prodno] ) == 0 ) {
                  unset( $this->items["ukeplan"][$prodno] );
               }
               
               
               $totalquantity = $this->getTotalItemQuantity( 'ukeplan', $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               $this->updateItemPrices( $newunitprice, 'ukeplan', $prodno );
               unset( $totalquantity );
               
               break;
               
            case 'merkelapp':
               $price = $this->items["merkelapp"][$prodno][$reference]["price"];
               $weight = $this->items["merkelapp"][$prodno][$reference]["weight"];
               
               
               unset( $this->items["merkelapp"][$prodno][$reference] );
               if( count( $this->items["merkelapp"][$prodno] ) == 0 ) {
                  unset( $this->items["merkelapp"][$prodno] );
               }
               
               
               $totalquantity = $this->getTotalItemQuantity( 'merkelapp', $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               $this->updateItemPrices( $newunitprice, 'merkelapp', $prodno );
               unset( $totalquantity );
               
               break;
            case 'stempel':
               $price = $this->items["stempel"][$prodno][$reference]["price"];
               $weight = $this->items["stempel"][$prodno][$reference]["weight"];
               
               
               unset( $this->items["stempel"][$prodno][$reference] );
               if( count( $this->items["stempel"][$prodno] ) == 0 ) {
                  unset( $this->items["stempel"][$prodno] );
               }
               
               
               $totalquantity = $this->getTotalItemQuantity( 'stempel', $prodno );
               $newunitprice = ProductOption::priceFromProdNo( $prodno, $totalquantity );
               $this->updateItemPrices( $newunitprice, 'stempel', $prodno );
               unset( $totalquantity );
               
               break;
               
            case 'goods':
               $price = $this->items['goods'][$prodno]['price'];
               $weight = $this->items['goods'][$prodno]['weight'];
               unset( $this->items['goods'][$prodno] );
               break;
               
            case 'subscription':
               $price = $this->items['subscription'][$prodno]['price'];
               $weight = $this->items['subscription'][$prodno]['weight'];
               unset( $this->items['subscription'][$prodno] );
               break;
                              
            default:
               break;
               
         }

         // The section of productType is empty, just unset it.
         if( count( $this->items[$productType] ) == 0 ) {
            unset( $this->items[$productType] );
         }
         
         if( count( $this->credits ) > 0 ) {
            
            /*foreach( $this->credits as $key => $credit ) {
               if( $credit['refid'] == $tmprefid ) {
                  unset( $this->credits[$key]['usedquantity'] );
                  unset( $this->credits[$key]['usedprice'] );
               }
            }*/
            unset( $this->credits );
            
         }
         
         // Recalculate price and weight of cart
         //$this->totalPrice -= $price;
         //$this->cartPrice -= $price;
         //$this->totalWeight -= $weight;
         //$this->totalItems -= 1;
         if( isset( $this->discount['info']['id'] ) ) {
            $this->discount = $this->addCartDiscount( $this->discount['info'] );
         }
         $this->recalculateTotals();
         
      }
         
      
      
      /**
       * Update the image quantity on an order row
       *
       * @param string $prodno
       * @param integer $quantity
       */
      private function setImageQuantity( $prodno = '', $quantity = 0 ) {
         
         if( isset( $prodno ) && isset( $quantity ) ) {

            // Check prints and set
            if( count( $this->items["prints"][$prodno]["images"] ) ) {
               foreach( $this->items["prints"][$prodno]["images"] as $key => $value ) {
                  $this->items["prints"][$prodno]["images"][$key] = $quantity;
               }
            }
            
            // Check and set enlargements
            if( count( $this->items["enlargements"][$prodno]["images"] ) ) {
               foreach( $this->items["enlargements"][$prodno]["images"] as $key => $value ) {
                  $this->items["enlargements"][$prodno]["images"][$key] = $quantity;
               }
            }
            
         }
         
      }
      
      
      
      /**
       * Update and save new updated info about production attributes
       *
       * @param unknown_type $cart
       */
      public function recalculatePrintMethods() {
         
         if( count( $this->items["prints"] ) == 0 ) {
            
            unset( $this->items["productionmethod"] );
            unset( $this->items["correctionmethod"] );
            unset( $this->items["paperquality"] );
               
         } else {
            
            
            if( isset( $this->productionmethod ) ) {
               
               $prodno = $this->productionmethod['prodno'];
               
               $this->removePrintAttribute( 'productionmethod' );
               $this->addPrintAttribute( $prodno );
               
            }
            
            if( isset( $this->correctionmethod ) ) {
               
               $prodno = $this->correctionmethod['prodno'];
               
               $this->removePrintAttribute( 'correctionmethod' );
               $this->addPrintAttribute( $prodno );
               
            }
            
            if( isset( $this->paperquality ) ) {
               
               $prodno = $this->paperquality['prodno'];
               
               $this->removePrintAttribute( 'paperquality' );
               $this->addPrintAttribute( $prodno );
               
            }
            
         }
         
      }
      
      
      
      /**
       * Unset an attribute in the cart
       *
       * @param string $key
       */
      public function removePrintAttribute( $key ) {

         // Recalculate price
         /*$product = $this->$key;
         $oldPrice = $product["price"];
         
         if( isset( $oldPrice ) ) {
            $this->totalPrice -= $oldPrice;
            $this->cartPrice -= $oldPrice;
         }*/
         
         unset( $this->$key );
         $this->recalculateTotals();
         
      }
      
      
      
      
      
      /**
       * Get the total number of prints ordered
       *
       * @return integer
       */
      private function getPrintQuantity() {
         
         $totalPrintQuantity = 0;

         if( count( $this->items["prints"] ) > 0 ) {
            
            foreach( $this->items["prints"] as $product ) {

               $totalPrintQuantity += $product["quantity"];
               
            }
            
         }
         if( count( $this->items["enlargements"]["0002"] ) > 0 ) {
            
            foreach( $this->items["enlargements"] as $key=>$product ) {

               if( $key == "0002"){
                  $totalPrintQuantity += $product["quantity"];
               }
               
            }
            
         }
         
         return $totalPrintQuantity;
         
      }
      
      
      
      /**
       * Get the number of actually unique images
       * in cart
       *
       * @return integer
       */
      private function getUniqueImagesQuantity() {
         
         $totalImageQuantity = 0;
         $images = array();
         
         
         if( count( $this->items["prints"] ) > 0 ) {
            
            foreach( $this->items["prints"] as $product ) {

               if( count( $product["images"] ) > 0 ) {
                  
                  foreach( $product["images"] as $imageid => $quantity ) {
                     
                     $images[$imageid] = 1;
                     
                  }
                  
               }
               
            }
            
         }
         
         if( count( $this->items["enlargements"] ) > 0 ) {
            
            foreach( $this->items["enlargements"] as $product ) {

               if( count( $product["images"] ) > 0 ) {
                  
                  foreach( $product["images"] as $imageid => $quantity ) {
                     
                     $images[$imageid] = 1;
                     
                  }
                  
               }
               
            }
            
         }
         
         return count( $images );
         
      }
      
      
      /**
       * Setup the images for this product
       *
       * @param unknown_type $cart
       * @param unknown_type $productType
       * @param unknown_type $prodno
       * @param unknown_type $attributes
       */
      private function setupImages( $productType, $prodno, $attributes ) {
         
         if( isset( $this->items[$productType][$prodno]['images'] ) ) {

            if( isset( $attributes["images"] ) ) {
               
               foreach( $attributes["images"] as $key => $value ) {

                  if( isset( $this->items[$productType][$prodno]["images"][$key] ) ) {
                     $this->items[$productType][$prodno]["images"][$key] += $attributes["images"][$key];
                  } else {
                     $this->items[$productType][$prodno]["images"][$key] = $attributes["images"][$key];
                  }

               }

            } 

         } else {
            if( is_array( $attributes['images']['crop'] ) ){
               $this->items[$productType][$prodno]['images']['crop'] = $attributes['images']['crop'];
            }
            $this->items[$productType][$prodno]['images'] = $attributes["images"];
         }
         
      }
      
      
      
      /**
       * Return the total price of all products in cart
       *
       * @return float
       * 
       */
      public function getTotalPrice() {

         return $this->totalPrice;
            
      }
      
      
      
      /**
       * Return nr of items in cart
       *
       * @return integer
       */
      public function getTotalItems() {
         
         return $this->totalItems;
         
      }
      
      
      
      /**
       * Get the total weight of items in cart
       *
       * @return float
       * 
       */
      public function getTotalWeight() {
         
         return $this->totalWeight;
         
      }
      
      /**
       * Set the total weight of items in cart
       *
       * @param float $weight
       */
      public function setTotalWeight( $weight = 0 ) {
         
         $weight = (float) $weight;
         $this->totalWeight = $weight;
         
      }
      
      
      
      /**
       * Get available delivery options from given weight
       *
       * @return array
       * 
       */
      private function getDeliveryOptions() {
         
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
      private function getPaymentOptions() {
         
         $regid = WebsiteHelper::region();
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
      
      
      public function getDeliveryAndPaymentOptionsNew() {
         $weight = $this->getTotalWeight();
         $price = $this->getTotalPrice();
         
         $siteid = Session::get( 'siteid' );
         
         $type = 'deliverytype';
         
         $delivery = DB::query( "SELECT * FROM site_delivery WHERE siteid = ?  AND weight <= ? ORDER BY deliverytype, weight", $siteid , $weight  )->fetchAll( DB::FETCH_ASSOC  );
         
         $availableDelivery = array();
         
         foreach( $delivery as $ret ){
            if( $ret['weight'] <= $weight ){
               $availableDelivery[$ret['deliverytype']] = $ret;
            }   
         }
         $options["delivery_options"] = array();
         
         foreach( $availableDelivery as $ret ){
            $collection = new DeliveryType($ret['deliverytype']);
            $options["delivery_options"][$ret['id']] = array(
                  "id" => $ret['id'],
                  "refid" => $ret['id'],
                  "price" =>  $ret['price'], 
                  "name" =>  $collection->title,
                  "title" =>  $collection->title,
                  "ingress" =>  $collection->ingress,
                  'artnr' =>  $ret['deliverytype'],
               );
             
              $payment_options = unserialize( $ret['paymentoptions'] );
              
              
               foreach(  $payment_options as $payment ){
                  
                  $collection = new Paymenttype($payment);
                  
                  
                  if( (int)$collection->price <= 0 ){
                     $price = 0;
                  }else{
                     $price = $collection->price;
                     
                  }
                  
                  $options["delivery_options"][$ret['id']]["payment_options"][] = array( 
                     "id" => $collection->id,
                     "refid" => $collection->id,
                     "price" =>  $price,
                     "name" =>  $collection->title,
                     "title" =>  $collection->title,
                     "ingress" =>  $collection->ingress
                 );
            
                  
               }
            
         }
         
         
         return $options;
         
         
      }
      
      
      public function getDeliveryAndPaymentOptions() {
         
         $weight = $this->getTotalWeight();
         $price = $this->getTotalPrice();
         
         $not_updated = 0;
         $regionid = WebsiteHelper::region();
         $deliverydata = $this->getDeliveryOptions();
         $paymentdata = $this->getPaymentOptions();
         
         $availableDelivery = array();
         $n = count( $deliverydata );
            
         for($i=0;$i<$n;$i++){
               
            if( $deliverydata[$i]["weight"] <= $weight){
               
               if( $deliverydata[$i]["artnr"] == 390 &&  $weight >= 2000 ){
                  continue;
               }else{
                  $availableDelivery[$deliverydata[$i]["artnr"]] = $deliverydata[$i]["deliveryid"];
               }    
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


         // Only allow creditcard as payment option in theese cases:
         // Some product in cart tagged as creditcard only or
         // user is not logged in.
         
         if( $this->creditcard() || $this->isOnlyStampOrder( true ) ) {

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
      
      
      
      /**
       * Set the choosen delivery type by user
       *
       * @param integer $id
       * @return boolean
       * 
       */
      public function setDeliveryType( $id, $price = null) {

         $deliverytype = DeliveryType::fromRefId( $id );
         
         if( !$deliverytype->isLoaded() || !$deliverytype instanceof DeliveryType ) return false;
         
         if( isset( $this->deliveryType ) ) {
            
            $oldPrice = $this->deliveryType['price'];
            //$this->totalPrice -= $oldPrice;
            
         }
         
         $this->deliveryType = $deliverytype->asArray();
         
         if( $this->checkFreeShipping() ){
            $this->deliveryType['price'] = 0;
         }
         
         if( $this->discount['info']['id'] == 351 ){
            $this->deliveryType['price'] = 0;
         }
         
         if( $price !== null ){
            //Util::Debug( "ikkje  null" );
            $this->deliveryType['price'] = $price;
         }
         
         //$this->totalPrice += $deliverytype->price;
         
         if( $deliverytype->price > 0){
            $this->recalculateTotals();
         }
         else{
            $this->save();
         }
         
         
         return true;
         
      }
      
      public function setDeliveryTypeNew( $id, $price = null) {
         
         
         $deliverytype = DB::query( "SELECT deliverytype FROM site_delivery WHERE id = ?", $id  )->fetchSingle();
         $price = DB::query( "SELECT price FROM site_delivery WHERE id = ?", $id  )->fetchSingle();

         $deliverytype = new DeliveryType( $deliverytype );
         
         if( !$deliverytype->isLoaded() || !$deliverytype instanceof DeliveryType ) return false;
         
         if( isset( $this->deliveryType ) ) {
            
            $oldPrice = $this->deliveryType['price'];
            //$this->totalPrice -= $oldPrice;
            
         }
         
         $this->deliveryType = $deliverytype->asArray();
         
         if( $this->checkFreeShipping() ){
            $this->deliveryType['price'] = 0;
         }
         if( $price !== null ){
            //Util::Debug( "ikkje  null" );
            $this->deliveryType['price'] = $price;
         }
         
          $this->deliveryType['refid'] = $this->deliveryType['id'];
          $this->deliveryType['artnr'] = $this->deliveryType['id'];
         
         //$this->totalPrice += $deliverytype->price;
         
         if( $deliverytype->price > 0){
            $this->recalculateTotals();
         }
         else{
            $this->save();
         }
         
         
         return true;
         
      }
      
      
      /**
       * Return the choosen delivery type by user
       *
       * @return array
       * 
       */
      public function getDeliveryType() {
         
         return $this->deliveryType;
         
      }
      
      
      /**
       * Set the choosen payment type by user
       *
       * @param integer $id
       * @return boolean
       * 
       */
      public function setPaymentTypeNew( $id ) {

         //$paymenttype = PaymentType::fromRefId( $id );
         $paymenttype = new PaymentType( $id );
         
         
         if( !$paymenttype->isLoaded() || !$paymenttype instanceof PaymentType ) return false;
         
         if( isset( $this->paymentType ) ) {
            
            $oldPrice = $this->paymentType['price'];
            //$this->totalPrice -= $oldPrice;
            
         }
         
         $this->paymentType = array(
                  'id' => $paymenttype->id,
                  'title' => $paymenttype->title,
                  'refid' => $paymenttype->id,
                  'regionid' => $paymenttype->regionid,
                  'artnr' =>  $paymenttype->id,
                  'minvalue' => $paymenttype->minvalue,
                  'maxvalue' => $paymenttype->maxvalue,
                  'price' => $paymenttype->price,
               );
         //$this->totalPrice += $paymenttype->price;
         
         $this->recalculateTotals();
         
         return true;
         
      }
      
      
      /**
       * Set the choosen payment type by user
       *
       * @param integer $id
       * @return boolean
       * 
       */
      public function setPaymentType( $id ) {

         $paymenttype = PaymentType::fromRefId( $id );
         //$paymenttype = new PaymentType( $id );
         
         if( !$paymenttype->isLoaded() || !$paymenttype instanceof PaymentType ) return false;
         
         if( isset( $this->paymentType ) ) {
            
            $oldPrice = $this->paymentType['price'];
            //$this->totalPrice -= $oldPrice;
            
         }
         
         $this->paymentType = $paymenttype->asArray();
         //$this->totalPrice += $paymenttype->price;
         
         $this->recalculateTotals();
         
         return true;
         
      }
      
      
      /**
       * Return the choosen payment type by user
       *
       * @return array
       * 
       */
      public function getPaymentType() {
         
         return $this->paymentType;
         
      }
      
      
      
      
      /**
       * Recalculate the cart price
       *
       * @return float
       * 
       */
      private function getRecalculatedTotalPrice() {

         $totalPrice = 0.00;
         
         if( count( $this->items ) > 0 ) {
            
            foreach( $this->items as $producttype => $products ) {

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
      
     
      
      /**
       * Check if cart contains only service products
       * Products that are not physically delivered.
       *
       * @return boolean
       */
      public function serviceProductsOnly() {
         
         $numServices = count( $this->services );
         
         if( ( $numServices > 0 && ( $this->totalItems == $numServices ) ) || $this->isOnlyStampOrder() || $this->isGiftcardOnlyAndNoProducing() ) {
            
            return true;
            
         }
         
         return false;
         
      }
      
      
      public function isGiftcardOnlyAndNoProducing() {

         if( $this->totalItems == 1 ) {
            
            if( is_array( $this->items['goods'] ) ) {
               
               foreach( $this->items['goods'] as $prodno => $item ) {
                  
                  if( is_array( $item['giftcard'] ) ) {
                     
                     if( isset( $item['giftcard']['print'] ) ) {
                        
                        return false;
                        
                     } else {
                        
                        return true;
                        
                     }
                     
                  } else {
                     
                     return false;
                     
                  }
                  
               }
               
            }
            
         } else {
            
            if( is_array( $this->items['goods'] ) ) {
               
               if( $this->totalItems > count( $this->items['goods'] ) ) return false;
               
               foreach( $this->items['goods'] as $prodno => $item ) {
                  
                  if( is_array( $item['giftcard'] ) ) {
                     
                     if( isset( $item['giftcard']['print'] ) ) {
                        
                        return false;
                        
                     }
                  
                  } else {
                     
                     return false;
                     
                  }
               
               }
               
               return true;
               
            } else {
               
               return false;
               
            }
            
         }
         
         return false;
         
      }
      
      
      public function isOnlyStampOrder( $anystampsgiftsatall = false ) {
         
         $numGiftServices = 0;
         
         if( is_array( $this->items['gifts'] ) )
         foreach( $this->items['gifts'] as $prodno => $objects ) {
            foreach( $objects as $objectid => $object ) {
               if( $object['refid'] == 7007 ) {
                  $numGiftServices++;
               }
            }
         }
         
         if( $numGiftServices > 0 && $anystampsgiftsatall ) {
            
            return true;
            
         }
         
         if( $numGiftServices > 0 && ( $this->totalItems == $numGiftServices ) ) {
            
            return true;
            
         }
         
         return false;
         
      }
      
      
      /**
       * Check what images are also in cart
       *
       * @param array $compare
       * @return array
       */
      public function imagesInCart( $compare = array() ) {

         $prints = $this->items['prints'];
         $enlargements = $this->items['enlargements'];
         $images = array();
         
         if( is_array( $prints ) ) {
            
            foreach( $prints as $product ) {
               
               foreach( $product['images'] as $key => $value ) {
                  
                  $images[$key] = 1; 
                  
               }
               
               
            }
            
         }
         
         if( is_array( $enlargements ) ) {
            
            foreach( $enlargements as $product ) {
               
               foreach( $product['images'] as $key => $value ) {
                  
                  $images[$key] = 1; 
                  
               }
               
               
            }
            
         }
         
         $instersect = array_intersect_key( $compare, $images );
         return array_flip( $instersect );
         
      }
      
      
      
      /**
       * Remove user choice of red removal from the cart
       *
       * @param string $prodno
       * @param integer $referenceid
       * @return unknown
       */
      public function removeRedeye( $prodno = null, $referenceid = null ) {
         
         if( isset( $this->items['gifts'][$prodno][$referenceid]['redeyeremoval'] ) && is_array( $this->items['gifts'][$prodno][$referenceid]['redeyeremoval'] ) ) {
            
            //$price = $this->items['gifts'][$prodno][$referenceid]['redeyeremoval']['price'];
            unset( $this->items['gifts'][$prodno][$referenceid]['redeyeremoval'] );
            //$this->totalPrice -= $price; 
            //$this->cartPrice -= $price; 
            $this->recalculateTotals();
            return true;
            
         }
         else if( isset( $this->items['mediaclip'][$prodno][$referenceid]['redeyeremoval'] ) && is_array( $this->items['mediaclip'][$prodno][$referenceid]['redeyeremoval'] ) ) {
            
            //$price = $this->items['gifts'][$prodno][$referenceid]['redeyeremoval']['price'];
            unset( $this->items['mediaclip'][$prodno][$referenceid]['redeyeremoval'] );
            //$this->totalPrice -= $price; 
            //$this->cartPrice -= $price; 
            $this->recalculateTotals();
            return true;
            
         }
         
         return false;
         
      }
      
      
      /**
       * Remove user choice of varnish from the cart
       *
       * @param string $prodno
       * @param integer $referenceid
       * @return unknown
       */
      public function removeVarnish( $prodno = null, $referenceid = null ) {
         
         if( isset( $this->items['gifts'][$prodno][$referenceid]['varnish'] ) && is_array( $this->items['gifts'][$prodno][$referenceid]['varnish'] ) ) {
            
            //$price = $this->items['gifts'][$prodno][$referenceid]['redeyeremoval']['price'];
            unset( $this->items['gifts'][$prodno][$referenceid]['varnish'] );
            //$this->totalPrice -= $price; 
            //$this->cartPrice -= $price; 
            $this->recalculateTotals();
            return true;
            
         }
         else if( isset( $this->items['mediaclip'][$prodno][$referenceid]['varnish'] ) && is_array( $this->items['mediaclip'][$prodno][$referenceid]['varnish'] ) )         {
            
            //$price = $this->items['gifts'][$prodno][$referenceid]['redeyeremoval']['price'];
            unset( $this->items['mediaclip'][$prodno][$referenceid]['varnish'] );
            //$this->totalPrice -= $price; 
            //$this->cartPrice -= $price; 
            $this->recalculateTotals();
            return true;
            
         }
         
         return false;
         
      }

      /**
       * Remove user choice of u from the cart
       *
       * @param string $prodno
       * @param integer $referenceid
       * @return unknown
       */
      public function removeUpgrade( $prodno = null, $referenceid = null ) {
         
         if( isset( $this->items['gifts'][$prodno][$referenceid]['upgrade'] ) && is_array( $this->items['gifts'][$prodno][$referenceid]['upgrade'] ) ) {
            
            //$price = $this->items['gifts'][$prodno][$referenceid]['redeyeremoval']['price'];
            unset( $this->items['gifts'][$prodno][$referenceid]['upgrade'] );
            //$this->totalPrice -= $price; 
            //$this->cartPrice -= $price; 
            $this->recalculateTotals();
            return true;
            
         }
         else if( isset( $this->items['mediaclip'][$prodno][$referenceid]['upgrade'] ) && is_array( $this->items['mediaclip'][$prodno][$referenceid]['upgrade'] ) )         {
            
            //$price = $this->items['gifts'][$prodno][$referenceid]['redeyeremoval']['price'];
            unset( $this->items['mediaclip'][$prodno][$referenceid]['upgrade'] );
            //$this->totalPrice -= $price; 
            //$this->cartPrice -= $price; 
            $this->recalculateTotals();
            return true;
            
         }
         
         return false;
         
      }
      /**
       * Remove user choice of maskit from the cart
       *
       * @param string $prodno
       * @param integer $referenceid
       * @return unknown
       */
      public function removeMaskit( $prodno = null, $referenceid = null ) {
         
         if( isset( $this->items['ukeplan'][$prodno][$referenceid]['maskit'] ) && is_array( $this->items['ukeplan'][$prodno][$referenceid]['maskit'] ) ) {
            unset( $this->items['ukeplan'][$prodno][$referenceid]['maskit'] );
            $this->recalculateTotals();
            return true;
            
         }
         
         return false;
         
      }
      
      
      
      
      public function setPrecent( $refid, $precent ){
         
         $campaign = new DiscountCampaign( $refid );
         
         $res['info'] = $campaign->asArray();
         $res['products'] = DiscountCampaign::enumRefId( $refid );
         $totalcartdiscount = 0;
      
         foreach( $this->items as $producttype => $product ) {
            
               foreach( $product as $prodno => $proddata ) {
                  $totalprice = 0;
                  
                  if($proddata['refid']){
                     $quantity += $proddata['quantity'];
                     $refid = $proddata['refid'];
                     $unitprice += $proddata['unitprice'];
                     $totalprice += $unitprice;
                     $productoption = ProductOption::fromRefId( $refid );
                     $productobject = new Product( $productoption->productid );
                     
                     if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {
                              //$unitdiscountpercent = CartDiscount::evalQuantum( $quantity, $quantums[$refid] );
                              $unitdiscountpercent = $precent;
                              $unitdiscountprice = round( ( $unitprice * $unitdiscountpercent / 100 ), 2 );
                              
                              
                              $totalproductdiscount = $quantity * $unitdiscountprice;
         
                              $item = array();
                              $item['quantity'] = $quantity;
                              $item['refid'] = $productoption->refid;
                              $item['product'] = $productobject->asArray();
                              $item['regularprice'] = $unitprice;
                              $item['regulartotalprice'] = $totalprice;
                              $item['unitdiscount'] = round( $unitdiscountprice, 2 );
                              $item['totaldiscount'] = round( $totalproductdiscount, 2 );
                              
                              if( $item['totaldiscount'] > 0 ) {
                                 $res['final'] [$proddata['referenceid']]= $item;
                                 $calculated []= $campaignproduct;
                                 $totalcartdiscount += $item['totaldiscount'];
                              }
      
                     }
                     
                     unset( $quantity );
                     unset( $refid );
                     unset( $unitprice );
                     unset( $totalprice );
                     
                     
                     
                  }else{
                     
                     if( is_array($proddata) ){ 
                        foreach( $proddata as $referenceid => $referencedata ) {
                           
                              $quantity += $referencedata['quantity'];
                              $refid = $referencedata['refid'];
                              $unitprice += $referencedata['unitprice'];
                              $totalprice += $unitprice;
                           
                           
                           
                           $productoption = ProductOption::fromRefId( $refid );
                           $productobject = new Product( $productoption->productid );
                           
                           if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {
                                 //$unitdiscountpercent = CartDiscount::evalQuantum( $quantity, $quantums[$refid] );
                                 $unitdiscountpercent = $precent;
                                 $unitdiscountprice = round( ( $unitprice * $unitdiscountpercent / 100 ), 2 );
                                 
                                 
                                 $totalproductdiscount = $quantity * $unitdiscountprice;
            
                                 $item = array();
                                 $item['quantity'] = $quantity;
                                 $item['refid'] = $productoption->refid;
                                 $item['product'] = $productobject->asArray();
                                 $item['regularprice'] = $unitprice;
                                 $item['regulartotalprice'] = $totalprice;
                                 $item['unitdiscount'] = round( $unitdiscountprice, 2 );
                                 $item['totaldiscount'] = round( $totalproductdiscount, 2 );
                                 
                                 if( $item['totaldiscount'] > 0 ) {
                                    $res['final'] [$referencedata['referenceid']]= $item;
                                    $calculated []= $campaignproduct;
                                    $totalcartdiscount += $item['totaldiscount'];
                                 }
         
                           }
                             
                           
                           
                           unset( $quantity );
                           unset( $refid );
                           unset( $unitprice );
                           unset( $totalprice );
                        }
                     }
                  }
               }
         }
         
         
         if( (  $this->deliveryType['price']  > 0  ||  $this->paymentType['price']  > 0 ) && $precent == 100  ){
            
            $unitdiscountprice = 0;
            
            $unitprice = $this->paymentType['price'] + $this->deliveryType['price'];
            
            if( $this->paymentType['price']  > 0 ){
                $unitdiscountprice  += round( ( $this->paymentType['price'] * $unitdiscountpercent / 100 ), 2 );
            }
            if( $this->deliveryType['price']  > 0 ){
                $unitdiscountprice  += round( ( $this->deliveryType['price'] * $unitdiscountpercent / 100 ), 2 );
            };
   
            $item = array();
            $item['quantity'] = $quantity;
            $item['refid'] = 127;
            $item['regularprice'] = $unitprice;
            $item['regulartotalprice'] = $unitprice;
            $item['unitdiscount'] = round( $unitdiscountprice, 2 );
            $item['totaldiscount'] = round( $unitdiscountprice, 2 );
            
            
            $item['product'] = array(
                            'id' => 127,
                            'title' => "Porto og Ekpedisjonsgebyr",
                            );
            
            if( $item['totaldiscount'] > 0 ) {
               $res['final'] [$item['refid']]= $item;
               $calculated []= $campaignproduct;
               $totalcartdiscount += $item['totaldiscount'];
            }
         
         }
         
         if(  $this->paymentType['price']  > 0 ){
            $item['totaldiscount'] += round( ( $this->deliveryType['price']  * $unitdiscountpercent / 100 ), 2 );
         }
         
         $this->discount = $res;
         
      }
      
      
            /**
       * Calculate discount for the cart
       *
       * @param array $ref
       * @return array
       */
      public function addCartDiscountDM( $ref ) {

         $res = array();
         $totalcartdiscount = 0;
         

         
         // Reset discount from cart
         if( count( $this->discount['final'] ) ) {
            foreach( $this->discount['final'] as $partdiscount ) {
               $totalcartdiscount += $partdiscount['totaldiscount'];
            }
         }
         
         $totalcartdiscount = 0;
         unset( $this->discount );
         
         
         if( $ref['id'] > 0 ) {
            
            $campaign = new DiscountCampaign( $ref['id'] );
            $res['info'] = $campaign->asArray();
            $res['products'] = DiscountCampaign::enumRefId( $ref['id'] );
              
         }
         if( count( $res['products'] ) > 0 ) {

            $quantums = DBDiscountQuantum::fromCampaignId( $ref['id'] );
            $quantumsarray = array_keys( $quantums );
            $calculated = array();
            
            foreach( $this->items as $producttype => $product ) {
               
               if( !in_array( $producttype, array( 'productionmethod', 'correctionmethod', 'paperquality' ) ) ) {
                  
                  foreach( $product as $prodno => $proddata ) {
                  
                     $creditquantity = 0;
                     
                     // Prints, goods, enlargements
                     if( isset( $proddata['refid'] ) ) {

                        if( in_array( $proddata['refid'], $quantumsarray ) ) {
                           
                           $productoption = ProductOption::fromRefId( $proddata['refid'] );
                           $productobject = new Product( $productoption->productid );
                           
                           if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {

                              if( !in_array( $productoption->refid, $calculated ) && in_array( $productoption->refid, $res['products'] ) ) {

                                 if( count( $this->credits ) > 0 ) {
                                    foreach( $this->credits as $creditkey => $creditproduct ) {
                                       if( $creditproduct['refid'] == $proddata['refid'] ) {
                                          $creditquantity = $creditproduct['quantity'];
                                       }
                                    }
                                 }
                                 
                                 $quantity = $proddata['quantity'];
                                 
                                 if( $quantity > $creditquantity ) {
                                 
                                    if( !empty( $creditkey ) ) {
                                       $creditprice = round( ( $proddata['unitprice'] * $creditquantity ), 2 );
                                       $this->credits[$creditkey]['price'] = $creditprice;
                                    }
                                    
                                    $quantity = $quantity - $creditquantity;
                                    $unitdiscountpercent = CartDiscount::evalQuantum( $quantity, $quantums[$proddata['refid']] );
                                    $unitdiscountprice = round( ( $proddata['unitprice'] * $unitdiscountpercent ), 2 );
                                    $totalproductdiscount = $quantity * $unitdiscountprice;
   
                                    $item = array();
                                    $item['quantity'] = $quantity;
                                    $item['refid'] = $productoption->refid;
                                    $item['product'] = $productobject->asArray();
                                    $item['regularprice'] = $proddata['unitprice'];
                                    $item['regulartotalprice'] = $proddata['price'];
                                    $item['unitdiscount'] = round( $unitdiscountprice, 2 );
                                    $item['totaldiscount'] = round( $totalproductdiscount, 2 );
   
                                    if( $item['totaldiscount'] > 0 ) {
                                       $res['final'] []= $item;
                                       $calculated []= $campaignproduct;
                                       $totalcartdiscount += $item['totaldiscount'];
                                    }
                                 
                                 } else {
                                    
                                    if( !empty( $creditkey ) ) {
                                       $creditprice = round( ( $proddata['unitprice'] * $quantity ), 2 );
                                       $this->credits[$creditkey]['price'] = $creditprice;
                                    }
                                    
                                 }

                              }

                           }
                           
                        }
                        
                     } else {  // Mediaclip, gifts
                        
                        $totalprice = 0;
                        
                        foreach( $proddata as $referenceid => $referencedata ) {
                         
                           $quantity += $referencedata['quantity'];
                           $refid = $referencedata['refid'];
                           $unitprice = $referencedata['unitprice'];
                           $totalprice += $unitprice;
                             
                        }
                        
                        if( in_array( $refid, $quantumsarray ) ) {

                           $productoption = ProductOption::fromRefId( $refid );
                           $productobject = new Product( $productoption->productid );
                           
                           if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {

                              if( !in_array( $productoption->refid, $calculated ) && in_array( $productoption->refid, $res['products'] ) ) {

                                 $unitdiscountpercent = CartDiscount::evalQuantum( $quantity, $quantums[$refid] );
                                 $unitdiscountprice = round( ( $unitprice * $unitdiscountpercent ), 2 );
                                 $totalproductdiscount = $quantity * $unitdiscountprice;

                                 $item = array();
                                 $item['quantity'] = $quantity;
                                 $item['refid'] = $productoption->refid;
                                 $item['product'] = $productobject->asArray();
                                 $item['regularprice'] = $unitprice;
                                 $item['regulartotalprice'] = $totalprice;
                                 $item['unitdiscount'] = round( $unitdiscountprice, 2 );
                                 $item['totaldiscount'] = round( $totalproductdiscount, 2 );
                                 
                                 if( $item['totaldiscount'] > 0 ) {
                                    $res['final'] []= $item;
                                    $calculated []= $campaignproduct;
                                    $totalcartdiscount += $item['totaldiscount'];
                                 }

                              }

                           }
                           
                        }
                        
                        unset( $quantity );
                        unset( $refid );
                        unset( $unitprice );
                        unset( $totalprice );
                        
                     }
                     
                  }
                  
               }
               
            }
            
         }

         
         return $res;
         
      }
      
      /**
       * Calculate discount for the cart
       *
       * @param array $ref
       * @return array
       */
      public function addCartDiscount( $ref ) {

         $res = array();
         $totalcartdiscount = 0;
         
         if( Login::isLoggedIn() ) {
               
            $credit = new Credit();
            if( $credit instanceof Credit ) {
               $this->credits = $credit->enum( Login::userid() );
            }
               
         }
         
         // Reset discount from cart
         if( count( $this->discount['final'] ) ) {
            foreach( $this->discount['final'] as $partdiscount ) {
               $totalcartdiscount += $partdiscount['totaldiscount'];
            }
         }
         
         $totalcartdiscount = 0;
         unset( $this->discount );
         
         
         if( $ref['id'] > 0 ) {
            
            $campaign = new DiscountCampaign( $ref['id'] );
            $res['info'] = $campaign->asArray();
            $res['products'] = DiscountCampaign::enumRefId( $ref['id'] );
              
         }
         if( count( $res['products'] ) > 0 ) {

            $quantums = DBDiscountQuantum::fromCampaignId( $ref['id'] );
            $quantumsarray = array_keys( $quantums );
            $calculated = array();
            
            foreach( $this->items as $producttype => $product ) {
               
               if( !in_array( $producttype, array( 'productionmethod', 'correctionmethod', 'paperquality' ) ) ) {
                  
                  foreach( $product as $prodno => $proddata ) {
                  
                     $creditquantity = 0;
                     
                     // Prints, goods, enlargements
                     if( isset( $proddata['refid'] ) ) {

                        if( in_array( $proddata['refid'], $quantumsarray ) ) {
                           
                           $productoption = ProductOption::fromRefId( $proddata['refid'] );
                           $productobject = new Product( $productoption->productid );
                           
                           if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {

                              if( !in_array( $productoption->refid, $calculated ) && in_array( $productoption->refid, $res['products'] ) ) {

                                 if( count( $this->credits ) > 0 ) {
                                    foreach( $this->credits as $creditkey => $creditproduct ) {
                                       if( $creditproduct['refid'] == $proddata['refid'] ) {
                                          $creditquantity = $creditproduct['quantity'];
                                       }
                                    }
                                 }
                                 
                                 $quantity = $proddata['quantity'];
                                 
                                 if( $quantity > $creditquantity ) {
                                 
                                    if( !empty( $creditkey ) ) {
                                       $creditprice = round( ( $proddata['unitprice'] * $creditquantity ), 2 );
                                       $this->credits[$creditkey]['price'] = $creditprice;
                                    }
                                    
                                    $quantity = $quantity - $creditquantity;
                                    $unitdiscountpercent = CartDiscount::evalQuantum( $quantity, $quantums[$proddata['refid']] );
                                    $unitdiscountprice = round( ( $proddata['unitprice'] * $unitdiscountpercent ), 2 );
                                    $totalproductdiscount = $quantity * $unitdiscountprice;
   
                                    $item = array();
                                    $item['quantity'] = $quantity;
                                    $item['refid'] = $productoption->refid;
                                    $item['product'] = $productobject->asArray();
                                    $item['regularprice'] = $proddata['unitprice'];
                                    $item['regulartotalprice'] = $proddata['price'];
                                    $item['unitdiscount'] = round( $unitdiscountprice, 2 );
                                    $item['totaldiscount'] = round( $totalproductdiscount, 2 );
   
                                    if( $item['totaldiscount'] > 0 ) {
                                       $res['final'] []= $item;
                                       $calculated []= $campaignproduct;
                                       $totalcartdiscount += $item['totaldiscount'];
                                    }
                                 
                                 } else {
                                    
                                    if( !empty( $creditkey ) ) {
                                       $creditprice = round( ( $proddata['unitprice'] * $quantity ), 2 );
                                       $this->credits[$creditkey]['price'] = $creditprice;
                                    }
                                    
                                 }

                              }

                           }
                           
                        }
                        
                     } else {  // Mediaclip, gifts
                        
                        $totalprice = 0;
                        
                        foreach( $proddata as $referenceid => $referencedata ) {
                         
                           $quantity += $referencedata['quantity'];
                           $refid = $referencedata['refid'];
                           $unitprice = $referencedata['unitprice'];
                           $totalprice += $unitprice;
                             
                        }
                        
                        if( in_array( $refid, $quantumsarray ) ) {

                           $productoption = ProductOption::fromRefId( $refid );
                           $productobject = new Product( $productoption->productid );
                           
                           if( $productoption instanceof ProductOption && $productoption->isLoaded() ) {

                              if( !in_array( $productoption->refid, $calculated ) && in_array( $productoption->refid, $res['products'] ) ) {

                                 $unitdiscountpercent = CartDiscount::evalQuantum( $quantity, $quantums[$refid] );
                                 $unitdiscountprice = round( ( $unitprice * $unitdiscountpercent ), 2 );
                                 $totalproductdiscount = $quantity * $unitdiscountprice;

                                 $item = array();
                                 $item['quantity'] = $quantity;
                                 $item['refid'] = $productoption->refid;
                                 $item['product'] = $productobject->asArray();
                                 $item['regularprice'] = $unitprice;
                                 $item['regulartotalprice'] = $totalprice;
                                 $item['unitdiscount'] = round( $unitdiscountprice, 2 );
                                 $item['totaldiscount'] = round( $totalproductdiscount, 2 );
                                 
                                 if( $item['totaldiscount'] > 0 ) {
                                    $res['final'] []= $item;
                                    $calculated []= $campaignproduct;
                                    $totalcartdiscount += $item['totaldiscount'];
                                 }

                              }

                           }
                           
                        }
                        
                        unset( $quantity );
                        unset( $refid );
                        unset( $unitprice );
                        unset( $totalprice );
                        
                     }
                     
                  }
                  
               }
               
            }
            
         }

         
         return $res;
         
      }
      
      public function removeDiscount(){
         unset($this->discount );
         Util::debug( $this->discount );
      }
      
      
      public function setDiscount( $discount = array() ) {
         
         if( count( $discount['info'] ) > 0 ) {
            $this->discount = $discount;
         }
         
      }
      
      
      public function validate() {
         
         if( (int) $this->version < 2 ) {

            $upgraded = $this->upgrade();
            if( !empty( $upgraded['id'] ) ) {
               $discount = $this->addCartDiscount( $upgraded['id'] );
               $this->setDiscount( $discount );
            } 
            
            $this->version = 2;
            $this->save();
            
         }
         
         $this->recalculateTotals();
         
      }
      
      
      
      
      public function upgrade() {
         
         $upgraded = array(
            'result' => false,
         );
         
         if( count( $this->items ) > 0 ) {
            
            foreach( $this->items as $productType => $data ) {
               
               switch( $productType ) {
                  
                  case 'prints':
                  case 'enlargements':
                     if( count( $data ) > 0 ) {
                  
                        if( isset( $this->items[$productType][''] ) ) {
                           unset( $this->items[$productType][''] );
                           unset( $data[$productType][''] );
                        }
                        
                        
                        foreach( $data as $prodno => $proddata ) {
                     
                           if( is_array( $proddata['discount'] ) ) {
                              $images = $proddata['images'];
                              $quantity = $proddata['quantity'];
                              $this->removeItem( $prodno );
                              $this->addItem( "$prodno", $quantity, array( 'images' => $images ) );
                              $upgraded = array(
                                 'result' => true,
                                 'id' => $proddata['discount']['campaign']['id'],
                              );
                           }
                     
                        }
                  
                     }
                     break;
                  case 'mediaclip':
                     if( count( $data ) > 0 ) {
                  
                        if( isset( $this->items[$productType][''] ) ) {
                           unset( $this->items[$productType][''] );
                           unset( $data[$productType][''] );
                        }
                        
                        foreach( $data as $prodno => $proddata ) {
                     
                           if( count( $proddata ) > 0 ) {
                              
                              if( is_array( $proddata['discount'] ) ) {
                              
                                 foreach( $proddata as $referenceid => $referencedata ) {
                                    
                                    // Used for user that are not logged in
                                    if( Login::isLoggedIn() ) {
                                       $userid = Login::userid();
                                    } else {
                                       $userid = 639866;
                                    }
   
   
                                    $attributes = array(
                                       "userid" => $userid,
                                       "projectid" => $referenceid,
                                       "title" => $referencedata['title']."<splitter>".$referenceid,
                                       "extrapages" => $referencedata['extrapages']['uniquequantity'],
                                    );
                                    
                                    $quantity = $referencedata['quantity'];
                                    $productoptionid = $referencedata['optionid'];
   
                                    $this->removeItem( $prodno, $referenceid );
                                    $this->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
   
                                    $upgraded = array(
                                       'result' => true,
                                       'id' => $proddata['discount']['campaign']['id'],
                                    );
   
                                    unset( $this->items[$productType][$prodno][$referenceid]['discount'] );
                                    unset( $this->items[$productType][$prodno]['discount'] );
                                    
                                 }
                              
                              } 
                              
                           }
                     
                        }
                  
                     }
                     break;
                     
                  case 'gifts':
                     if( count( $data ) > 0 ) {
                  
                        if( isset( $this->items[$productType][''] ) ) {
                           unset( $this->items[$productType][''] );
                           unset( $data[$productType][''] );
                        }
                        
                        foreach( $data as $prodno => $proddata ) {
                     
                           if( count( $proddata ) > 0 ) {
                              
                              if( is_array( $proddata['discount'] ) ) {
                              
                                 foreach( $proddata as $referenceid => $referencedata ) {
                                    
                                    $productoptionid = $referencedata['optionid'];
                                    $quantity = $referencedata['quantity'];
                                    $redeye = $referencedata['redeyeremoval'];
                                    $attributes = array( 
   		                                  "templateorderid" => $referenceid,
   		                                  "redeyeremoval" => $redeye ? true : false,
   		                           );
   		                           
                                    $this->removeItem( $prodno, $referenceid );
                                    $this->addItemByProductOptionId( $productoptionid, $quantity, $attributes );
   
                                    $upgraded = array(
                                       'result' => true,
                                       'id' => $proddata['discount']['campaign']['id'],
                                    );
   
                                    unset( $this->items[$productType][$prodno][$referenceid]['discount'] );
                                    unset( $this->items[$productType][$prodno]['discount'] );
                                    
                                 }
                              
                              } 
                              
                           }
                     
                        }
                  
                     }
                     break;
                  default:
                     break;
                  
               }
               
               
            }
            
            
         }
         
         
         return $upgraded;
         
      }
      
      
      public function recalculateTotals() {
         
         
         switch( $this->discount['info']['id']  ){
            case 409:
            case 457:
            case 518:
               $this->setPrecent( $this->discount['info']['id'], 10 );
               break;
            case 422:
               $this->setPrecent( $this->discount['info']['id'], 15 );
               break;
            case 522:
            case 514:
               $this->setPrecent( $this->discount['info']['id'], 20 );
               break;
            case 382:
            case 390:
               $this->setPrecent( $this->discount['info']['id'], 25 );
               break;
            case 512:
            case 524:
               $this->setPrecent( $this->discount['info']['id'], 30 );
               break;
            case 513:
               $this->deliveryType['price']  = 0 ;
               $this->freeShippingArray = array(1);
               break;  
               
         }
         
         if(  date('Y-m-d') > '2015-12-26' && date('Y-m-d') < '2016-01-05 07:00:00' && ( Dispatcher::getPortal() == '' || Dispatcher::getPortal() == 'SP-001' ) ){
            $this->setPrecent( 524, 30 );
         }
         
         if( count( $this->credits ) == 0 && Login::isLoggedIn() && $this->totalItems > 0 ) {
            $credit = new Credit();
            if( $credit instanceof Credit ) {
               $tmpcredits = $credit->enum( Login::userid() );
               if( count( $tmpcredits ) ) {
                  $this->credits = $tmpcredits;
               }
            }
         }
         
         
         $totalprice = 0;
         $cartprice = 0; // The price without delivery or payment prices
         $totalitems = 0;
         $totalweight = 0;
         
                  
         
         if( count( $this->items ) > 0 ) {
            
            foreach( $this->items as $producttype => $products ) {

               switch( $producttype ) {
                  
                  case 'prints':
                  case 'enlargements':
                  case 'goods':
                  case 'subscription':

                     if( count( $this->items[$producttype] ) > 0 ) {
                        
                        foreach( $this->items[$producttype] as $prodno => $productdata ) {
                           if( empty($prodno) ) {
                              unset( $this->items[$producttype][$prodno] );
                           } else {
                              
                              if( count( $this->credits ) > 0 ) {
                                 
                                 $allcredits = array();
                                 $usedcredits = 0;
                                 $leftcredits = 0;
                                 
                                 $itemquantity = $productdata['quantity'];

                                 if( is_array( $this->credits ) ){
                                    foreach( $this->credits as $creditkey => $creditdata ) {
                                       
                                       if( $creditdata['refid'] == $this->items[$producttype][$prodno]['refid'] ) {
                                          $allcreditsrefid = $creditdata['refid'];
                                          if( !isset( $allcredits[$allcreditsrefid] ) ) {
                                             $allcredits[$allcreditsrefid] = $creditdata['quantity'];
                                          } else {
                                             $allcredits[$allcreditsrefid] += $creditdata['quantity'];
                                          }
                                       }
                                       
                                    }
                                 
                                 }
                                 
                                 if( $allcredits[$allcreditsrefid] >= $productdata['quantity'] ) {
                                    $leftcredits = $productdata['quantity'];
                                 } else {
                                    $leftcredits = $allcredits[$allcreditsrefid];
                                 }
                                 if( is_array( $this->credits ) ){
                                 foreach( $this->credits as $creditkey => $creditdata ) {

                                    if( $creditdata['refid'] == $productdata['refid'] ) {

                                       if( $leftcredits > 0 ) {
                                          
                                          if( $productdata['quantity'] <= $creditdata['quantity'] ) {
                                             
                                             if( $leftcredits <= $productdata['quantity'] ) {
                                                $creditquantity = $leftcredits;
                                             } else {
                                                $creditquantity = $productdata['quantity'];
                                             }
                                          } else {

                                             if( $leftcredits <= $creditdata['quantity'] ) {
                                                $creditquantity = $leftcredits;
                                             } else {
                                                $creditquantity = $creditdata['quantity'];
                                             }
                                          }
                                          
                                          $usedcredits += $creditquantity;
                                          $leftcredits -= $creditquantity;
                                          $allcredits[$creditdata['refid']] -= $creditquantity;
                                          $this->credits[$creditkey]['usedquantity'] = $creditquantity;
                                          $this->credits[$creditkey]['usedprice'] = round( ( $creditquantity * $productdata['unitprice'] ), 2 );
                                          
                                       }
                                       
                                    }
                                 }
                                 }
                                 
                              }
                              
                              //die();
                              
                              $totalprice += $this->items[$producttype][$prodno]['price'];
                              $cartprice += $this->items[$producttype][$prodno]['price'];
                              
                              if( count( $this->items[$producttype][$prodno]['license'] ) > 0 ) {
                                 
                                 foreach( $this->items[$producttype][$prodno]['license'] as $licenseimageid => $licensevalues ) {
                                    
                                    if( $this->items[$producttype][$prodno]['license'][$licenseimageid]['totalfee'] > 0 )  {
                                       $totalprice += $licensevalues['totalfee'];
                                       $cartprice += $licensevalues['totalfee'];
                                    }
                                    
                                 }
                                 
                              }
                              
                              $totalweight += $this->items[$producttype][$prodno]['weight'];
                              $totalitems += 1;
                           }
                        }
                        
                     }
                     break;
                     
                  case 'gifts':
                  case 'mediaclip':
                  case 'ukeplan':
                  case 'merkelapp':
                  case 'stempel':
                  case 'smilesontiles':
                  case 'textgift':
                  case 'module':
                  case 'digital':
                     if( count( $this->items[$producttype] ) > 0 ) {
                        
                        foreach( $this->items[$producttype] as $prodno => $productdata ) {
                           if( empty( $prodno ) ) {
                              unset( $this->items[$producttype][$prodno] );
                           } else {
                              
                              if( count( $this->items[$producttype][$prodno] ) ) {
                                 $usedrefs = array();
                                 foreach( $this->items[$producttype][$prodno] as $referenceid => $referencedata ) {
                                    
                                    
                                    $creditquantity = isset( $this->credits[$creditkey]['usedquantity'] ) ? $this->credits[$creditkey]['usedquantity'] : 0;
                                    
                                    
                                    if( empty( $referenceid ) ) {
                                       unset( $this->items[$producttype][$prodno][$referenceid] );
                                    } else {
                                       
                                       if( count( $this->credits ) > 0 && is_array( $this->credits ) ) {
                                          
                                          foreach( $this->credits as $creditkey => $creditdata ) {
                                             
                                             if( !in_array( $referenceid, $usedrefs ) ) {
                                                
                                                if( $creditdata['refid'] == $referencedata['refid'] ) {
                                                   if( ( $creditquantity + $referencedata['quantity'] ) <= $creditdata['quantity'] ) {
                                                      $creditquantity += $referencedata['quantity'];
                                                   } else {
                                                      $creditquantity = $creditdata['quantity'];
                                                   }
                                                   
                                                   $usedrefs []= $referenceid;
                                                   $this->credits[$creditkey]['usedquantity'] = $creditquantity;
                                                   $this->credits[$creditkey]['usedprice'] = round( ( $creditquantity * $referencedata['unitprice'] ), 2 );
                                                   
                                                }
                                             } 
                                          }
                                       }
                                       
                                       // Special for gifts
                                       if( $producttype == 'gifts' ) {
                                          if( isset( $this->items[$producttype][$prodno][$referenceid]['redeyeremoval'] ) ) {
                                             $totalprice += $this->items[$producttype][$prodno][$referenceid]['redeyeremoval']['price'];
                                             $cartprice += $this->items[$producttype][$prodno][$referenceid]['redeyeremoval']['price'];
                                          }
                                          if( isset( $this->items[$producttype][$prodno][$referenceid]['varnish'] ) ) {
                                             $totalprice += $this->items[$producttype][$prodno][$referenceid]['varnish']['price'];
                                             $cartprice += $this->items[$producttype][$prodno][$referenceid]['varnish']['price'];
                                          }
                                          if( isset( $this->items[$producttype][$prodno][$referenceid]['upgrade'] ) ) {
                                             $totalprice += $this->items[$producttype][$prodno][$referenceid]['upgrade']['price'];
                                             $cartprice += $this->items[$producttype][$prodno][$referenceid]['upgrade']['price'];
                                          }
                                       }
                                       
                                       if( $producttype == 'ukeplan' ) {
                                          if( isset( $this->items[$producttype][$prodno][$referenceid]['maskit'] ) ) {
                                             $totalprice += $this->items[$producttype][$prodno][$referenceid]['maskit']['price'];
                                             $cartprice += $this->items[$producttype][$prodno][$referenceid]['maskit']['price'];
                                          }
                                       }
                                       
                                       if( $producttype == 'mediaclip' ) {
                                          // Special for extrapages
                                          if( isset( $this->items[$producttype][$prodno][$referenceid]['extrapages'] ) ) {
                                             $totalprice += $this->items[$producttype][$prodno][$referenceid]['extrapages']['price'];
                                             $cartprice += $this->items[$producttype][$prodno][$referenceid]['extrapages']['price'];
                                             $totalweight += $this->items[$producttype][$prodno][$referenceid]['extrapages']['weight'];
                                          }
                                          // Special for redeyeremoval
                                          if( isset( $this->items[$producttype][$prodno][$referenceid]['redeyeremoval'] ) ) {
                                             $totalprice += $this->items[$producttype][$prodno][$referenceid]['redeyeremoval']['price'];
                                             $cartprice += $this->items[$producttype][$prodno][$referenceid]['redeyeremoval']['price'];
                                          }
                                       }
                                       
                                       // Add to totals
                                       $totalprice += $this->items[$producttype][$prodno][$referenceid]['price'];
                                       $cartprice += $this->items[$producttype][$prodno][$referenceid]['price'];
                                       
                                       if( is_array( $this->items[$producttype][$prodno][$referenceid]['license'] ) ) {
                                          
                                          foreach( $this->items[$producttype][$prodno][$referenceid]['license'] as $licenseimageid => $licensevalues ) {
                                             
                                             if( $licensevalues['totalfee'] > 0 ) {
                                                $totalprice += $licensevalues['totalfee'];
                                                $cartprice += $licensevalues['totalfee'];
                                             }
                                             
                                          }
                                          
                                       }
                                       
                                       $totalweight += $this->items[$producttype][$prodno][$referenceid]['weight'];
                                       $totalitems += 1;
                                    }
                                    
                                 }
                              }
                           }
                           
                        }
                        
                     }
                     break;
                  case 'productionmethod':
                  case 'correctionmethod':
                  case 'paperquality':
                      if( $this->items[$producttype]['price'] > 0 ) {
                         $totalprice +=  $this->items[$producttype]['price'];
                         $cartprice +=  $this->items[$producttype]['price'];
                      }
                      break;
                      
                  default:
                     break;
                    
                  
               }
               
            }
            
         }
         
         // update the cart discount
         if( count( $this->discount['final'] ) > 0 ) {
            $totaldiscount = 0;
            if( is_array( $this->discount['final'] ) ) {
               foreach( $this->discount['final'] as $rowid => $discountrow ) {
                  $totaldiscount += $this->discount['final'][$rowid]['totaldiscount'];
               }
            }
            $totalprice -= $totaldiscount;
            $cartprice -= $totaldiscount;
         }
         
         
         // Update cart credits
         if( count( $this->credits ) > 0 ) {
            $totalcredit = 0;
            if( is_array( $this->credits ) ) {
               foreach( $this->credits as $credits ) {
                  $totalcredit += round( $credits['usedprice'], 2 );
               }
            }
            $totalprice -= $totalcredit;
            $cartprice -= $totalcredit;
         }

         $changed = false;
         $checkpaths = array( 'cart', 'checkout', 'delivery-method', 'payment-method' );
         $execpath = Dispatcher::getExecPath();
         $execpath = end( $execpath );
         
         if( in_array( $execpath, $checkpaths ) ) {

            if( $cartprice > 0 && is_array( $this->giftcard ) && count( $this->giftcard ) > 0 && $this->giftcard['value'] > 0 ) {
            
               if( $cartprice >= $this->giftcard['value'] ) {
                  $cartprice = round( $cartprice - $this->giftcard['value'], 2 );
                  $totalprice = round( $totalprice - $this->giftcard['value'], 2 );
                  $this->giftcard['usedvalue'] = $this->giftcard['value'];
                  $changed = true;
               } else {
                  $this->giftcard['usedvalue'] = $cartprice;
                  $cartprice = 0.00;
                  $totalprice = 0.00;
                  $changed = true;
               }
               
            }
            
         }
         
         
         // Add cost of delivery method choosen
         if( isset( $this->deliveryType['price'] ) ) {
            $totalprice +=  $this->deliveryType['price'];
         }
         
         // Add cost of payment method choosen
         if( isset( $this->paymentType['price'] ) ) {
            $totalprice +=  $this->paymentType['price'];
         }
         
         if( !in_array( $execpath, $checkpaths ) ) {

            if( $totalprice > 0 && is_array( $this->giftcard ) && count( $this->giftcard ) > 0 && $this->giftcard['value'] > 0 ) {
               
               if( $totalprice >= $this->giftcard['value'] ) {
                  $cartprice = round( $cartprice -= $this->giftcard['value'], 2 );
                  $totalprice = round( $totalprice -= $this->giftcard['value'], 2 );
                  $this->giftcard['usedvalue'] = $this->giftcard['value'];
                  $changed = true;
               } else {
                  $this->giftcard['usedvalue'] = $totalprice;
                  $cartprice = 0.00;
                  $totalprice = 0.00;
                  $changed = true;
               }
               
            }
         }
         
         
         if( $this->totalPrice != $totalprice ) $changed = true;
         if( $this->totalWeight != $totalweight ) $changed = true;
         if( $this->cartPrice != $cartprice ) $changed = true;
         if( $this->totalItems != $totalitems ) $changed = true;
         $this->totalPrice = $totalprice;
         $this->totalWeight = $totalweight;
         $this->cartPrice = $cartprice;
         $this->totalItems = $totalitems;
         
         // only save if changed
         if( $changed ) {
            
            // save the cart
            $this->save();
         }
         
      }
      
      public function useGiftcard( $giftcard ) {
         
         $this->giftcard = $giftcard;
         $this->recalculateTotals();
         
      }
      
      public function hasGiftcardActivated() {
         
         if( count( $this->giftcard ) > 0 ) return true;
         
         return false;
         
      }
      
      
      public function togglePrintGiftcard( $prodno, $status ) {

         if( is_array( $this->items['goods'][$prodno]['giftcard'] ) ) {
            if( $status == true ) {
               $this->items['goods'][$prodno]['giftcard'] = array( 'print' => true );
            } else {
               unset( $this->items['goods'][$prodno]['giftcard']['print'] );
            }
            
         }
      }
      
      public function updateGiftcard() {
         
         relocate( '/cart/giftcard'.$this->giftcard['code'] );
         
      }
      
      
      public function getGiftcard() {
         
         return $this->giftcard;
         
      }
      
      // Save the not logged in basket to user
      // session array for later use.
      static function setMergeBasket() {
         
         $tmpcart = new Cart();
         if( $tmpcart->getTotalItems() > 0 ) {
            UserSessionArray::addItem( 'mergebasket', $tmpcart );
            $tmpcart->clear();
         }
         
      }
      
      
      // Merge the not logged in basket with
      // the users logged in basket.
      static function mergeBasket() {
         
         $cart = UserSessionArray::getItems( 'mergebasket' );
         
         if( isset( $cart ) ) {
            $cart = $cart[0];
         }
         
         if( Login::isLoggedIn() && $cart instanceof Cart ) {
            
            $cart = $cart->asArray();
            if( count( $cart['items'] ) > 0 ) {
               
               $mergedcart = new Cart();
               
               foreach( $cart['items'] as $producttype => $products ) {
                  
                  switch( $producttype ) {
                     
                     case 'prints':
                        foreach( $cart['items']['prints'] as $prodno => $product ) {
                           
                           $qty = $product['quantity'];
                           $attributes['images'] = $product['images'];
                           $mergedcart->addItem( $prodno, $qty, $attributes );
                           
                        }
                        break;
                     case 'gifts':
                        foreach( $cart['items']['gifts'] as $prodno => $products ) {
                           
                           foreach( $products as $referenceid => $product ) {
                              
                              $qty = $product['quantity'];
                              $attributes['templateorderid'] = $referenceid;
                              if( is_array( $product['redeyeremoval'] ) ) $attributes['redeyeremoval'] = true;
                              
                              try {
                                 $mergedcart->addItem( $prodno, $qty, $attributes );
                              } catch ( Exception $e ) { util::Debug( $e->getMessage() ); die(); }
                              
                           }
                           
                        }
                        
                     break;
                      case 'textgift':
                        foreach( $cart['items']['textgift'] as $prodno => $products ) {
                           
                           foreach( $products as $referenceid => $product ) {
                              $qty = $product['quantity'];
                              
                              try {
                                 //$mergedcart->addItem( $prodno, $qty, $product['attributes'] );
                                 $mergedcart->addItemByProductOptionId( $product['optionid'], $qty, $product['attributes']);
                              } catch ( Exception $e ) { util::Debug( $e->getMessage() ); die(); }
                              
                           }
                           
                        }
                        
                        break;
                     case 'merkelapp':
                        foreach( $cart['items']['merkelapp'] as $prodno => $products ) {
                           
                           foreach( $products as $referenceid => $product ) {
                              
                              $qty = $product['quantity'];
                              $attributes["projectid"] = $referenceid;
                              
                              try {
                                 $mergedcart->addItem( $prodno, $qty, $attributes );
                              } catch ( Exception $e ) { util::Debug( $e->getMessage() ); die(); }
                              
                           }
                           
                        }
                        break;
                     case 'stempel':
                        foreach( $cart['items']['stempel'] as $prodno => $products ) {
                           
                           foreach( $products as $referenceid => $product ) {
                              
                              $qty = $product['quantity'];
                              $attributes["projectid"] = $referenceid;
                              
                              try {
                                 $mergedcart->addItem( $prodno, $qty, $attributes );
                              } catch ( Exception $e ) { util::Debug( $e->getMessage() ); die(); }
                              
                           }
                           
                        }
                        break;
                     case 'ukeplan':
                        foreach( $cart['items']['ukeplan'] as $prodno => $products ) {
                           
                           foreach( $products as $referenceid => $product ) {
                              $qty = $product['quantity'];
                              $attributes['projectid'] = $referenceid;
                              
                              if( is_array( $product['maskit'] ) ) $attributes['maskit'] = true;
                              
                              try {
                                 $mergedcart->addItem( $prodno, $qty, $attributes );

                              } catch ( Exception $e ) { util::Debug( $e->getMessage() ); die(); }
                              
                           }

                           
                        }
                        
                     break;   
                     case 'mediaclip':
                        foreach( $cart['items']['mediaclip'] as $prodno => $products ) {
                           
                           foreach( $products as $referenceid => $product ) {
                              
                              $qty = $product['quantity'];
                              $attributes['projectid'] = $referenceid;
                              if( is_array( $product['redeyeremoval'] ) ) $attributes['redeyeremoval'] = true;
                              if( is_array( $product['extrapages'] ) ) $attributes['extrapages'] = $product['extrapages']['uniquequantity'];
                              $mergedcart->addItem( $prodno, $qty, $attributes );
                              
                           }
                           
                        }
                        break;
                     case 'smilesontiles':
                        foreach( $cart['items']['smilesontiles'] as $prodno => $products ) {
                           foreach( $products as $referenceid => $product ) {
                              $qty = $product['quantity'];
                              $attributes['projectid'] = $referenceid;
                              $attributes['price'] = $product['unitprice'];
                              if( is_array( $product['redeyeremoval'] ) ) $attributes['redeyeremoval'] = true;
                              $mergedcart->addItem( $prodno, $qty, $attributes );
                           }
                           
                        }
                        break;
                     case 'goods':
                        foreach( $cart['items']['goods'] as $prodno => $product ) {
                           
                           $qty = $product['quantity'];
                           if( is_array( $product['set'] ) ) {
                              $attributes = array(
                                 'set' => true,
                                 'totalitemquantity' => $product['set']['totalitemquantity'],
                                 'setitemquantity' => $product['set']['setitemquantity'],
                                 'type' => $product['set']['type'],
                              );
                           }
                           
                           $mergedcart->addItem( $prodno, $qty, $attributes );
                           
                        }
                        break;
                     default:
                        break;
                     
                  }
                  
               }
               
               
               // Reset all order attributes
               $mergedcart->removePrintAttribute( 'productionmethod' );
               $mergedcart->removePrintAttribute( 'correctionmethod' );
               $mergedcart->removePrintAttribute( 'paperquality' );
         
               // Add the production-, correctionmethods and paperquality to cart
               if( isset( $cart['items']['productionmethod']['prodno'] ) ) {
                  $mergedcart->addPrintAttribute( $cart['items']['productionmethod']['prodno'] );
               }
         
               if( isset( $cart['items']['correctionmethod']['prodno'] ) ) {
                  $mergedcart->addPrintAttribute( $cart['items']['correctionmethod']['prodno'] );
               } 
         
               if( isset( $cart['items']['paperquality']['prodno'] ) ) {
                  $mergedcart->addPrintAttribute( $cart['items']['paperquality']['prodno'] );
               }
               
               $mergedcart->recalculateTotals();
               $mergedcart->save();
               
            }
         
         }
         
         UserSessionArray::clearItems( 'mergebasket' );
         
      }
      
      
      public function removeDeliveryType() {
         unset( $this->deliveryType );
      }
      
       public function removePaymentType() {
         unset( $this->paymentType );
      }
      
      
      public function setComment( $text ) {   
         $this->comment = $text;
      }
      
      public function getComment() {
         return $this->comment;
      }
      
      public function getContactInfo() {
         return $this->contactInfo;
      }
      
      public function getDeliveryInfo() {
         return $this->deliveryInfo;
      }
      
      public function setStore( $storeid ) {
         $this->storeid = $storeid;
      }
      
      public function setTrackingcode( $trackingcode ) {
         $this->trackingcode = $trackingcode;
      }
      
      public function unsetStore() {
         unset( $this->storeid );
      }
      
      public function getStore() {
         return $this->storeid;
      }
      
   }


?>