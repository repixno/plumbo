<?php

   import( 'finance.klarna.klarna' );
   import( 'website.order' );
   
   class ExecuteKlarna extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute() {
        
        @$checkoutId = $_GET['klarna_order'];
       //$ksarray = explode( '/', $checkoutId );
        
        $klarna = new KlarnaEF();
        $klarna->fetch($checkoutId);
        
        //Util::debug( $klarna->order  );
        
        //mail('tor.inge@reedfoto.no', "klarna" , $klarna->order['status'] . " ".  $checkoutId . "\n orderid: ". serialize( $klarna->order )  );
        
         if ( $klarna->order['status'] == "checkout_complete" ) {
          
             //https://checkout.testdrive.klarna.com/checkout/orders/14C2C86920BEACF707508290000
             // At this point make sure the order is created in your system and send a
             // confirmation email to the customer
             
             $orderid1 = (int)$klarna->order['merchant_reference']['orderid1'];
             //mail('tor.inge@eurofoto.no', "Klarna orderid" , $orderid1  );
             
             if( $orderid1 > 0 ){
               $orderid = DB::query( "SELECT id FROM historie_ordre WHERE ordrenr = ?",  $orderid1 )->fetchSingle();
             }
             
             
             
             $complatedate = date( "Y-m-d H:i:s", strtotime( (string)$klarna->order['completed_at'] ) );
             
             
             /*$order = new Order($orderid);
             
             $klarnainfo = array(
                                 'reference' => $klarna->order['reference'],
                                 'checkoutid' => $checkoutId,
                                 'reservation' => $klarna->order['reservation'],
                                 'eid' => $klarna->order['merchant']['id']
                                 );
             
             mail('tor.inge@eurofoto.no', "klarnaid" ,  serialize( $klarnainfo ) );
             
             $order->klarnaid = serialize( $klarnainfo );
             $order->save();*/
             
            if( $orderid ){
               //mail('tor.inge@eurofoto.no', "klarna OK" , $klarna->order['status'] . " ".  $checkoutId . "\n orderid: " . $orderid  );
               $update = array();
               $update['status'] = 'created';
               $klarna->order->update($update);
            }
            else{
               try{
                  if( date( strtotime( $complatedate  . " +4 hours" ) < date('Y-m-d H:i:s') )){
                     mail('tor.inge@reedfoto.no', "Klarna not OK" , $complatedate . " ------------\n " .   serialize( $klarna->order) );
                  }
               }Catch(Exception $e ){
                   mail('tor.inge@reedfoto.no', "Klarna error" , $e->getMessage() );
                
               }
            } 
         }
                         
         
      }

      
   }


?>
