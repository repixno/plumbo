<?php


   import( 'pages.json' );
   import( 'website.order' );
   
   class APIOrderstatus extends JSONPage implements NoAuthRequired,IView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  'orderid' => VALIDATE_INTEGER,
               ),
               'post' => array(
                  'orderid' => VALIDATE_INTEGER,
               )
            ),
         );
         
      }

      
      public function Execute( $orderid = null) {

         $order = Order::fromOrderNo($orderid);
         
         $this->result = true;
         $this->message = $order->status;
         return true;
            
      }
      
   }

?>