<?
   /**
    * Add discounts to order.
    * @author Tor Inge Lovland <tor.inge@eurofoto.no>
    * 
    */
   import( 'website.order.handlers.base' );
   import( 'website.discounthistory' );
   
   
   class OrderHandlerDiscount extends OrderHandlerBase {
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::process( $order, $type, $item );
         
         $this->parseItem( $item );
         $this->finalizeDiscount( $item );
         
      }
      
      
      private function parseItem( $item ){
         
         $id = $item['info']['id'];
         $userid = Login::userid();
         if( is_array( $item['final'] ) ){
            foreach ($item['final'] as $final){
               $newdiscount = DiscountHistory::fromIdAndUser( $id, $userid );
               
               if( !$newdiscount ){
                  $newdiscount = new DiscountHistory();
                  $newdiscount->discount_campaign_id = (int)$id;
                  $newdiscount->code = $item['info']['code'];
                  $newdiscount->user_id = (int)$userid;
                  $newdiscount->ordernr = (int)$this->orderid;
                  $newdiscount->amount = $final['totaldiscount'];
                  $newdiscount->activated = date( 'Y-m-d H:i:s' );
                  $newdiscount->used = date( 'Y-m-d H:i:s' );
                  $newdiscount->type = 2;
                  $newdiscount->save();
               }else{
                  $newdiscount->orderid = $this->orderid;
                  $newdiscount->amount = $final['totaldiscount'];
                  $newdiscount->used = date( 'Y-m-d H:i:s' );
                  $newdiscount->save();
               }
               
            }
         }
      }
      
   }
   
   
?>