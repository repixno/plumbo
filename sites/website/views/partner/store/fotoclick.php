<?php

   import( 'pages.protected' );
   import( 'mail.send' );
   import( 'sms.send' );
   import( 'validate.phone.cell' );
   
   model( 'user.orders' );
   model( 'site.storeorder' );

   class fotoClick extends ProtectedPage implements IView {
      
      protected $template = 'partner.store.fotoclick';
      
      public function Execute() {

      }
      
      
      /**
       * List the order data 
       * from given orderid
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function order() {
        
         
         $orderid = isset( $_POST['orderid'] ) ? (int) trim( $_POST['orderid'] ) : null;
         if( isset( $orderid ) && $orderid > 0 ) {
            
            try {
               $order = DBStoreOrder::fromOrderId( $orderid );
               
               if( $order instanceof DBStoreOrder && $order->isLoaded() ) {
                  
                  $res = DB::query( "
                     SELECT 
                        uid,
                        dnavn,
                        depost,
                        dmphone,
                        dtelefon
                     FROM
                        historie_kunde
                     WHERE
                        ordrenr = ?
                     ", $orderid 
                  );
                  
                  
                  if( $res->count() > 0 ) {
                     list( $customerid, $name, $email, $mobilenr, $phonenr ) = $res->fetchRow();
                  }
                  
                  $res2 = DB::query( "
                     SELECT 
                        telefon_mobil
                     FROM
                        kunde
                     WHERE
                        uid = ?
                  ", $customerid );
                  
                  if( $res2->count() > 0 ) {
                     
                     list( $cmobilenr ) = $res2->fetchRow();
                     
                  }
                  
                  $this->storeuserid = $order->storeuserid;
                  $this->orderid = $orderid;
                  $this->received = $order->received;
                  $this->smssent = $order->smssent;
                  $this->emailsent = $order->emailsent;
                  $this->receivername = $name;
                  $this->receiveremail = $email;
                  
                  // Setup mobilenr to send SMS to
                  if( strlen( $mobilenr ) > 0 && ValidateCellPhone::validate( $mobilenr ) ) {
                     $this->receivermobile = $mobilenr;
                  } else if( strlen( $cmobilenr ) > 0 && ValidateCellPhone::validate( $cmobilenr ) ) {
                     $this->receivermobile = $cmobilenr;
                  }
                  
                  $this->searchresult = true;
                  
               }
               
            } catch( Exception $e ) {}
         
         } 
         
      }
      
      
      /**
       * Do the actual sending of SMS
       * or email to user
       *
       * @param string $type
       * @return boolean
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function send( $type, $fields ) {
         
         // Get order data connected to this orderid
         $orderdata              = $this->orderData( $fields['orderid'] );
         $storedata              = $this->storeData();
         
         // Add to replacement fields
         $fields['name']         = $orderdata['name'];
         $fields['storename']    = $storedata['storename'];
         $fields['storeaddress'] = $storedata['storeaddress'];
         
         if( count( $orderdata ) > 0 ) {
            
            switch( $type ) {
               
               case 'SMS':
                  // Create the message to be sent
                  $message = $this->createSMSMessage( $fields );
                  
                  // Check valid nr
                  if( strlen( $orderdata['mobilenr'] ) > 0 && ValidateCellPhone::validate( $orderdata['mobilenr'] ) ) {
                     $sms = new SMS();
                     if( $sms->send( $orderdata['mobilenr'], $message, 0 ) ) {
                        return true;
                     }
                     
                  } else if( strlen( $orderdata['cmobilenr'] ) > 0 && ValidateCellPhone::validate( $orderdata['cmobilenr'] ) ) {
                     $sms = new SMS();
                     if( $sms->send( $orderdata['cmobilenr'], $message, 0 ) ) {
                        return true;
                     }
                  }
                     
                  echo json_encode( array( 'result' => false, 'message' => 'Cell nr did not validate' ) );
                  die();
                  break;
                  
               case 'EMAIL':
                  if( isset( $orderdata['email'] ) ) {
                     $template = 'partner.store.orderreceived';
                     $from = 'post@eurofoto.no';
                     $subject = __( 'We have your order ' ).$fields['orderid'];
                     MailSend::Simple( $orderdata['email'], $subject, $template, $fields, 'phptal', $from );
                     return true;
                  }
                  break;
                  
               default:
                  return false;
                  break;
               
            }
         
         }
         
         return false;
         
      }
      
      
      /**
       * Update correct dbtables and
       * try to send a SMS to user
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function sendSMS() {
         
         $this->setTemplate( '' );
         $orderid = isset( $_POST['orderid'] ) ? (int) $_POST['orderid'] : null;
         $receiveddate = null;
         $smsdate = null;
         
         if( isset( $orderid ) && $orderid > 0 ) {
            
            $order = DBStoreOrder::fromOrderId( $orderid );
            if( $order instanceof DBStoreOrder && $order->isLoaded() ) {
               
               $date = date( 'Y-m-d H:i:s' );
               
               if( is_null( $order->received ) ) {
                  $order->storeuserid = Login::userId();
                  $order->received = $date;
                  $receiveddate = $date;
                  $order->save();
               } else {
                  $receiveddate = $order->received;
               }
               
               $fields = array(
                  'orderid' => $orderid,
                  'date' => $receiveddate,
               );
               
               if( $this->send( 'SMS', $fields ) ) {
                  
                  $order->smssent = $date;
                  $order->save();
                  $smsdate = $date;
                  
               } else {
                  echo json_encode( array( 'result' => false, 'message' => 'Could not send SMS' ) );
                  die();
               }
               
            }
            
         }
         
         // All well
         echo json_encode( array( 'result' => true, 'message' => 'OK', 'received' => $receiveddate, 'sms' => $smsdate ) );
         die();
         
      }
      
      
      /**
       * Update correct dbtables and
       * try to send an email to user
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function sendEmail() {
         
         $this->setTemplate( '' );
         $orderid = isset( $_POST['orderid'] ) ? (int) $_POST['orderid'] : null;
         $receiveddate = null;
         $emaildate = null;
         
         if( isset( $orderid ) && $orderid > 0 ) {
            
            $order = DBStoreOrder::fromOrderId( $orderid );
            if( $order instanceof DBStoreOrder && $order->isLoaded() ) {
               
               $date = date( 'Y-m-d H:i:s' );
               
               if( is_null( $order->received ) ) {
                  $order->storeuserid = Login::userId();
                  $order->received = $date;
                  $receiveddate = $date;
                  $order->save();
               } else {
                  $receiveddate = $order->received;
               }
               
               $fields = array(
                  'orderid' => $orderid,
                  'date' => $receiveddate,
               );
               
               if( $this->send( 'EMAIL', $fields ) ) {
                  
                  $order->emailsent = $date;
                  $order->save();
                  $emaildate = $date;
                  
               } else {
                  echo json_encode( array( 'result' => false, 'message' => 'Failed to send email' ) );
                  die();
               }
               
            }
            
         }
         
         // All well
         echo json_encode( array( 'result' => true, 'message' => 'OK', 'received' => $receiveddate, 'email' => $emaildate ) );
         die();
         
      }
      
      
      /**
       * Fetch yhe data about the order and user
       *
       * @param integer $orderid
       * @return array
       */
      private function orderData( $orderid ) {
         
         $res = DB::query( "
            SELECT 
               hk.dnavn,
               hk.depost,
               hk.dmphone,
               hk.dtelefon,
               k.telefon_mobil
            FROM
               kunde AS k
            LEFT JOIN
               historie_kunde AS hk ON hk.uid = k.uid
            WHERE
               hk.ordrenr = ?
            ", $orderid 
         );


         if( $res->count() > 0 ) {
            list( $name, $email, $mobilenr, $phonenr, $cmobilenr ) = $res->fetchRow();
            return array( 'name' => trim($name), 'email' => trim($email), 'mobilenr' => trim($mobilenr), 'phonenr' => trim($phonenr), 'cmobilenr' => $cmobilenr );
         }
         
         return array();
         
      }
      
      
      /**
       * Fetch the data about the store
       *
       * @return array
       */
      private function storeData() {
         
         $res = DB::query( "
            SELECT 
               fnavn,
               enavn,
               adresse1,
               postnr,
               stad
            FROM
               kunde
            WHERE
               uid = ?
         ", Login::userId() );
         
         if( $res->count() > 0 ) {
            
            list( $storename, $storelastname, $storeaddress, $storezipcode, $storecity ) = $res->fetchRow();
            $storeaddress = $storeaddress."\n".$storezipcode." ".$storecity;
            $storefullname = $storename . " " . $storelastname . ",";
            return array( 'storename' => $storefullname, 'storeaddress' => $storeaddress );
            
         }
         
         return array();
         
      }
      
      
      /**
       * Compile the whole SMS message to be sent
       *
       * @param array $fields
       * @return string
       */
      private function createSMSMessage( $fields ) {
         
         $msg = __( 'Your order has now arrived at' ).' '.$fields['storename']."\n";
         $msg .= $fields['storeaddress']."\n";
         $msg .= __( "Eurofoto customercenter" );
         
         return $msg;
         
      }
      
      
   }


?>