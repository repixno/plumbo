<?php

	import( 'core.util' );
	model( 'user.orders' );
	import( 'website.order' );
	
	class MyAccountOrders extends UserPage implements IView {
		
		protected $template = 'myaccount.orders';
		
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
		
		public function Execute( ) {
			
		   $this->setTemplate( 'myaccount.orders.index' );
		   $orders = new Order();
		   // For some reason we only want orders with order number higher than this.
		   $newordercut = 389910;
		   
			$list = array();
			foreach ( $orders->collection( array( 'id' ), array( 'uid' => Login::userid(), 'ordrenr' => array( '>', $newordercut ) ), 'tidspunkt DESC' )->fetchAllAs( 'Order' ) as $order ) {
				$list[] = array(
					'id' => $order->orderid,
					'orderid' => $order->ordernum,
					'date' => strftime( '%e. %B %Y', strtotime( $order->date ) ),
					'timestamp' => strtotime( $order->date ),
					'status' => $order->status
		        );
			}
			$this->orders = $list;
		}
		
		public function info( $orderid ) {
			
			$this->setTemplate( 'myaccount.orders.showorder' );
			$order = new Order( $orderid );
			// Don't anyone see anyone else's orders.
			if ( $order->userid != Login::userid() ) {
				$this->order = array();
				$this->message = 'You do not have permission to see this order.';
				return false;
			}
			
			// Build order array.
			$ret = array(
				'id' => $order->orderid,
				'orderid' => $order->ordernum,
				'date' => strftime( '%e. %B %Y', strtotime( $order->date ) ),
				'timestamp' => strtotime( $order->date ),
				'comment' => $order->comment,
				'status' => $order->status,
				'items' => $order->items,
				'totalprice' => $order->price,
			);
		   $this->order = $ret;
		}
		
		public function utestemme() {
			$this->template = false;
			
			if( $_GET['utorderid'] ){
				$utorderid = $_GET['utorderid'];
				$ordrenr = DB::query( "SELECT eforderid FROM production_utestemme WHERE utestemmeid = ?", $utorderid )->fetchSingle();
				$orderid = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ? " , $ordrenr )->fetchSingle();
				relocate( "/myaccount/orders/info/" . $orderid );
				
			}else{
				relocate( "/myaccount/orders/" );
			}
			
			
			
			
			
			
			
			
		}

	}

?>