<?php


   /**
    * 
    * Handler class for ukeplan products
    * 
    * @author Tor Inge Løvkand <tor.inge@eurofoto.no>
    * 
    */


   import( 'website.order.manual.handlers.base' );
   import( 'website.order.merkelapporder' );

   

   class ManualOrderHandlerMerkelapp extends ManualOrderHandlerBase {
      
      
      public function Process( ManualOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         //$this->createFilePaths( $item );
         if( !$this->parseItem( $item ) ) throw new Exception( 'Failed to parse ukeplan project order' );
         
         
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
       * @author Tor Inge Løvkand <tor.inge@eurofoto.no>
       * 
       */
      private function parseItem( $item ) {
         
         

         try {
         
            $project = new UserMerkelappOrder( $item['referenceid'] );

         
         } catch( Exception $e ) {
            
            throw new Exception( 'No such project' );
            
         }

         
         if( $project instanceof DBMerkelappOrder && $project->isLoaded() ) {
            
           
            
            
            try{
               // Add user's own title of project on order row
               if( strlen( $item['usertitle'] ) > 0 ) {
                  $projecttitle = $item['product']['title'] . ': ' . $item['usertitle'] ;
               } else {
                  $projecttitle = $item['product']['title'];
               }
               
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
               (float)$item['unitprice'],
               utf8_encode( $projecttitle ),
               $project->id,
               $item['referenceid'] )->fetchSingle();
               
      
               $project->order_row_id = $rowid;
               $project->orderid = $this->orderid;
               $project->quantity = $item['quantity'];
               $project->articleid = $item['refid'];
               //$project->order_time = date( 'Y-m-d H:i:s' );
               if( $project->userid == $this->notloggedinuserid ) $project->userid = Login::userid();
               
               if( !$project->userid ){
                  $project->userid = Login::userid();
               }
               
               $project->save();
            }catch( Exception $e ){
               Util::Debug( $e->getMessage() );
            }            
            
            return true;
            
         }
         
         exit;
         return false;
         
      }
      
      
   }


?>