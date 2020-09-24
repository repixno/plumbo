<?php

   /**
    * 
    * Setup and execute user's order from cart
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    * @todo Need to ut8_encode template texts when fetching from db. That isnt done right now. OK
    * @todo Clarify if we only have support for one page products throught gift editor today OK

    * @todo check currencycode
    * 
    * @todo This class should really only handle db insert. All creation of production scripts should be handled by
    * production classes and cronjobs afterwards.
    * 
    * @todo Add shipping cost to total cost before inserting order into db OK
    * 
    */

   import( 'website.cart' );
   import( 'website.credit' );
   import( 'website.order' );
   import( 'mail.send');
   import( 'website.order.edi' );
   
   model( 'order.customer' );
   model( 'order.row' );
   model( 'order.delivery' );
   model( 'user.orders' );
   model( 'site.storeorder' );
   
   
   config( 'website.countries' );
   

   class UserOrder extends Cart {
      
      private $comment = null;
      private $orderid = null;
      private $user = null;
      private $cartarray = array();
      private $startlotnr = 0;
      private $hasaccessories = false;
      private $date = null;
      private $storedelivery = false;
      
      
      /**
       * Execute the order.
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function executeOrder( $orderid = null ) {
      
         if( Login::isLoggedIn() ){
            $this->user = new User( Login::userid() );
            $rubid = (int) $this->user->rubid;
         }else{
            //mail( 'tor.inge@eurofoto.no', 'DBUser Bug', "Trying to confirm order without being logged in\n" . "orderid:" . $this->orderid  . "\n" .print_r( debug_backtrace(), true ));
            throw new SecurityException( 'User not logged in!' );
         }

         // No previous rubicon id
         // Create one
         if( $rubid < 1 ) {
            $rubid = $this->createNewRubiconId();
            $this->user->rubid = $rubid;
            $this->user->save();
         }
         
         
         // Create a new orderid for this order
         $this->userid     = Login::userid();
         $this->orderid    = isset( $orderid ) ? $orderid : $this->createNewOrderId();
         UserSessionArray::addItem( 'controlorderid', $this->orderid );
         $this->startlotnr = $this->initLotNr();
         $this->cartarray  = $this->asArray();
         $this->date       = date( 'Y-m-d' );
         
         // Insert customer contact and delivery info
         $this->insertContactAndDeliveryData();
          
            
         $items = array();
         $collection = array();
         $collectionarray = $this->cartarray['items'];
         
         import( 'website.order.handlers.print' );
                  
         foreach( $collectionarray as $type => $items ) {

            foreach( $items as $prodno => $item ) {

               // For regular products
               switch( $type ) {

                  case 'prints':
                  case 'enlargements':
                     
                     // imported above
                     // import( 'website.order.handlers.print' );
                     
                     // process the orderline
                     $handler = new OrderHandlerPrints();
                     $handler->process( $this, $type, $item );
                     break;
                     
                  case 'gifts':  // Items gone throught the gift editor
                  
                     import( 'website.order.handlers.gift' );
                     
                     // Can have multiple templates under the same product
                     if( count( $item ) > 0 ) {
                        foreach( $item as $gift ) {
                           $handler = new OrderHandlerGifts();
                           $handler->process( $this, $type, $gift );
                        }
                     }
                     break;
                  
                  case 'textgift':  // Items gone throught the gift editor
                  case 'module':
                  case 'digital':
                  
                     import( 'website.order.handlers.textgift' );
                     
                     // Can have multiple templates under the same product
                     if( count( $item ) > 0 ) {
                        foreach( $item as $gift ) {
                           $handler = new OrderHandlerTextGift();
                           $handler->process( $this, $type, $gift );
                        }
                     }
                     break;
                     
                  case 'ukeplan': 
                  
                  import( 'website.order.handlers.ukeplan' );
                  
                     
                  // Can have multiple templates under the same product
                  if( count( $item ) > 0 ) {
                     foreach( $item as $ukeplan ) {
                        $handler = new OrderHandlerUkeplan();
                        $handler->process( $this, $type, $ukeplan );
                     }
                  }
                  break;
                  
                  case 'merkelapp': 
                  
                  import( 'website.order.handlers.merkelapp' );
                     
                  // Can have multiple templates under the same product
                  if( count( $item ) > 0 ) {
                     foreach( $item as $merkelapp ) {
                        $handler = new OrderHandlerMerkelapp();
                        $handler->process( $this, $type, $merkelapp );
                     }
                  }
                  break;
               
                  case 'stempel': 
                  
                  import( 'website.order.handlers.stempel' );
                     
                  // Can have multiple templates under the same product
                  if( count( $item ) > 0 ) {
                     foreach( $item as $merkelapp ) {
                        $handler = new OrderHandlerStempel();
                        $handler->process( $this, $type, $merkelapp );
                     }
                  }
                  break;
               
                  case 'smilesontiles':
                     import( 'website.order.handlers.smilesontiles' );
                     
                     if( count( $item ) > 0 ) {
                        foreach( $item as $smilesontiles ) {
                           $handler = new OrderHandlerSmilesontiles();
                           $handler->process( $this, $type, $smilesontiles );
                        }
                     }
                  break;
                       
                  case 'mediaclip': // Items gone throught mediaclip editor
                     
                     import( 'website.order.handlers.mediaclip' );
                  
                     try{
                        if( count( $item ) > 0 ) {
                           foreach( $item as $mediaclip ) {
                              $handler = new OrderHandlerMediaclip();
                              $handler->process( $this, $type, $mediaclip );
                           }
                        }
                     }catch ( Exception $e ){
                        util::Debug( $e );
                        die();
                     }
                     break;
                     
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
                     $handler = new OrderHandlerPrints();
                     $handler->process( $this, $type, $items );
                     break;
                  case 'correctionmethod':
                     $handler = new OrderHandlerPrints();
                     $handler->process( $this, $type, $items );
                     break;
                  case 'paperquality':
                     $handler = new OrderHandlerPrints();
                     $handler->process( $this, $type, $items );
                     break;
                  default:
                     break;
               
            }
            
         }
         
         #$this->insertCredits();
         $this->insertDiscount();
         $this->insertShippingCost();
         $this->mailConfirmation();
         $this->insertOrder();
         $this->insertDeliveryOptions( $this->orderid, $this->cartarray );
         $this->createEDI();
         //$this->removeQuarantine();
         if( $this->getStoreDelivery() ) {
            $this->saveStoreDelivery();
         }
         //update Krid
         if( empty( $this->user->krid ) ){
            try{
               $krid = $this->phoneLookup();
               $this->user->krid = $krid;
               $this->user->save();
            }catch( Exception $e ){
               //mail( 'tor.inge@eurofoto.no', "krid bugs", "user id: " .  $this->userid );
            }
         }
         return $this->orderid;  
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
      
      
      private function createEDI() {
         
         $dinfo = $this->getDeliveryInfo();
         $dtype = $this->getDeliveryType();
         
         $edi = new OrderEDI();
         $edi->setOrderId( $this->orderid );
         $edi->setDeliveryInfo( $dtype['refid'] );
         $edi->setWeight( $this->cartarray['totalweight'] );
         $edi->setUserId( Login::userid() );
         $edi->setName( $dinfo['name'] );
         $edi->setAddress1( $dinfo['address'] );
          $edi->setAddress2( $dinfo['address2'] );
         $edi->setZipCode( $dinfo['zipcode'] );
         $edi->setCity( $dinfo['city'] );
         $edi->setPhoneNr( $dinfo['mphone'] );
         
         if( !empty( $this->user->contactemail  ) ){
             $edi->setEmail( $this->user->contactemail );
         }else{
             $edi->setEmail( $this->user->email );
         }
         
         $edi->save();
         
      }
      
      
      private function mailConfirmation(){
         
         $textrow = array();
         $imagecodes = array();
         $cartarray = $this->cartarray;
         
         $order = new Order();
         $orderrow = $order->getItems($this->orderid);
         $orderrow = array_reverse( $orderrow );
         
         
         foreach ($orderrow as $row){
            
            $textrow[] = sprintf("%-50s %8s %8s %8s\n", $row['title'], $row['unitprice'], $row['quantity'], $row['price']);
            
            if( $row['artikkelnr'] == 7291 ){
               $attributes = unserialize( $row['attributes'] );

               $imagecodes[]  = $attributes["code"];
            }
         }
         
         $textrow[] = sprintf("%-50s %8s %8s %8s\n", "Total", "", "",$cartarray['totalprice']) ."\n";
         
         $fields =  array(
            'orderrow'     => $orderrow ,
            'totalprice'   => $cartarray['totalprice'],
            'deliveryinfo' => $cartarray['deliveryinfo'],
            'comment'      => $this->comment,
            'textrow'      => $textrow,
            'orderrow'     => $orderrow,
            'orderid'      =>  $this->orderid,
            'imagecodes'    => $imagecodes
         );
         
         $subject = "Repix ordrebekreftelse, " . $this->orderid;
         if( Dispatcher::getPortal() == 'TK-001' || Dispatcher::getPortal() == 'FK-001' ){
            MailSend::Simple( $this->user->contactemail, $subject, 'order.confirm', $fields, 'phptal' );
         }
         else if( $this->user->kode == 'ST-001'){
            $subject = "Stabburet Leverpostei";
            MailSend::Simple(  $this->user->email, $subject, 'order.stabburet', $fields, 'phptal', '"Dinmerkelapp" <post@dinmerkelapp.no>' );
         }
      
         else if( Dispatcher::getPortal() == 'DM-001' ){
            $subject = "Dinmerkelapp.no Ordrebekreftelse";
            MailSend::Simple(  $this->user->email, $subject, 'order.merkelapp', $fields, 'phptal', '"Dinmerkelapp" <post@dinmerkelapp.no>' );
         }
         
            else if( Dispatcher::getPortal() == 'DM-002' ){
            $subject = "Dinmerkelapp.no Ordrebekreftelse";
            MailSend::Simple(  $this->user->email, $subject, 'order.gratismerkelapp', $fields, 'phptal', '"Dinmerkelapp" <post@dinmerkelapp.no>' );
         }
         
         else if( Dispatcher::getPortal() == 'SM-001' ){
            $subject = "Smiles on Tiles ordrebekreftelse, " . $this->orderid;
            MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', 'Smiles on Tiles <post@smilesontiles.no>' );
         }
         else if( Dispatcher::getCustomAttr('portalname') == 'Sparelappen' ){
            $subject = "Sparelappen ordrebekreftelse, " . $this->orderid;
            MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', 'Sparelappen <post@sparfoto.no>' );
         }
         else if( Dispatcher::getPortal() == 'SK-001' ){
            $subject = "Sparfoto ordrebekreftelse, " . $this->orderid;
            MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', 'post@sparfoto.no' );
         }
         else if( Dispatcher::getPortal() == 'UP-DK' ){
           $subject = "Ugeplan.dk ordrebekræftelse, " . $this->orderid;
           MailSend::Simple( "kontakt@ugeplan.dk", "Kundeordre " . $subject, 'order.confirm', $fields, 'phptal', '"Ugeplan" <kontakt@ugeplan.dk>' );
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Ugeplan" <kontakt@ugeplan.dk>' );
         }
         else if( Dispatcher::getPortal() == 'FC-001' &&  Dispatcher::getLoginGroup() == 'FC-LYSHOL'){
           $subject = "Fotograf P.P. Lyshol ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Fotograf P.P. Lyshol" <post@eurofoto.no>' );
           MailSend::Simple( "fotolyshol@xi.no", $subject, 'order.confirm', $fields, 'phptal', '"Fotograf P.P. Lyshol" <post@eurofoto.no>' );
         }
         else if( Dispatcher::getPortal() == 'FC-001' &&  Dispatcher::getLoginGroup() == 'FC-STATHELLE'){
           $subject = "Stathelle Foto ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Stathelle Foto" <post@eurofoto.no>' );
           MailSend::Simple( "post@stathellefoto.no", $subject, 'order.confirm', $fields, 'phptal', '"Stathelle Foto" <post@eurofoto.no>' );
         }
         else if( Dispatcher::getPortal() == 'RF-001' || Dispatcher::getPortal() == 'RF-002' ){
           $subject = "Reed Foto ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Reed Foto AS" <post@reedfoto.no>' );
         }
         else if( Dispatcher::getPortal() == 'UP-001' ){
           $subject = "Ukeplan ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Ukeplan.no" <post@ukeplan.no>' );
         }
         else if( Dispatcher::getPortal() == 'FOTONO' ){
           $subject = "Foto.no ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Foto.no" <bilder@foto.no>' );
         }
         else if( Dispatcher::getPortal() == 'FOTOPIX' ){
           $subject = "Fotopix.no ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Fotopix.no" <post@repix.no>' );
         }
         
         else if( Dispatcher::getPortal() == 'SL-001' ){
           $subject = "Seniorlappen ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Seniorlappen.no" <post@seniorlappen.no>' );
         }
         
         else if( Dispatcher::getPortal() == 'FE-001' ){
           $subject = "Felix ketchup orderbekräftelse";
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Japan Photo" <bildshop@japanphoto.se>' );
         }
         else if( Dispatcher::getPortal() == 'STU-SV' ){
           $subject = "CEWE Japan Photo orderbekräftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Japan Photo" <bildshop@japanphoto.se>' );
         }
         
         else if( Dispatcher::getPortal() == 'SKA-001' ){
           $subject = "Takk for din bestilling / Thank you for the order. Skanska sticker, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.skanska', $fields, 'phptal', '"Repix" <post@repix.no>' );
         }
         
         else if( Dispatcher::getPortal() == 'UL-001' ){
           $subject = "Utestemme Merkelapp ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.utestemme', $fields, 'phptal', '"Repix" <kundeservice@repix.no>' );
         }
         
         
         else if( Dispatcher::getPortal() == 'RP-001' ){
           $subject = "Repix ordrebekreftelse, " . $this->orderid;
           MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal', '"Repix" <post@repix.no>' );
         }
         
         
         
         
         
         
         
        else{ 
            MailSend::Simple( $this->user->email, $subject, 'order.confirm', $fields, 'phptal' );
         }
         
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
      
      private function creditcardTransactionOK() {
         return true;
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
         
         $order = new DBUserOrders();
         $order->ordrenr         = $this->orderid;
         $order->uid             = Login::userid();
         $order->tidspunkt       = date( 'Y-m-d H:i:s' );
         $order->pris            = $this->cartarray['totalprice'];
         $order->comment         = $this->comment;
         $order->kampanje_kode   = Dispatcher::getPortal();
         $order->customerlock    = date( 'Y-m-d H:i:s' );
         $order->exported        = date( 'Y-m-d' );
         $order->valutacode      = $this->getCurrencyCode();
         $order->source          = Dispatcher::getOs() . '-' . Dispatcher::getSiteAttr( 'template' );
         $order->klarnaid        = $this->cartarray['klarnaid'];
         $order->trackingcode    = $this->cartarray['trackingcode'] ? $this->cartarray['trackingcode'] : '';
         
         if( $this->hasaccessories ) {
            $order->medarbeidar = Settings::Get( 'coworker', 'accessories' );
         } else {
            $order->medarbeidar = Settings::Get( 'coworker', 'noaccessories' );
         }
         
         $order->save();
         
      }
      
      
      
      /**
       * Insert the item as an order row
       *
       * @param string $type
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       
      private function insertOrderRow( $type, $item ) {
         
         $orderrow = new DBOrderRow();
         $orderrow->orderid      = $this->orderid;
         $orderrow->artnr        = $item['refid'];
         $orderrow->quantity     = $item['quantity'];
         $orderrow->price        = $item['unitprice'];
         $orderrow->text         = $item['product']['title'];
         if( $type == 'gifts' )     $orderrow->templateid   = $item['referenceid'];
         if( $type == 'mediaclip' ) $orderrow->productid    = $item['referenceid'];
         $orderrow->save();
         
      }
      */
      
      
      /**
       * Insert the orders shipping cost as an order row
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function insertShippingCost() {
         
         $shippingrefid = Settings::Get( 'refid', 'shipping' );
         $deliverycost  = $this->cartarray['deliverytype']['price'];
         $paymentcost   = $this->cartarray['paymenttype']['price'];
         $shippingcost  = number_format( ( $deliverycost + $paymentcost ), 2 );
         
         if($shippingcost > 0){
            $orderrow = new DBOrderRow();
            $orderrow->orderid      = $this->orderid;
            $orderrow->artnr        = $shippingrefid;
            $orderrow->quantity     = 1;
            $orderrow->price        = $shippingcost;
            $orderrow->text         = __( 'shipping and handling' );
            $orderrow->save();
         }
         
      }
      
      
      
      /**
       * Is order with creditcard
       *
       * @return boolean
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function creditcard() {
         
         $paymenttype = $this->getPaymentType();
         $creditcardrefid = Settings::Get( 'refid', 'creditcard' );

         // If user choose to pay with creditcard
         if( $paymenttype['artnr'] == $creditcardrefid ) {

            return true;

         }
         
         return false;

      }
      
      
      
      /**
       * Set the user's comment of the order
       *
       * @param string $comment
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function setComment( $comment ) {
         
         $this->comment = $comment;
         
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
         
         $contactdata = $this->getContactInfo();
         $deliverydata = $this->getDeliveryInfo();
         
         if( Dispatcher::getPortal() == 'FK-001' ){
        	   $contactemail = $this->user->contactemail;
         }
         else{ 
        	   $contactemail = $this->user->email;
         }
         
         $oc = new DBOrderCustomer();
         $oc->orderid = $this->orderid;
         $oc->userid = $this->userid;
         $oc->contactemail = $contactemail;
         $oc->contactname = $contactdata['name'];
         $oc->contactfirstaddress = $contactdata['address'];
           $oc->contactsecondaddress = $contactdata['address2'];
         $oc->contactzipcode = $contactdata['zipcode'];
         $oc->contactcity = $contactdata['city'];
         $oc->contactperson = $contactdata['contactperson'];
         $oc->contactcountry = $this->getCountryId( $contactdata['country'] );
         $oc->mphone = $contactdata['mphone'];
         $oc->deliveryemail = $contactemail;
         $oc->deliveryname = $deliverydata['name'];
         $oc->deliveryfirstaddress = $deliverydata['address'];
          $oc->deliverysecondaddress = $deliverydata['address2'];
         $oc->deliveryzipcode = $deliverydata['zipcode'];
         $oc->deliverycity = $deliverydata['city'];
         $oc->deliverycountry = $this->getCountryId( $deliverydata['country'] );
         $oc->save();
         
      }
      
      
      
      /**
       * Get the order contact info
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getContactInfo() {
         
         return $this->cartarray['contactinfo'];
         
      }
      
      
      
      /**
       * Get the order delivery info
       *
       * @return array
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function getDeliveryInfo() {
         
         return $this->cartarray['deliveryinfo'];
         
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
            
               $storeorder->orderid = $this->getOrderId();
               $storeorder->save();
               
            } else { 
               // perhaps send error message 
            }
            
         } catch ( Exception $e ) {
            // Perhaps send error message?
         }
         
      }
      
      
      /********  Getter functions **********/
      
      public function getOrderId() {
         
         return $this->orderid;
         
      }
      
      
      public function getComment() {
         
         return $this->comment;
         
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
      
      public function getStoreDelivery() {
         return $this->storedelivery;
      }
      
      
      /***** Setter functions ******/
      
      
      public function increaseStartLotNr() {
         
         $this->startlotnr++;
         
      }
      
      private function insertDeliveryOptions( $orderid, $cartarray ) {
         
         $deliveryoption = new DBOrderDelivery();
         $deliveryoption->orderid = $orderid;
         $deliveryoption->deliverymethod = $cartarray['deliverytype']['refid'];
         $deliveryoption->paymentmethod = $cartarray['paymenttype']['refid'];
         $deliveryoption->weight = $cartarray['totalweight'];
         $deliveryoption->shopid = $this->getStoreShopId();
         $deliveryoption->save();
         
      }
      
      
      public function setStoreDelivery( $delivertostore = false ) {
         $this->storedelivery = $delivertostore;
      }
      
      public function getStoreShopId(){
         try{
            $store = $this->asArray();
            $storegroupdata = explode( ":", $store['storeid'] );
            $storeid = end( $storegroupdata );
            $stores = Settings::Get( 'storedelivery', reset( $storegroupdata ) );
            $stores = $stores['stores'];
            if($stores[$storeid]){
               $selectedStore = $stores[$storeid];
               return  $selectedStore['shopId'];
            }
            else{
               return false;
            }
            
            
         }catch( Exception $e){
            return false;
         }   
      }
      
      public function phoneLookup(){
         
      	return 0;
      	
      	try {
	      	
	         $contactdata = $this->getContactInfo();
	         $phonenumber = $contactdata['mphone'];
	         $ch = curl_init();
	         curl_setopt($ch, CURLOPT_URL, "https://secure.bringcrm.no/api/address/v1/Lookup/Telephone/" . $phonenumber );
	         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); //timeout in seconds 
				curl_setopt($ch, CURLOPT_TIMEOUT,		  1); //timeout in seconds
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json' , 'Authorization:BB9B38C5D8359AAEE040007F01000D02' ) );
	         $content = json_decode(  curl_exec($ch) ) ;
	         
	         return  $content[0]->Krid;
	         
      	} catch( Exception $e ) {
      		
      		return 0;
      		
      	}
         
      }
      
      
   }


?>
