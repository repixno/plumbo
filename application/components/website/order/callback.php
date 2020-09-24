<?php

   model( 'order.callback' );
   
   import( 'website.order.callbackurl' );
   
   library( 'pear.http.request2' );
   
   class OrderCallback extends DBOrderCallback {
      
      public function send() {

         $cart = unserialize( $this->cart );
         
         $callbackurl = new OrderCallbackUrl( $this->ordercallbackid );
         
         if( $callbackurl instanceof OrderCallbackUrl && $callbackurl->isLoaded() ) {
            
            if( strlen( $callbackurl->url ) > 0 ) {
               
               $request = new HTTP_Request2( $callbackurl->url, HTTP_Request2::METHOD_POST );
               
               try {

                  // add the post data
                  $request->addPostParameter( array(
                     'order' => json_encode( unserialize( $this->cart ) ),
                  ) );

                  // send the request
                  $response = $request->send();
                  $callbackstatus = $response->getStatus();
                  $this->attempts++;

                  // check the response code
                  if( $callbackstatus == 200 ) {

                     $this->httpresponse = $callbackstatus;
                     $this->confirmed = date( 'Y-m-d H:i:s' );
                     
                  } else if( $callbackstatus > 0 ) {

                     if( $callbackstatus > 0 && $cart['info']['orderno'] > 0 ) {
                        $this->httpresponse = $callbackstatus;
                     }

                  }
                  
                  $this->save();

               } catch (HTTP_Request2_Exception $e) {

                  util::debug( 'Error: ' . $e->getMessage() );
                  
               }
               
            }
            
         }
         
      }
      
   }


?>