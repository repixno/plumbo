<?php


   /**
    * 
    * Handler class for ukeplan products
    * 
    * @author Tor Inge Løvkand <tor.inge@eurofoto.no>
    * 
    */


    import( 'website.order.handlers.base' );
    model( 'smilesontiles.project' );

   

    class OrderHandlerSmilesontiles extends OrderHandlerBase {
       
      
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
            $project = new DBSmilesProject( $item['referenceid'] );
        } catch( Exception $e ) {
            throw new Exception( 'No such project' );    
        }

        $projecttitle = $item['product']['title'];
         
        if( $project instanceof DBSmilesProject && $project->isLoaded() ) {
            
            $rowid = DB::query("
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
                $projecttitle,
                $project->id,
                $item['referenceid']
            )->fetchSingle();
            
            
            
            $project->orderid = $this->orderid;
            $project->save();
            
            return true;  
         }
         
         return false;
         
      }
      
      
   }


?>