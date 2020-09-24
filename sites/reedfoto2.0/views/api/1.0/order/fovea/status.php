<?php

   /**
    * 
    * Display order status to forvea
    * @author Tor Inge Lovland <tor.inge@eurofoto.no>
    * 
    */

   import( 'pages.json' );
   
   class APIOrderFovea extends JSONPage implements NoAuthRequired,IView {
      
      //private $secureid = '6d84b982490ccb2551ee76d6762757f451949b542c50e0';
      private $secureid = 'hW5jk1WnO4L3l07nhH0Nghf7bvB0NAlNnEoNNXshyRU8BZ1YSnjn6dPn0jswIhk';
      
      /**
       * Receive orders from the fovea
       * 
       * @api-name order.fovea
       * @api-auth not required
       * @api-post-required securecode Secure code for auth
       * @api-post-required orderid Order id from Fovea
       * @api-result result Status string
       * @api-result message String Describes the result of the operation in US English

      */       
      
      public function Execute() {
         
         $securecode = isset( $_POST['securecode'] ) ? $_POST['securecode'] : null;
         $orderid = isset( $_POST['orderid'] ) ? $_POST['orderid'] : '';
         
         $this->result = false;
         $ipadress =  $_SERVER['REMOTE_ADDR'];
         
         if( $securecode == $this->secureid ){
            
            if( !empty( $orderid) ){
                try{
                  
                  $orderinfo = DB::query( "SELECT * FROM production_fovea WHERE foveaid = ?", $orderid )->fetchAll( DB::FETCH_ASSOC );
                    
                  if( !empty( $orderinfo[0]['sent'] ) ){
                     $status = "sent";
                  }else if( !empty( $orderinfo[0]['toproduction'] ) ){
                     $status = "produced";
                  }else if( !empty( $orderinfo[0]['downloaded'] ) ){
                     $status = "received";
                  }else{
                     $status = "no_order";
                  }
                
                }
                catch( Exception $e ){
                    $this->message = $e->getMessage();
                    return false;
                }
            }else{
                $this->message = "Missing orderid";
                return false;
            }
   
            $this->result = true;
            $this->message = "OK";
            $this->status = $status;
            return true;
         }else{
            $this->message = "Not authorized";
            return false;
         }
         
      }
      
   }

   


?>