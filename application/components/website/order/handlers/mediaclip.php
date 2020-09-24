<?php


   /**
    * 
    * Handler class for mediaclip products
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'website.order.handlers.base' );
   import( 'website.order.projectorder' );
   
   config( 'website.mediaclip' );
   

   class OrderHandlerMediaclip extends OrderHandlerBase {
      
      private $notloggedinuserid;
      private $refidextrapages;
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         $extrapages = $settings['mediaclip']['extrapages'];
         
         // Setup properties
         $settings = Settings::GetSection( 'mediaclip' );
         $this->refidextrapages = $settings['extrapages']['hardcover'];
         $this->notloggedinuserid = $settings['notloggedinuser'];
         
         if( !$this->parseItem( $item ) ) throw new Exception( 'Failed to parse mediaclip project order' );
         
         $this->checkLicense( $item );
         
         if( $item['redeyeremoval'] ){
            $this->finalizeRedeyeremoval($item['redeyeremoval']);
         }
         
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
         
         $credit = $this->checkCredit( $item );
         
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
         
      }
      
      

      /**
       * Parse and handle the user project
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      private function parseItem( $item ) {
         
         try {
         
            $project = UserProjectOrder::fromIdAndUserId( Login::userid(), $item['referenceid'] );
            
            // Is this user's own project
            if( !$project instanceof DBUserProjectOrder || !$project->isLoaded() ) {
               
               // Try loading project with not logged in user
               $project = UserProjectOrder::fromIdAndUserId( $this->notloggedinuserid, $item['referenceid'] );
               
            } 
         
         } catch( Exception $e ) {
            
            throw new Exception( 'No such project' );
            
         }
         
         
         if( $project instanceof DBUserProjectOrder && $project->isLoaded() ) {
            
            // Add user's own title of project on order row
            if( strlen( $item['usertitle'] ) > 0 ) {
               $projecttitle = utf8_decode( $item['product']['title'] ).': '.utf8_decode( $item['usertitle'] );
            } else {
               $projecttitle = utf8_decode( $item['product']['title'] );
            }
            
            // Project extrapages
            $numextrapages = (int) $item['extrapages']['quantity'];
            
            $rowid = DB::query( "
            INSERT INTO 
               historie_ordrelinje ( 
                  id,
                  ordrenr,
                  artikkelnr,
                  antall,
                  pris,
                  tekst,
                  malid,
                  product_id
               ) VALUES(
                  DEFAULT,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?,
                  ?
               ) RETURNING id
            ", 
            $this->orderid, 
            $item['refid'],
            $item['quantity'],
            $item['unitprice'],
            utf8_encode( $projecttitle ),
            $item['templateid'],
            $item['referenceid'] )->fetchSingle();
            
   
            $project->order_row_id = $rowid;
            $project->order_id = $this->orderid;
            $project->order_time = date( 'Y-m-d H:i:s' );
            if( $project->userid == $this->notloggedinuserid ) $project->userid = Login::userid();
            $project->save();
            
            // Insert extra pages of mediaclip project
            if( $numextrapages > 0 ) {
               
               $extrapages = ProductOption::fromRefId( $this->refidextrapages );
               $epprice = $extrapages->price;
               $eprefid = $extrapages->refid;
               
               if( $extrapages instanceof ProductOption && $extrapages->isLoaded() ) {
                  
                  $epproduct = new Product( $extrapages->productid );
                  
                  $orderrow            = new DBOrderRow();
                  $orderrow->orderid   = $this->orderid;
                  $orderrow->artnr     = $eprefid;
                  $orderrow->quantity  = $numextrapages;
                  $orderrow->price     = $epprice;
                  $orderrow->text      = utf8_encode( $epproduct->title );
                  $orderrow->save();
                  
               } else {
                  
                  return false;
                  
               }
               
            }
            
            return true;
            
         }
         
         
         return false;
         
      }
      
      
   }


?>