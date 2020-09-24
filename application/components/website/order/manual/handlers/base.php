<?PHP
   
   /**
    * 
    * Base handler class for products
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * @author Øyvind Selbek      <oyvind.selbek@interweb.no>
    * 
    */
   model( 'order.row' );
   config( 'website.order' );
   config( 'website.cart' );
   
   class ManualOrderHandlerBase extends DBOrderRow {
      
      private $type;
      private $item;
      private $today;
      private $downloadpath;
      private $imagepath;
      private $order;
      private $cartarray;
      
      
      /**
       * Setup the order
       *
       * @param UserOrder $order
       * @param string $type
       * @param array $item
       */
      public function Process( ManualOrder $order, $type, $item ) {
         
         $this->orderid       = $order->getOrderId();
         $this->type          = $type;
         $this->item          = $item;
         $this->today         = date( 'Y-m-d' );
         $this->order         = $order;
         $this->cartarray     = $order->getCartArray();
         $this->downloadpath  = "/home/produksjon/webspool";
         $this->imagepath     = Settings::Get( 'paths', 'images' );
         
      }
      
      
      
      /**
       * Finalize the order row and insert into db
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function Finalize() {
         
         $this->insertOrderRow( $this->type, $this->item );
         
      }
      
      
      
      
      /**
       * Insert the item as an order row
       *
       * @param string $type
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function insertOrderRow( $type, $item ) {
         
         if( $item['usertitle'] ){
            $titletext = $item['usertitle'];
         }
         else{
            $titletext = $item['product']['title'];
         }
         
         if( $item['currentproductoption']['title'] ){
            $titletext .= " " . $item['currentproductoption']['title'] ;
         }
         if( $item['quantity'] > 0){
            $this->artnr        = $item['refid'];
            $this->quantity     = $item['quantity'];
            $this->price        = $item['unitprice'];
            $this->text         = $titletext;
            if( $type == 'gifts' )     $this->templateid   = $item['referenceid'];
            if( $type == 'mediaclip' ) $this->productid    = $item['referenceid'];
            $this->save();
         }
         
      }
      
      
      
      /**
       * Check and update user's credit items
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function checkCredit( $item ) {
         
         $credits = array();
         $credits = $this->cartarray['credits'];
         $validcredits = array();
         
         $refid = $item['refid'];
         if( is_array( $credits ) ){
            foreach( $credits as $credit ) {
               
               // Is there a credit for this product?
               if( $credit['refid'] == $refid ) {
                  
                  // Load the credit object and subtract the used quantity
                  if( isset( $credit['usedquantity'] ) && $credit['usedquantity'] > 0 ) {
                     
                     //$creditobject = Credit::getCreditByUserIdAndRefId( Login::userid(), $refid );
                     $creditobject = new Credit( $credit['id'] );
                  
                     if( $creditobject instanceof Credit && $creditobject->isLoaded() ) {
                        
                        $validcredits []= $credit;
                        
                     }
                     
                  }
                  
                  
               }
               
            }
         }
         
         if( count( $validcredits ) > 0 ) {
            return $validcredits;
         }
         
         return null;
         
      }
      
            /**
       * Insert order row for credit
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      protected function finalizeCredits( $credits = array(), $item ) {
         
         $freeitemrefid = Settings::Get( 'cart', 'freeitemrefid' ); 
         
         if( $credits > 0 ) {
            
            foreach( $credits as $credit ) {
            
               $orderrow               = new DBOrderRow();
               $orderrow->orderid      = $this->orderid;
               $orderrow->artnr        = $freeitemrefid['refid'];
               $orderrow->quantity     = ( $credit['usedquantity'] * -1 );
               $orderrow->price        = $item['unitprice'];
               $orderrow->text         = $credit['usertitle'];
               $orderrow->save();
               
               //$creditobject = Credit::getCreditByUserIdAndRefId( Login::userid(), $item['refid'] );
               $creditobject = new Credit( $credit['id'] );
               $creditobject->quantity -= $credit['usedquantity'];
               
               if($creditobject->quantity == 0){
                  $creditobject->delete();
               }
               else{
                  $creditobject->save();
               }
            
            }
         
         }
         
         
      }
      
    /**
    * Insert order row for Redeyeremoval
    *
    * @param array $item
    * 
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */
      protected function finalizeRedeyeremoval( $item ) {
         
         $orderrow               = new DBOrderRow();
         $orderrow->orderid      = $this->orderid;
         $orderrow->artnr        = $item['refid'];
         $orderrow->quantity     = $item['quantity'];
         $orderrow->price        = $item['price'];
         $orderrow->text         = $item['product']['title'];
         $orderrow->save();
         
         
      }
      
            
    /**
    * Insert order row for Discount
    *
    * @param array $item
    * 
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */
      public function finalizeDiscount( $item ) {
      
         $freeitemrefid = Settings::Get( 'cart', 'discountrefid' );
         
         $orderrow               = new DBOrderRow();
         $orderrow->orderid      = $this->orderid;
         $orderrow->artnr        = $freeitemrefid['refid'];
         $orderrow->quantity     = -1;
         $orderrow->price        = $item['final'][0]['totaldiscount'];
         $orderrow->text         = $item['info']['name'];
         $orderrow->save();
      
      }
      
      
      /****** Getter functions *******/
      
      
      public function getToday() {
         
         return $this->today;
         
      }
      
      
      public function getStartLotNr() {
         
         return $this->startlotnr;
         
      }
      
      
      public function getImagePath() {
         
         return $this->imagepath;
         
      }
      
      
      public function getDownloadPath() {
         
         return $this->downloadpath;
         
      }
      
      
      public function getOrder() {
         
         return $this->order;
         
      }
      
      
   }
   
?>