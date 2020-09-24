<?php

   import( 'website.order.default' );   
   import( 'session.usersessionarray' );
   config( 'website.stores' );
   
   import( 'finance.klarna.klarna' );
   
   
   class Execute extends UserPage implements IView {
      
      protected $template = null;
      
      public function Execute(){
         $cart = new Cart();
         // Check if cart is empty
         if( $cart->getTotalItems() == 0 ) {
            relocate( '/cart' );   
         }
         $klarna = new KlarnaEF();
         $klarna->fetch();
         //$this->snippet = $klarna->snippet;
         
         //$klarnaid = $klarna->order['reference'];
         
         if ($klarna->order['status'] == 'checkout_incomplete') {
            relocate( '/checkout/error' );
         }
         else{
            
            try {
               
               $contact["name"] = $klarna->order['billing_address']['given_name'] . " " .  $klarna->order['billing_address']['family_name'];
               $contact["firstname"] = $klarna->order['billing_address']['given_name'];
               $contact["lastname"] = $klarna->order['billing_address']['family_name'];
               $contact["zipcode"] = $klarna->order['billing_address']['postal_code'];
               $contact["city"] = $klarna->order['billing_address']['city'];
               $contact["address"] = $klarna->order['billing_address']['street_address'];
               $contact["country"] =$klarna->order['billing_address']['country'];
               $contact["mphone"] = $klarna->order['billing_address']['phone'];
               
               // Setup delivery info
               $delivery["name"] = $klarna->order['shipping_address']['given_name'] . " " .  $klarna->order['shipping_address']['family_name'];
               $delivery["firstname"] = $klarna->order['shipping_address']['given_name'];
               $delivery["lastname"] = $klarna->order['shipping_address']['family_name'];
               $delivery["zipcode"] =  $klarna->order['shipping_address']['postal_code'];
               $delivery["city"] =  $klarna->order['shipping_address']['city'];
               $delivery["address"] =  $klarna->order['shipping_address']['street_address'];
               $delivery["country"] =  $klarna->order['shipping_address']['country'];
               
               
               try{
                  $user = new User( Login::userid() );
                  $gender = ( $klarna->order['customer']['gender'] == 'male'  ) ? 'M' :  'K';
                  $user->fnavn = $klarna->order['billing_address']['given_name'];
                  $user->enavn =  $klarna->order['billing_address']['family_name'];
                  $user->postnr = $klarna->order['billing_address']['postal_code'];
                  $user->stad = $klarna->order['billing_address']['city'];
                  $user->born = date( 'Y-d-m', strtotime($klarna->order['customer']['date_of_birth']) );
                  $user->kjonn = $gender;
                  $user->adresse1 = $klarna->order['billing_address']['street_address'];
                  $user->mphone = $klarna->order['billing_address']['phone'];
                  $user->save();
               }catch( Exception $e ){
                  
                  mail('tor.inge@eurofoto.no', 'feil med oppdatering av kunde' , $e->getMessage() );
                  
               }
               
               $userorder = new UserOrder();
               
               $klarnainfo = array(
                                'paymentmethod' => 'KLARNA',
                                'reference' => $klarna->order['reference'],
                                'reservation' => $klarna->order['reservation'],
                                'eid' => $klarna->order['merchant']['id']
                                );
               
               $userorder->setKlarnaid( serialize( $klarnainfo ) );
               
               $userorder->setContactInfo( $contact );
               $userorder->setDeliveryInfo( $delivery );
               
               if( $orderidresult = $userorder->executeOrder() ){
                  
                  $update = array();
                  $update['merchant_reference']['orderid1'] = (string)$orderidresult;
                  
                  $klarna->order->update($update);
                  
                  UserSessionArray::clearItems( 'purchased_cart' );
                  UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
                  
                  relocate( '/kasse/finished/' .  $orderidresult );
                  die();
                  
               } else {
                  
                  //Util::Debug("ka farskebn");
                  mail( 'tor.inge@eurofoto.no' , "Klarna order failed ", $message  );
                  relocate( '/checkout/error' );
                  
               }
               
            } catch( Exception $e ) {
               
               util::debug( $e );
               util::debug( 'User Order Failed3! Reason: '.$e->getMessage() );
               $message =  $e->getMessage() . "\n" . $this->debugging( $cart );
               $message .= $this->debugging( $e );
               
               mail( 'tor.inge@eurofoto.no' , "User Order Failed! Creditcard Reason3: ", $message  );
               //$userorder->deleteOrder();
               die();
               
            }
         }
      }
      
   }


?>