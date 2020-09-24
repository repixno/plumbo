<?php


   /**
    * 
    * Base handler class for products
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   
   import( 'website.order.manual.handlers.base' );
   import( 'website.order.template' );
   import( 'website.giftordertemplate' );
   import( 'website.giftpagetemplate' );
   import( 'website.giftordertext' );
   import( 'website.giftorderclipart' );
   
   model( 'order.option' );
   
   
   class ManualOrderHandlerGifts extends ManualOrderHandlerBase {

      private $clipartpath;
      private $fontpath;
      private $originaltemplatespath;
      
      public function Process( ManualOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         $credit = $this->checkCredit( $item );
         
         //$this->createFilePaths( $item );
         $this->parseItem( $item );
		 //$this->checkLicense( $item );
         $this->Finalize();
         
         if( $item['redeyeremoval'] ){
            $this->finalizeRedeyeremoval($item['redeyeremoval']);
         }
         if( $item['varnish'] ){
            $this->finalizeXtra($item['varnish']);
         }
		 if( $item['upgrade'] ){
            $this->finalizeXtra($item['upgrade']);
         }
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
      }
      
      
      /**
       * Parse a gift template item and 
       * insert into necessary tables.
       * 
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function parseItem( $item ) {
         
         $templatereferenceid = $item['referenceid'];
         $quantity            = $item['quantity'];
		 
         $templateorders = $item['pages'];
         
		 foreach( $templateorders as $key=>$giftordertemplate ) {
            
            $unique        = sprintf( "%03d", $this->order->getStartLotNr() );
            $sartnr        = sprintf( "%03d", $item['prodno'] );
            $page          = sprintf( "%03d", $key);
            $fquantity     = sprintf( "%03d", $quantity );
            $filename      = $fquantity."-".$this->orderid."-".$unique."-".$sartnr."-".$page."."."jpg";
            $usermod       = '';
            //$productoption = $item['currentproductoption'];
            //$refsubid      = $productoption['refsubid'];
            
            $this->artnr = $sartnr; 
            
            // Dont know if this is necessary.
            // It seems always to be true in old code so
            // I'll set it as true here for now.
            if( !$usermod ) {
               $usermod = true;
            } else {
              $usermod = false; 
            }
            $usermod = true;
            
            // User wish for redeye correction?
            if( count( $item['redeyeremoval'] ) > 0 ) {
               $redeye = true;
            } else {
               $redeye = false;
            }
            
            if( count( $item['varnish'] ) > 0 ) {
               $varnish = true;
            } else {
               $varnish = false;
            }
			if( count( $item['upgrade'] ) > 0 ) {
               $upgrade = true;
            } else {
               $upgrade = false;
            }
            // Update object and save it
            $orderobject            = new OrderTemplate();
            $orderobject->orderid	= $this->orderid;
            $orderobject->artnr		=  $item['prodno'];
            $orderobject->templateid= $templatereferenceid;
            $orderobject->lot		= $unique;
            $orderobject->page		= $key;
            $orderobject->imageid	= 0;
            $orderobject->quantity	= $quantity;
            $orderobject->filename	= $filename;
            $orderobject->text		= $giftordertemplate['filename'];
            $orderobject->user_mod	= $usermod;
            $orderobject->redeye	= $redeye;
            $orderobject->varnish	= $varnish;
			$orderobject->upgrade	= $upgrade;
            $orderobject->save();
            
            // Is there an option for this gift?
            if( strlen( $refsubid ) ) {
               $options    = explode( '-', $refsubid );
               $mainoption = reset( $options );
               $suboption  = end( $options );
               
               $option              = new DBOrderOption();
               $option->orderid     = $this->orderid;
               $option->templateid  = $giftordertemplate->id;
               $option->option      = $mainoption;
               $option->suboption   = $suboption;
               $option->quantity    = $quantity;
               $option->save();
               
            }
         }
         
      }
      
      
   }
   
   
?>