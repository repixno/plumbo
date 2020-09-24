<?php

    /**
    * Delivery for ef3
    * 
    * @author Tor Inge
    *
    */
   
    model( 'site.delivery' );
   
    class Delivery extends DBDelivery {
        
        public function fromSiteidAndId($siteid, $textentityid){
            
            $deliveryoptions = DB::query( "SELECT * FROM site_delivery WHERE siteid = ? AND deliverytype = ?", $siteid, $textentityid )->fetchAll( DB::FETCH_ASSOC );
            
            //$paymentoptions = $this->paymentOptions();
            
        
            return $deliveryoptions;
            
        }
      
        public function asArray() {
         
         
        }
        
        
        public function paymentOptions(){
            
            import( 'website.paymenttype' );
            
            $siteid = Session::get( 'adminsiteid', 1 ); 
            $type = 'paymenttype';
            $delivery = DB::query( "SELECT * FROM site_textentity WHERE siteid = ? AND type=?" , $siteid, $type )->fetchAll( DB::FETCH_ASSOC  );
            
            
            $collection = new PaymentType();
            if( count( $collection ) ) foreach( $collection->collection( array( 'id' ), array( 'deleted' => null, 'siteid' => $siteid ) )->fetchAllAs( PaymentType ) as $textentity ) {
               
               $entities[] = array(
                  'id'        => $textentity->id,
                  'title'     => $textentity->title,
                  'refid'  => $textentity->refid,
               );
                
            }
            
            
            return $entities;
        }
        
      
    }
   
?>