<?php

   /**
    * Get order details.
    *
    * @author Svein Arild Bergset <sab@interweb.no> / Tor inge
    *
    */

   import( 'pages.json' );
   import( 'website.order' );

   class APIOrderDelivery extends JSONPage implements NoAuthRequired, IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER
                  ),
                'get' => array(
                  "orderid" => VALIDATE_INTEGER
                  ),
               )
            );

      }

      /**
       * Get order details.
       * 
       * @api-name order.delivery
       * @api-auth required
       * @api-param orderid Integer ID of the order to fetch
       * @api-result order Array Order array ( items, totalprice, delivery )
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */   
      public function Execute( $orderid = null ) {
         
         header('Access-Control-Allow-Origin: *');
         
         if( $_GET['orderid'] ){
            $orderid = $_GET['orderid'];
         }
         else if( $_POST['orderid'] ){
            $orderid = $_POST['orderid'];
         }
         
         $id = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?", $orderid )->fetchSingle();
         
         $order = new Order( $id );
         
         $delivery = $order->delivery;
         
         $customer = DB::query( "SELECT fnavn, mnavn, enavn FROM kunde WHERE uid = ?", $delivery[0]['uid'])->fetchAll( DB::FETCH_ASSOC );

		   $ret = array(
		      'items' => $order->items,
		      'totalprice' => $order->price,
		      'delivery' => $order->delivery,
		      'customer' => $customer
		      );

         // Return values.
         $this->result = true;
         $this->order = $ret;
         $this->message = "OK";
         return true;

      }


   }


?>