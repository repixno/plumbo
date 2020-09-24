<?php


   /**
    * 
    * Handler class for ukeplan products
    * 
    * @author Tor Inge Løvkand <tor.inge@eurofoto.no>
    * 
    */


   import( 'website.order.handlers.base' );
   model( 'producteditor.producteditororder' );
   model( 'producteditor.producteditororderpage' );

   

   class OrderHandlerStempel extends OrderHandlerBase {
      
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         //$this->createFilePaths( $item );
         if( !$this->parseItem( $item ) ) throw new Exception( 'Failed to parse ukeplan project order' );
         
         
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
         
            $project = new DBproducteditorOrder( $item['referenceid'] );
            
            $projectpage = DB::query( "SELECT * FROM producteditor_order_page WHERE refid = ?", $item['referenceid'] )->fetchAll( DB::FETCH_ASSOC );
         
         } catch( Exception $e ) {
            throw new Exception( 'No such project' );   
         }
         
         if( $project ) {
            
            // Add user's own title of project on order row
            if( strlen( $item['usertitle'] ) > 0 ) {
               $projecttitle = $item['product']['title'] . ': ' . $item['usertitle'] ;
            } else {
               $projecttitle = $item['product']['title'] . ':' . $item['color']['name'];
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
            $item['unitprice'],
            utf8_encode( $projecttitle ),
            $project->id,
            $item['referenceid'] )->fetchSingle();
            

            $project->orderid = $this->orderid;

            //$project->order_time = date( 'Y-m-d H:i:s' );
            if( $project->userid == $this->notloggedinuserid ) $project->userid = Login::userid();
            
            if( !$project->userid ){
               $project->userid = Login::userid();
            }
            
            $project->save();
            
            
            
            
            return true;
            
         }
         
         
         return false;
         
      }
      
      
   }


?>