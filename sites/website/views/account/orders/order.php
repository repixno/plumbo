<?php

   import( 'website.subscription' );
   import( 'website.album' );
   
   import( 'pages.protected' );
   
   class AccountOrdersOrder extends ProtectedPage implements IView {

      protected $template = 'account.orders.order';

      /**
       * Validator
       *
       * @return Array
       * 
       */
      
		public function Validate() {

         return array(
            'execute' => array(),
            'info' => array(
               'fields' => array(
                  VALIDATE_INTEGER
               )
            )
         );

      }
      
      /**
       * Execute
       *
       * adds orders to view
       * 
       */
      
      public function Execute( $orderid ) {
         
         // fetch order

		   $order = new Order( $orderid );

		   // Don't anyone see anyone else's orders.
		   
		   $ret = array();
		   
		   if ( $order->userid == Login::userid() ) {
		      
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

		   }
		   
		   $this->order = $ret;

		}
   }
?>