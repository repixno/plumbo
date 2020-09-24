<?php

   /**
    *
    */

   import( 'website.cart' );
   import( 'finance.vipps.vipps' );
   import( 'website.order.default' );
   import( 'session.usersessionarray' );
   
   class VippsFallbackPage extends WebPage implements IView {
      
      protected $template = false;
        
      public function Execute( $orderid = 0 ) {
         
        $vipps = new Vipps($orderid);
        $vippsdetails = $vipps->getdetails( $orderid );
        
        if( $vippsdetails->transactionLogHistory[0]->operation == "CANCEL" ){
         relocate( '/cart' );
         exit;
        }
        $email = $vippsdetails->userDetails->email;
        $fullname = $vippsdetails->userDetails->firstName . " " . $vippsdetails->userDetails->lastName;
        $phone =  $vippsdetails->userDetails->mobileNumber;
        
        $address = $vippsdetails->shippingDetails->address;
        $user = User::fromUsernameAndPortal($email, 'DM-001');
        
         if( $user ){
            $user = new User( $user->uid );
            $user->setFullname( $fullname  );
            $user->setCellPhone( $phone );
            $user->streetaddress = $address->addressLine1;
            $user->zipcode = $address->postCode;
            $user->city = $address->city;
            $user->save();
            
            if( !Login::isLoggedIn() ){
                if( !Login::byUserObject( $user ) ) {
                    throw new CriticalException( 'Unable to login' );
                }
            }
             
         }else{
             
             try {
                  // Fine - passed all checks
                  // Let's create a new user
                 
                  $user = new User();
                  $user->username = $email;
                  $user->password = 'nopass';
                  $user->portal = Dispatcher::getPortal();
                 
                  $user->setFullname( $fullname  );
                  $user->setCellPhone( $phone );
                  $user->streetaddress = $address->addressLine1;
                  $user->zipcode = $address->postCode;
                  $user->city = $address->city;
                 
                  $user->country = 203;
                  $user->created = date( 'Y-m-d H:i:s' );
                  $user->save();
                  // New user is created and saved
                  // Continue and list delivery methods
                  if( !Login::byUserObject( $user ) ) {
                     throw new CriticalException( 'Unable to login' );
                  }
                 
             } catch( Exception $e ) {
               
                  Util::Debug($e->getMessage());
                  
                  mail( 'tor.inge@reedfoto.no' , "Vippps Order Failed! New user 71: ", $e->getMessage()  );
                  exit;  
             }
             
         }
        
         try {
   
            $userorder = new UserOrder();
            $portal = Dispatcher::getPortal();
            
            $cart = new Cart();
            $cart->setDeliveryType( 1, 0 );
            //endra fra 78 til 737 for å få endra tekst på ordreskjema til betalingsmåte VIPPS
            $cart->setPaymentType( 737 );
            
            $contact["name"] = $vippsdetails->userDetails->firstName . " " . $vippsdetails->userDetails->lastName;
            $contact["zipcode"] = $address->postCode;
            $contact["city"] = $address->city;
            $contact["address"] = $address->addressLine1;
            $contact["country"] = 160;
            $contact["mphone"] = $phone;
            
            $cart->setContactInfo( $contact );
            $cart->setDeliveryInfo( $contact );
            
            // la inn vipps som kommentar
             $cart->setComment('OBS OBS VIPPS ORDRE!!');
            $cart->save();
            
            relocate( '/checkout/execute' );
            
            /*if( $orderid = $userorder->executeOrder() ) {
               UserSessionArray::clearItems( 'purchased_cart' );
               UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
               $cartarray = $cart->asArray();
               $klarnarray = unserialize($cartarray['klarnaid']);
               $vipps->orderid = $orderid;
               $vipps->status = "paid";
               $vipps->save();
               relocate( '/checkout/finished/'.$orderid );
   
            } else {
               relocate( '/checkout/error' );
            }*/
            
         } catch( Exception $e ) {

            //util::debug( "User order failed" );
            util::debug( 'Vippps Order Failed! Userorder fallback-order-result 124: '. $e->getMessage() );
            
            $message = "Vippps Order Failed! Userorder fallback-order-result 124:\n";
            $message .=  $e->getMessage() . "\n" . $this->debugging( $cart );
            $message .= $this->debugging( $e );
            
            mail( 'til@gloppen.net' , "Vippps Order Failed! Userorder r fallback-order-result 136: ", $message  );
            
            file_put_contents( '/home/toringe/debug/' . date("Y-m-d_H:i:s") . ".txt", $message  );
            //$userorder->deleteOrder();
            die();
   
         }
         
      }
      
      private function debugging(){
         
         
         $args = func_get_args();
         
         foreach( $args as $argument ) {
            $ret .= "<pre>".print_r( $argument, true )."</pre>\n";
         }
      
         return $ret;
         
      }
      
   }


?>