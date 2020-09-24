s
<?php

   /**
    * 
    * Setup and execute manual orders from local files.
    *
    * 
    */
   import( 'website.credit' );
   import( 'website.order' );
   import( 'mail.send');
 // ikkje lengre i bruk  import( 'website.order.edi' );
   
   
   model( 'order.customer' );
   model( 'order.row' );
   model( 'order.delivery' );
   model( 'site.storeorder' );
   model( 'user.orders' );
   model( 'order.cduploads' );
   
   config( 'website.countries' );
   config( 'website.stores' );
   

   class ManualOrder{
      
      private $comment = '';
      private $orderid = null;
      private $user = null;
      private $cartarray = array();
      private $startlotnr = 0;
      private $hasaccessories = false;
      private $date = null;
      private $storedelivery = false;
      private $data = null;
      private $totalquantity = 0;
      private $kampanje = '';

      /**
       * Execute the order.
       * 
       * @author Tor Inge Løvland <tor.inge@eurofoto.no>
       */
      public function executeManualOrder( $data ) {
         $this->data = $data;
         $this->comment = $data['comment'];
         $this->kampanje = $data['kampanje'];
         $userID = $this->data['userid'];
         $this->user = new User( $userID );
         $rubid = (int) $this->user->rubid;
         
         if( $this->data['uploadimages'] == 'upload' ){   
            $this->registerUpload();
         }
         
         
         
         // No previous rubicon id
         // Create one
         if( $rubid < 1 ) {
            $rubid = $this->createNewRubiconId();
            $this->user->rubid = $rubid;
            $this->user->save();
         }
         // Create a new orderid for this order
         $this->userid     = $userID;
         $this->orderid    = isset( $orderid ) ? $orderid : $this->createNewOrderId();
         $this->startlotnr = $this->initLotNr();
         $this->date       = date( 'Y-m-d' );
         
         // Insert customer contact and delivery info
         $this->insertContactAndDeliveryData();
         $this->OrderItems();
         $items = array();
         $collection = array();
         $collectionarray = $this->items;
         
         try{
            foreach( $collectionarray as $type => $items ) {
   
               foreach( $items as $prodno => $item ) {
                  // For regular products
                  switch( $type ) {
                     case 'prints':
                     case 'enlargements':
                        
                        // imported above
                        import( 'website.order.manual.handlers.print' );
                        
                        // process the orderline
                        $handler = new ManualOrderHandlerPrints();
                        $handler->process( $this, $type, $item );
                        break;
                        
                     case 'gifts':  // Items gone throught the gift editor
                     
                        import( 'website.order.manual.handlers.gift' );
                        
                        // Can have multiple templates under the same product
                        if( count( $item ) > 0 ) {
                           foreach( $item as $gift ) {
                              
                              $handler = new ManualOrderHandlerGifts();
                              $handler->process( $this, $type, $gift );
                           }
                        }
                        break;
                        
                     /*case 'mediaclip': // Items gone throught mediaclip editor
                        
                        import( 'website.order.handlers.mediaclip' );
                     
                        if( count( $item ) > 0 ) {
                           foreach( $item as $mediaclip ) {
                              $handler = new OrderHandlerMediaclip();
                              $handler->process( $this, $type, $mediaclip );
                           }
                        }
                        break;*/
                        
                     case 'goods':
                        
                        import( 'website.order.handlers.goods' );
                        
                        $this->hasaccessories = true;
                        $handler = new OrderHandlerGoods();
                        $handler->process( $this, $type, $item );
                        break;
                        
                     case 'subscription':
                        
                        import( 'website.order.handlers.subscription' );
                        
                        $handler = new OrderHandlerSubscription();
                        $handler->process( $this, $type, $item );
                        break;
                        
                     default:
                        break;
   
                  }
   
               }
               
               // For methods not indexed in array
               switch( $type ) {
                  
                  case 'productionmethod':
                        $handler = new ManualOrderHandlerPrints();
                        $handler->process( $this, $type, $items );
                        break;
                     case 'correctionmethod':
                        $handler = new ManualOrderHandlerPrints();
                        $handler->process( $this, $type, $items );
                        break;
                     case 'paperquality':
                        $handler = new ManualOrderHandlerPrints();
                        $handler->process( $this, $type, $items );
                        break;
                     default:
                        break;
                  
               }
               
            }
         }catch ( Exception $e){
            util::Debug( $e->getMessage() );
         }

         #$this->insertCredits();
         #$this->insertDiscount();
         $this->insertShippingCost();
         //$this->mailConfirmation();
         $this->insertDeliveryOptions();
         if( $this->getStoreShopId() ) {
            $this->saveStoreDelivery();
         }
         $this->insertOrder();
       //  $this->createEDI();
         $this->removeQuarantine();
         return $this->orderid;  
      }
      
      
      
      private function registerUpload(){
         
         $folder = $this->data['folder'];
         $done = array();
         foreach ( $folder as $res ){
            if( !in_array( $res , $done ) ){
               $cdorder = new DBCdUploads();
               
               $cdorder->userid = $this->user->uid;
               $cdorder->email = $this->user->brukarnamn;
               $cdorder->location = $res;
               $cdorder->date = date( 'Y-m-d H:i:s');
               $cdorder->save();
            }
            $done[] = $res;
         }
         
      }
      
      
      private function OrderItems(){
         
         $orderdetails = $this->data;
         
         if( is_array( $orderdetails['article']['prints'] ) ){
            foreach ( $orderdetails['article']['prints'] as $key=>$prodno ){
               
              $this->productType = 'prints';
              
              if( $prodno == 'auto' ){
                 $prodnos = $this->SetupImagesAuto( $key );
                 
                 foreach( $prodnos as $prodno=>$qty ){
                     
                     $this->setupPrints( $key, $prodno );
                 }
                 
                 //util::Debug( $orderdetails['article']['prints']  );  
              }
              else if( is_array( $prodno ) ){
                  $articlenr = sprintf( '%04d' , $prodno['prodno'] );
                  
                  $unitprice = $prodno['unitprice'];
                  
                  $this->SetupImagesArray( $prodno );
                  $this->setupPrints( $key, $articlenr, $unitprice ); 
              }
              else{
                 $prodno = sprintf( '%04d' , $prodno );
                 $this->setupImages( $key, $prodno );
                 $this->setupPrints( $key, $prodno );  
              }
            }
            
            foreach ( $orderdetails['article']['productionmethod'] as $key=>$prodno ){
               $prodno = sprintf( '%04d' , $prodno );
               $this->addPrintAttribute( $prodno ); 
            }
            foreach ( $orderdetails['article']['papertype'] as $key=>$prodno ){
               $prodno = sprintf( '%04d' , $prodno );
               $this->addPrintAttribute( $prodno ); 
            }
         
         }
         
         if( is_array( $orderdetails['article']['gifts'] ) ){
         
            foreach ( $orderdetails['article']['gifts'] as $prodno=>$gift ){
               
              $this->productType = 'gifts';
              
              $this->SetupGifts( $prodno, $gift );
            }
         }
         
         
         /*set delivery and payment*/            
         $this->items['paymenttype']['title'] = 'Faktura';//payment always invoice
         $this->items['paymenttype']['refid'] = 1;//payment always invoice
         $this->items['paymenttype']['regionid'] = 1;//payment always invoice
         $this->items['paymenttype']['artnr'] = 393;//payment always invoice
         $this->items['paymenttype']['price'] = 29.00;//payment always invoice
         
         
         if( $orderdetails['delivery'] == 'local' ){
            $this->items['deliverytype']['refid'] = 484;
            $this->items['deliverytype']['price'] = 0;
            $this->items['deliverytype']['weight'] = $this->total;
            $this->items['deliverytype']['storeid'] = 'Japan Photo:19';           
         }
         
           
         
         else{
            if( $this->totalweight >= 1000 ){
               $this->items['deliverytype']['refid'] = 391;
               $this->items['deliverytype']['price'] = 105;
               $this->items['deliverytype']['weight'] = $this->total;
            }else{
               $this->items['deliverytype']['refid'] = 390;
               $this->items['deliverytype']['price'] = 49;
               $this->items['deliverytype']['weight'] = $this->total;
            }    
         }
            
      }
      
      
      
      private function setupPrints( $key, $prodno, $unitPrice = null ){
         
           $productoption = ProductOption::fromRefId( $prodno );
           $productType = $this->productType;
           
           $product = new Product( $productoption['productid']  );
           
            //Util::Debug("##########################################" . $unitPrice . "#########################################" );
           
           if( !$unitPrice ){
               $unitPrice = ProductOption::priceFromProdNo( $prodno, $this->items[$productType][$prodno]['quantity'] );
            }
           //Util::Debug("********************************************" . $unitPrice . "************************************************" );
           $totalProductPrice = ( $this->items[$productType][$prodno]['quantity'] * $unitPrice );
           
           //$this->setupImages( $productType, $key, $prodno );
           
           $this->items[$productType][$prodno]['prodno'] = $productoption->prodno;
           $this->items[$productType][$prodno]['optionid'] = $productoptionid;
           //$this->items[$productType][$prodno]['quantity'] = $totalQuantity;
           $this->items[$productType][$prodno]['unitprice'] = $unitPrice;
           $this->items[$productType][$prodno]['price'] = round( $totalProductPrice, 2 );
           $this->items[$productType][$prodno]['unitweight'] = round( $productoption->getUnitWeight(), 3 );
           $this->items[$productType][$prodno]['weight'] = round( ( $this->items[$productType][$productoption->prodno]['unitweight'] *  $this->items[$productType][$prodno]['quantity'] ), 3 );
           $this->items[$productType][$prodno]['refid'] = $productoption->refid;
           $this->items[$productType][$prodno]['currentproductoption'] = $productoption->asArray();
           $this->items[$productType][$prodno]['product'] = $product->asArray();
           $this->totalweight += $this->items[$productType][$prodno]['weight'];
           
     
      }
      
      
      private function setupGifts($prodno , $gift){
         
         foreach( $gift as $ret ){
         
            $quantity = $ret['quantity'];
            $templateref = $ret['referenceid'];
            $productoption = ProductOption::fromRefId( $prodno );
            $productType = $this->productType;
            $product = new Product( $productoption['productid']  );
            //Util::Debug("##########################################" . $ret['unitprice'] . "#########################################" );
            if( !$ret['unitprice'] ){
               $unitPrice = ProductOption::priceFromProdNo( $prodno, $quantity );
            }else{
               
               //Util::Debug("##########################################" . $ret['unitprice'] . "#########################################" );
               
               $unitPrice = $ret['unitprice'];
            }
            
            
            $totalProductPrice = ( $this->items[$productType][$prodno]['quantity'] * $unitPrice );
            
            $this->items[$productType][$prodno][$templateref]['prodno'] = $prodno;
            $this->items[$productType][$prodno][$templateref]['optionid'] = $productoption->id;
            $this->items[$productType][$prodno][$templateref]['referenceid'] = $templateref;
            $this->items[$productType][$prodno][$templateref]['quantity'] = $quantity;
            $this->items[$productType][$prodno][$templateref]['unitprice'] = $unitPrice;
            $this->items[$productType][$prodno][$templateref]['price'] = round( $totalProductPrice, 2 );
            $this->items[$productType][$prodno][$templateref]['unitweight'] = round( $productoption->getUnitWeight(), 3 );
            $this->items[$productType][$prodno][$templateref]['weight'] = round( ( $productoption->getUnitWeight() * $quantity ), 3 );
            $this->items[$productType][$prodno][$templateref]['refid'] = $productoption->refid;
            $this->items[$productType][$prodno][$templateref]['currentproductoption'] = $productoption->asArray();
            $this->items[$productType][$prodno][$templateref]['product'] = $product->asArray();
            $this->items[$productType][$prodno][$templateref]['image'] = '';
            $this->items[$productType][$prodno][$templateref]['malid'] = 0;
            $this->items[$productType][$prodno][$templateref]['pages'] = $ret['pages'];
            $this->items[$productType][$prodno][$templateref]['usertitle'] = $ret['text'];
         
         }
      }
      
      private function SetupImages( $key, $prodno ){
         
         $productdata = $this->data;
         $productType = $this->productType;
         $allowed_array = array( 'jpg', 'jpeg' );
            try{
               if( !$productdata['folder'][$key] ) die( "feil med bildefolder ");
               foreach ( glob( $productdata['folder'][$key] . '/*') as $imageres ){
                  
                  $pathinfo = pathinfo( $imageres );
                  
                  if( in_array( strtolower( $pathinfo['extension'] ),  $allowed_array ) ){
                     $this->items[$productType][$prodno]['images'][] = array( 
                                                                           'path'    => $imageres,
                                                                           'quantity' => $productdata['quantity'][$key],
                                                                           'fit-in'   => $productdata['fit-in'][$key]
                                                                       );
                                                                    
                     $this->items[$productType][$prodno]['quantity'] += $productdata['quantity'][$key];
                  }
               }
            }catch ( Exception  $e ){
               util::debug( $e->getmessage() );
               die();
            }
      }
      
      private function SetupImagesArray( $prodno ){
         
         $productdata = $this->data;
         $productType = $this->productType;
         $articlenr = sprintf( '%04d' , $prodno['prodno'] );
         
         if( $prodno['fitin']  == 1 ){
            $fitin = true;
         }else $fitin = false;
         
         try{ 
            $this->items[$productType][$articlenr]['images'][] = array( 
                                                                  'path'    => $prodno['file'],
                                                                  'quantity' => $prodno['quantity'],
                                                                  'fit-in'   => $fitin
                                                              );
                                                           
            $this->items[$productType][$articlenr]['quantity'] += $prodno['quantity'];
         }catch ( Exception  $e ){
               util::debug( $e->getmessage() );
               die();
            }
      }
      
      private function SetupImagesAuto( $key ){
         
         $productdata = $this->data;
         $productarray = array();  
         
         $productType = $this->productType;
         $allowed_array = array( 'jpg', 'jpeg' );
            try{
               if( !$productdata['folder'][$key] ) die( "feil med bildeforlder ");
               foreach ( glob( $productdata['folder'][$key] . '/*') as $imageres ){
                  
                  $pathinfo = pathinfo( $imageres );
                  
                  $imagesize = getimagesize( $imageres );
                  
                  $x = $imagesize[0];
                  $y = $imagesize[1];
                  
                  try{
                     if( max( $x, $y ) / min( $x, $y ) <= 1.4 ){
                        $prodno = '0419';
                     }else{
                        $prodno = '0001';
                     }
                  }catch (Exception  $e){
                     util::Debug( $e->getMessage() );
                  }
                  
                  if( in_array( strtolower( $pathinfo['extension'] ),  $allowed_array ) ){
                     $this->items[$productType][$prodno]['images'][] = array( 
                                                                           'path'    => $imageres,
                                                                           'quantity' => $productdata['quantity'][$key],
                                                                           'fit-in'   => $productdata['fit-in'][$key]                     
                                                                       );
                                                                       
                                                                    
                     $this->items[$productType][$prodno]['quantity'] += $productdata['quantity'][$key];
                     
                     $productarray[$prodno] = $productdata['quantity'][$key];
                  }
               }
            }catch ( Exception  $e ){
               util::debug( $e->getmessage() );
               die();
            }
            return $productarray;         
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
            
            //$this->recalculateTotals();
               
         }
         
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
       * Removes quarantine for all user's
       * quarantined images. Updates cache afterwards
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      private function removeQuarantine() {
         
         // Make sure this is a valid user
         $user = new User( Login::userid() );
         $albums = array();
         
         if( $user instanceof User && $user->isLoaded() ) {
         
            // Get all quarantined images
            $res = DB::query( "SELECT aid, bid FROM bildeinfo WHERE quarantined_at IS NOT NULL AND deleted_at IS NULL AND owner_uid=?", Login::userid() );
            
            // Remove quarantine for all images
            DB::query( "UPDATE bildeinfo SET quarantined_at = null WHERE deleted_at IS NULL AND owner_uid = ?", Login::userid() );
            
            if( $res->count() > 0 ) {

               while( list( $aid, $bid ) = $res->fetchRow() ) {
                  
                  // Remove image from cache
                  Model::deleteFromObjectCacheByClassAndId( 'image', $bid );
                  $albums[$aid] = 1;
                  
               }
               
               foreach( $albums as $albumid => $value ) {
                  
                  Model::deleteFromObjectCacheByClassAndId( 'album', $albumid );
                  
               }
               
            }
            
         }
         
      }
 
      
   /*   private function createEDI() {
         
         
        
	 $dinfo = $this->data;
         $dtype = $this->items['deliverytype'];
 
         $edi = new OrderEDI();
         $edi->setOrderId( $this->orderid );
         $edi->setDeliveryInfo( $dtype['refid'] );
         $edi->setWeight( $this->cartarray['totalweight'] );
         $edi->setUserId( $this->userid );
         
         //  Denne lagt inn for å få inn til til utestemme $edi->setPhonenr( $dinfo['phonenr'] );
         $edi->setPhoneNr( $dinfo['mobile_phone_number'] );
         $edi->setName( $dinfo['fullname'] );
         $edi->setAddress1( $dinfo['address'] );
         $edi->setZipCode( $dinfo['zipcode'] );
         $edi->setCity( $dinfo['city'] );
         $edi->save();
         
      }
      */
      
      private function mailConfirmation(){
         
         $textrow = array();
         $cartarray = $this->cartarray;
         
         $order = new Order();
         $orderrow = $order->getItems($this->orderid);
         $orderrow = array_reverse( $orderrow );
         
         foreach ($orderrow as $row){
            
            $textrow[] = sprintf("%-50s %8s %8s %8s\n", $row['title'], $row['unitprice'], $row['quantity'], $row['price']);
         }
         
         $textrow[] = sprintf("%-50s %8s %8s %8s\n", "Total", "", "",$cartarray['totalprice']) ."\n";
         
         $fields =  array(
            'orderrow'     => $orderrow ,
            'totalprice'   => $cartarray['totalprice'],
            'deliveryinfo' => $cartarray['deliveryinfo'],
            'comment'      => $this->comment,
            'textrow'     => $textrow,
         );
         
         #util::Debug($fields);
         #die();
         
         $subject = "Repix ordrebekreftelse, " . $this->orderid;

        	MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal' );
         
      }
      
       /**
       * Check for discounts, insert if exsists
       * 
       * @author Tor Inge Lovland <tor.inge@eurofoto.no>
       */
      private function insertDiscount(){
         
         $discount = $this->cartarray['discount'];
            
         if( count( $discount ) ){
            import( 'website.order.handlers.discount' );
            $handler = new OrderHandlerDiscount();
            $handler->process( $this, $type, $discount );
         }
         
      }
      
      
      
      /**
       * Get the currency code for this region
       *
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getCurrencyCode() {
         
         $regid = WebsiteHelper::region();
         $currencycode = "";
         
         if( !is_numeric( $regionid ) ) {
            $currencycode = 1;
         } else{
            
            $currencycode = DB::query( "
               SELECT 
                  export_valuta_code 
               FROM 
                  region 
               WHERE 
                  regionid = ?
            ", $regid )->fetchSingle();
            
            if( !is_numeric( $currencycode ) ) {
               $currencycode = 1;
            }
            
         }
         
         return $currencycode;
         
      }
      
      
      
      /**
       * Insert the main order into correct table
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function insertOrder() {
         
         
         if( $this->kampanje == 'Netlife' ){
            $kampanje_kode = 'Netlife';
         }
         
         
         
         else{
            
            $kampanje_kode = Dispatcher::getPortal();
         }
         
         
         
         $order = new DBUserOrders();
         $order->ordrenr         = $this->orderid;
         $order->uid             = $this->userid;
         $order->tidspunkt       = date( 'Y-m-d H:i:s' );
         $order->pris            = $this->totalprice();
         $order->comment         = $this->comment;
         $order->kampanje_kode   = $kampanje_kode;
         $order->customerlock    = date( 'Y-m-d H:i:s' );
         $order->exported        = date( 'Y-m-d' );
         $order->valutacode      = $this->getCurrencyCode();
         $order->source          = 'MANUAL';
         
         if( $this->hasaccessories ) {
            $order->medarbeidar = Settings::Get( 'coworker', 'accessories' );
         } else {
            $order->medarbeidar = Settings::Get( 'coworker', 'noaccessories' );
         }
         
         $order->save();
         
      }
      
      private function totalprice(){
         
         return DB::Query( "SELECT SUM( antall * pris ) FROM historie_ordrelinje WHERE ordrenr = ?" , $this->orderid )->fetchSingle();
      
      }
      
      
      /**
       * Insert the orders shipping cost as an order row
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function insertShippingCost() {
         
         $shippingrefid = Settings::Get( 'refid', 'shipping' );
         $deliverycost  = $this->items['deliverytype']['price'];
         $paymentcost   = $this->items['paymenttype']['price'];
          $paymentcost   = $this->items['paymenttype']['price'];
         $shippingcost  = number_format( ( $deliverycost + $paymentcost ), 2 );
         
         
         try{
         if($shippingcost > 0){
            $orderrow = new DBOrderRow();
            $orderrow->orderid      = $this->orderid;
            $orderrow->artnr        = $shippingrefid;
            $orderrow->quantity     = 1;
            $orderrow->price        = $shippingcost;
            $orderrow->text         = 'Porto og ekspedisjonskostnader';
            $orderrow->save();
         }
         }catch ( Exception $e){
            
            util::Debug( $e->getMessage()  );
            die();
            
         }
         
      }
            
      /**
       * Create a new order id for this order
       *
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function createNewOrderId() {
         
         return DB::query( "SELECT nextval('ordrenr');" )->fetchSingle();
         
      }
      
      
      /**
       * Get a new rubicon id used for Vismaid
       *
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function createNewRubiconId() {
         
         return DB::query( "SELECT nextval( 'rubid' )" )->fetchSingle();
         
      }
      
      
      /**
       * Insert contact and delivery information
       * of customer order.
       *
       * @param array $contactdata
       * @param array $deliverydata
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function insertContactAndDeliveryData() {
         
         
         $oc = new DBOrderCustomer();
         $oc->orderid = $this->orderid;
         $oc->userid = $this->userid;
         $oc->contactemail = $this->user->email;
         $oc->contactname = $this->user->fnavn . " " . $this->user->enavn;
         $oc->contactfirstaddress = $this->user->adresse1 ;
         $oc->contactzipcode = $this->user->postnr;
         $oc->contactcity = $this->user->stad;
         $oc->contactcountry = $this->getCountryId( $this->user->country );
         $oc->mphone = $this->user->telefon_mobil ;
         $oc->deliveryemail = $this->data['email'];
         $oc->deliveryname = $this->data['fullname'];
         $oc->deliveryfirstaddress = $this->data['address'];
         $oc->deliveryzipcode = $this->data['zipcode'];
         $oc->deliverycity = $this->data['city'];
        
   
          // Lagt til dessee linja for å få inn utestemme mobilnr på skjema
        //$oc->deliverycellnr = $this->data['mobile_phone_number'];
        // $oc->deliveryphonenr = $this->data['mobile_phone_number'];
        // $oc->phonenr = $this->data['mobile_phone_number'];
        
          $oc->deliveryphonenr = $this->data['mobile_phone_number'];
         $oc->contactcellnr = $this->data['mobile_phone_number'];
          $oc->phonenr = $this->data['mobile_phone_number'];
         $oc->deliverycountry = $this->getCountryId( $this->user->country  );
         $oc->save();
         //util::Debug( $oc );
         
         
      }
      
      
      
      
      
      /**
       * Get the corresponding country id from a country 2char ISO code
       *
       * @param string $countrycode
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function getCountryId( $countrycode = 'NO' ) {
         
         $countries = Settings::getSection( 'countries' );
         return $countries[$countrycode]['id'];
         
      }
      
      
      
      /**
       * Get the start lotnr for images
       *
       * @return integer
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function initLotNr() {
         
         $hbcount = DB::query( "SELECT count(*) FROM historie_bilde WHERE ordrenr = ?", $this->orderid )->fetchSingle();
         $hmcount = DB::query( "SELECT count(*) FROM historie_mal WHERE ordrenr = ?", $this->orderid )->fetchSingle();
         
         return $hbcount + $hmcount;
         
      }
      
      
      public function saveStoreDelivery() {
         
         try {
            
            $storeorder = new DBStoreOrder();
            if( $storeorder instanceof DBStoreOrder ) {
            
               $storeorder->orderid = $this->orderid;
               $storeorder->save();
               
            } else { 
               util::debug( "Error inserting storeorder" );
               die();
            }
            
         } catch ( Exception $e ) {
            util::debug( $e->getMessage() );
            die();
         }
         
      }
      
      
      /********  Getter functions **********/
      
      public function getOrderId() {
         
         return $this->orderid;
         
      }
      
      
      public function getCartArray() {
         
         return $this->cartarray;
         
      }
      
      
      public function getStartLotNr() {
         
         $lot = $this->startlotnr;
         $this->increaseStartLotNr();
         
         return $lot;
         
      }
      
     
      public function getHasAccessories() {
         
         return $this->hasaccessories;
         
      }
      
      
      public function getDate() {
         
         return $this->date;
         
      }
      
      
      /***** Setter functions ******/
      public function increaseStartLotNr() {
         
         $this->startlotnr++;
         
      }
      
      private function insertDeliveryOptions() {
         
         $deliveryoption = new DBOrderDelivery();
         $deliveryoption->orderid = $this->orderid;
         $deliveryoption->deliverymethod = $this->items['deliverytype']['refid'];
         $deliveryoption->paymentmethod = $this->items['paymenttype']['refid'];
         $deliveryoption->weight = $this->items['totalweight'];
         $deliveryoption->shopid = $this->getStoreShopId();
         
         $deliveryoption->save();
         
      }
      
      
      public function setStoreDelivery( $delivertostore = false ) {
         $this->storedelivery = $delivertostore;
      }
      
      public function getStoreShopId(){
         try{
            $storegroupdata = explode( ":", $this->items['deliverytype']['storeid']  );
            $storeid = end( $storegroupdata );
            $stores = Settings::Get( 'storedelivery', reset( $storegroupdata ) );
            $stores = $stores['stores'];
            
            if($stores[$storeid]){
               $selectedStore = $stores[$storeid];
               $this->comment = "Lokal ordre: blir henta! \n" . $this->comment;
               return  $selectedStore['shopId'];
            }
            else{
               return false;
            }
            
            
         }catch( Exception $e){
            return false;
         }   
      }
      
      
   }


?>
