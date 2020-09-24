<?php

   /**
    * Order is finished. Display order id 
    * and other necessary information to customer.
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.cart' );
   
   class CheckoutFinished extends WebPage implements IView {
      
      public function Execute( $orderid = 0 ) {
         
         $cart = new Cart();
         $cart->clear();
         
         $this->setTemplate( 'checkout.complete' );
         
         if( isset( $orderid ) ) {
            UserSessionArray::clearItems( 'controlorderid' );
            
            $this->orderid = $orderid;
            $purchasedCart = UserSessionArray::getItem( 'purchased_cart', 0 );
            $this->purchasedcart = $purchasedCart;
            
            if( isset( $purchasedCart['giftcard'] ) && count( $purchasedCart['giftcard'] ) ) {
               $giftcardid = $purchasedCart['giftcard']['giftcardid'];
               if( isset( $giftcardid ) && $giftcardid > 0 ) {
                  
                  try {
                     
                     $giftcard = new Giftcard( $giftcardid );
                     $newvalue = $giftcard->value - $purchasedCart['giftcard']['usedvalue'];
                     $giftcard->value = $newvalue;
                     $giftcard->changed = date( 'Y-m-d H:i:s' );
                     
                     $orderids = $giftcard->usedorderid;
                     if( isset( $orderids ) ) {
                        $orderids = unserialize( $orderids );
                     }
                     
                     if( is_array( $orderids ) ) {
                        $orderids []= $orderid;
                     } else {
                        $orderids = array();
                        $orderids []= $orderid;
                     }
                     
                     $orderids = serialize( $orderids );
                     $giftcard->usedorderid = $orderids;
                     
                     $giftcard->save();
                     
                  } catch( Exception $e ) {
                     
                  }
                  
               }
               
            }
            

            // Is this a giftcard purchase?
            if( DB::query( "SELECT count(*) FROM giftcard WHERE orderid = ? AND buyerid = ?", $orderid, Login::userid() )->fetchSingle() > 0 ) {
               $this->giftcard = true;
            }
            
            if( Session::get('utm_source') == "kelkoono" ){
               //Change to information provided by TradeDoubler/Kelkoo
               $this->kelkoo = array("org_id" => 1542373,
                                     "orderValue" => $purchasedCart['totalprice'],
                                     "orderNumber" => $orderid,
                                     "event" => 213844,
                                     "currency" => "NOK"
                                     );
            }
            
            if( Login::isLoggedIn() && date( 'Y-m-d' ) < '2010-10-01' && $orderid > 0 && is_array( $purchasedCart ) ) {
               
               if( !DB::query( "
                  SELECT 
                     COUNT(*)
                  FROM 
                     freepics_given
                  WHERE 
                     uid = ? AND
                     campaign = ?
               ", Login::userid(), '100 gratisbilder, September-tilbud 2010' )->fetchSingle() ) {
               
               
                  $qty = (int) DB::query( "SELECT antall FROM tilgode WHERE artikkelnr = ? AND uid = ?", 419, Login::userid() )->fetchSingle();
                  if( $qty > 0 ) {
                  
                     $qty += 100;
                     
                     DB::query( "
                        UPDATE 
                           tilgode 
                        SET 
                           antall = ?,
                           tekst = ?
                        WHERE
                           uid = ? AND 
                           artikkelnr = ?
                     ", $qty, '100 gratisbilder, September-tilbud 2010', Login::userid(), 419 );
                      
                     
                  } else {
                   
                     DB::query( 'INSERT INTO tilgode (uid, artikkelnr, antall, tekst, tidspunkt ) VALUES (?, ?, ?, ?, NOW())', Login::userid(), 419, 100, '100 gratisbilder, September-tilbud 2010' );                     
                       
                  }
                  
                  DB::query( 'INSERT INTO freepics_given (uid, given, campaign) VALUES (?, NOW(), ?)', Login::userid(), '100 gratisbilder, September-tilbud 2010' );
                  
               }
                  
            }
            
            
            $today = date( 'Y-m-d' );
            if( Login::isLoggedIn() && $today >= '2011-10-01' && $today < '2011-10-16' && $orderid > 0 && is_array( $purchasedCart ) ) {
               
               // Number of valid calendars
               $numvalidprojects = 0;
               // These are the calendars that must be purchased to give free cards
               $calendarrefids = array( 908, 909, 910, 911, 912, 913 );
               
               $purchasedmp = $purchasedCart['items']['mediaclip'];
               if( count( $purchasedmp ) > 0 ) {
                  
                  foreach( $purchasedmp as $prodno => $proddata ) {
                     
                     if( count( $proddata ) > 0 ) {
                        
                        foreach( $proddata as $projectid => $projectdata ) {
                           
                           // If this is a valid calendar
                           if( in_array( $projectdata['refid'], $calendarrefids  ) ) {
                              
                              $numvalidprojects += $projectdata['quantity'];
                              
                           }
                           
                        }
                        
                     }
                     
                  }
                  
               }
               
               
               // Check if there are enough calendars purchased
               // If so, give free cards
               if( $numvalidprojects >= 5 ) {
                  
                  $qty = (int) DB::query( "SELECT antall FROM tilgode WHERE artikkelnr = ? AND uid = ?", 939, Login::userid() )->fetchSingle();
                  if( $qty > 0 ) {
                  
                     $qty += 10;
                     
                     DB::query( "
                        UPDATE 
                           tilgode 
                        SET 
                           antall = ?,
                           tekst = ?
                        WHERE
                           uid = ? AND 
                           artikkelnr = ?
                     ", $qty, 'Gratis julekort 2010', Login::userid(), 939 );
                      
                     
                  } else {
                   
                     DB::query( 'INSERT INTO tilgode (uid, artikkelnr, antall, tekst, tidspunkt ) VALUES (?, ?, ?, ?, NOW())', Login::userid(), 939, 10, 'Gratis julekort 2010' );
                     $qty = 10;
                  }
                  $this->freechristmascard = $qty;
                  UserSessionArray::removeItem( 'purchased_cart', 0 );
                  
               }
               
            }
            
         }
         
      }
      
   }


?>