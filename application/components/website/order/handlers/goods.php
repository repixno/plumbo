<?php

   /**
    * 
    * Handle products tagged as goods.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'website.order.handlers.base' );
   model( 'order.archive');
   
   
   class OrderHandlerGoods extends OrderHandlerBase {
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::process( $order, $type, $item );
         $this->finalize();
         
         $archiveproduct = Settings::Get('cart' , 'archive' );
         if( in_array( $item['refid'] , $archiveproduct['optionidarray'] ) ){  
            $this->archive( $item, $archiveproduct );
         }

         $credit = $this->checkCredit( $item );
         
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
         
      }
      
      
      private function archive( $item , $archiveproduct ){
         
         $archive = new DBArchive();
         
         $archive->orderid    = $this->orderid;
         $archive->tidspunkt  = date( 'Y-m-d H:i:s' );
         
         if( $item['refid'] == $archiveproduct['options']['DVD'] ){
            $archive->dvd        = $item['quantity'];
         }
         if( $item['refid'] == $archiveproduct['options']['CD'] ){
            $archive->cd        = $item['quantity'];
         }
         if( $item['refid'] == $archiveproduct['options']['MINNEPENN'] ){
            $archive->minnepenn        = $item['quantity'];
         }
         $archive->userid     = Login::userid();
         
         $archive->save();
         
         
      }
      
   }



?>