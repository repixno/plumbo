<?php

   /**
    * 
    * Handle products tagged as textgift.
    * 
    * @author *TIL
    * 
    */


   import( 'website.order.handlers.base' );
   model( 'order.option' );
   
   class OrderHandlerTextGift extends OrderHandlerBase {
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::process( $order, $type, $item );
         $this->parseItem( $item );
         $this->finalize();
         
         $credit = $this->checkCredit( $item );
         
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
         
      }
      
      private function parseItem( $item ) {

         $productoption = $item['currentproductoption'];
         $refsubid      = $productoption['refsubid'];
         $optionid = $item['optionid'];
         $quantity      = $item['quantity'];
         $referenceid = $item['referenceid'];
         
         if( strlen( $refsubid ) ) {
               $options    = explode( '-', $refsubid );
               $mainoption = reset( $options );
               $suboption  = end( $options );
               
               $option              = new DBOrderOption();
               $option->orderid     = $this->orderid;
               $option->templateid  = $referenceid;
               $option->option      = $mainoption;
               $option->suboption   = $suboption;
               $option->quantity    = $quantity;
               $option->save();
               
         }
      }
      
      
   }



?>