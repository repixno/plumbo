<?php

   import( 'website.subscription' );
   import( 'website.album' );
   import ('website.order' );
   
   import( 'pages.protected' );
   
   class AccountOrders extends ProtectedPage implements IView {

      protected $template = 'account.orders.index';

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

		public function Execute( ) {

		   $orders = new Order();
		   
		   // For some reason we only want orders with order number higher than this.
		   $newordercut = 389910;

         $list = array();
		   foreach ( $orders->collection( array( 'id' ), array( 'uid' => Login::userid(), 'ordrenr' => array( '>', $newordercut ) ), 'tidspunkt DESC' )->fetchAllAs( 'Order' ) as $order ) {

		      $list[] = array(
		         'id'         => $order->orderid,
		         'orderid'    => $order->ordernum,
		         'date'       => strftime( '%e. %B %Y', strtotime( $order->date ) ),
		         'timestamp'  => strtotime( $order->date ),
		         'status'     => $order->status
		         );

         }

         $this->orders = $list;

		}
      
   }
      
?>