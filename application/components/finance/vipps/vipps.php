<?php

   model( 'finance.vipps' );
   import( 'finance.vipps.vippsCurl' );

   class Vipps extends DBVippsTransaction {
    
    
    
        public function register($cart){
            $vipps = new VippsCurl();        
            $relocateurl = $vipps->payments( $cart );
            return $relocateurl;
            
        }
        
        public function getdetails( $orderid ){
            
            $vipps = new VippsCurl();        
            $details = $vipps->details( $orderid );
            
            return $details;
            
        }
    
    
    
   }