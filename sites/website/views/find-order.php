<?php


   import( 'website.order' );

   class OrdersFindOrder extends UserPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'orderid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
               )
            
            )
         );
         
      }
      
      
      public function Execute( $orderid = null ) {
         
         $this->setTemplate( 'dialogs.find-order' );
         
         if( !isset( $orderid ) ) {
            $orderid = isset( $_POST['orderid'] ) ? $_POST['orderid'] : null;
         }
         
         if( isset( $orderid ) && $orderid > 0 ) {
            
            $order = Order::fromOrderNo( $orderid );
            if( $order instanceof Order && $order->isLoaded() ) {
               
               if( $order->userid == Login::userid() ) {
                  
                  // Build order array.
         		   $ret = array(
         		      'id'         => $order->orderid,
         	         'orderid'    => $order->ordernum,
         	         'date'       => strftime( '%e. %B %Y', strtotime( $order->date ) ),
         	         'timestamp'  => strtotime( $order->date ),
         	         'comment'    => $order->comment,
         	         'status'     => $order->status,
         	         'items'      => $order->items,
         		      'totalprice' => $order->price,
         	      );
         	      
         	      $this->order = $ret;
         	      
               }
               
            }
            
         }
         
      }
      
   }


?>