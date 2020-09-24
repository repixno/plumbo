<?php


   /**
    * 
    * Handler class for ukeplan products
    * 
    * @author Tor Inge Løvkand <tor.inge@eurofoto.no>
    * 
    */


   import( 'website.order.handlers.base' );
   import( 'website.order.ukeplanorder' );
   import( 'website.giftpagetemplate' );
   import( 'website.order.template' );
   

   class OrderHandlerUkeplan extends OrderHandlerBase {
      
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         //$this->createFilePaths( $item );
         if( !$this->parseItem( $item ) ) throw new Exception( 'Failed to parse ukeplan project order' );

         if( $item['maskit'] ){
            $this->finalizeMaskit($item['maskit']);
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
         
            $project = UserUkeplanOrder::fromIdAndUserId( Login::userid(), $item['referenceid'] );

            // Is this user's own project
            if( !$project instanceof DBUkeplanOrder || !$project->isLoaded() ) {
               
               // Try loading project with not logged in user
               $project = UserUkeplanOrder::fromIdAndUserId( $this->notloggedinuserid, $item['referenceid'] );
               
            }
         
         } catch( Exception $e ) {
            
            throw new Exception( 'No such project' );
            
         }

         
         if( $project instanceof DBUkeplanOrder && $project->isLoaded() ) {
            
               $xml = new SimpleXMLElement( $project->xml );
               
               foreach ( $xml->image as $image ){                  
                  if( is_numeric(  trim( $image['imageid']  ) ) ){
                     
                     $image               = new Image( $image['imageid'] );
                     $filename            = $image->filename;
                     $filetype            = $image->filetype;
                     $title               = $image->title;
                     $originalfilename    =  $image['imageid'] . '.jpg';
                     $templateorderfiles  = '';
                  
                     $partition              = explode( '/', $image->filename );
                     $partition              = reset( $partition );
                     $orderdirectory         = "$this->imagepath/$partition/"."print_download";
                     
                     // Create production directories to put files in
                     if( !is_dir( $orderdirectory ) ) {
                        mkdir( $orderdirectory, 0750 );
                     }
                     $orderdirectory.= "/".$this->today;
                     if( !is_dir( $orderdirectory ) ) {
                        mkdir( $orderdirectory, 0750 );
                     }
                     $orderdirectory.= "/$this->orderid";
                     if( !is_dir( $orderdirectory ) ) {
                        mkdir( $orderdirectory, 0750 );
                     }
                     $orderdirectory.= "/" . $item['prodno'];
                     if( !is_dir( $orderdirectory ) ) {
                        mkdir( $orderdirectory, 0750 );
                     }
                     if( !is_dir( $orderdirectory."/autoedit" ) ) {
                        mkdir( $orderdirectory."/autoedit", 0750 );
                     }
   
                     
                     if( !file_exists( $orderdirectory."/autoedit/".$originalfilename ) ) {
                        link( $this->imagepath."/".$filename, $orderdirectory."/autoedit/".$originalfilename );
                     }
                     chmod( $orderdirectory."/autoedit/".$originalfilename, 0664 );
                  
                  }
               }
               
               if( empty( $orderdirectory ) ){
                  config( 'website.storage' );
                  $partition = Settings::get( 'storage', 'currentpartition' );
                  $orderdirectory = "$this->imagepath/$partition/print_download/$this->today/$this->orderid/" . $item['prodno'] ;
                  
                  if( !is_dir( $orderdirectory )){
                     mkdir( $orderdirectory , 0750, true);
                  }
                  if( !is_dir( $orderdirectory."/autoedit" ) ) {
                     mkdir( $orderdirectory."/autoedit", 0750 );
                  }
               }
               $templateid = $item['templateid'];
               
               $tpagedata              = GiftPageTemplate::fromTemplateIdAndPageId( $templateid , 0 );
               
               
               $templatefile           = Settings::Get( 'paths', 'originaltemplates' ) . "/$tpagedata->fullsize_src";
               $templatetarget         = $tpagedata->fullsize_src;
               
               
               $templateorderfiles .=  $item['prodno'] . "/autoedit/$orginalfilename ";
               if( !file_exists( $orderdirectory."/autoedit/$templatetarget" ) ) {
                  copy( $templatefile, $orderdirectory."/autoedit/".$templatetarget );
                  chmod( $orderdirectory."/autoedit/".$templatetarget, 0664 );
                  $templateorderfiles .= "$artnr/autoedit/$templatetarget ";
               }
            
            
            
            // Add user's own title of project on order row
            if( strlen( $item['usertitle'] ) > 0 ) {
               $projecttitle = $item['product']['title'] .': '.  $item['usertitle'];
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
            $item['unitprice'],
            $projecttitle,
            $project->id,
            $item['referenceid'] )->fetchSingle();
            
   
            $project->order_row_id = $rowid;
            $project->orderid = $this->orderid;
            //$project->order_time = date( 'Y-m-d H:i:s' );
            if( $project->userid == $this->notloggedinuserid ) $project->userid = Login::userid();
            $project->save();
            
            
            $unique        = sprintf( "%03d", $this->order->getStartLotNr() );
            $sartnr        = sprintf( "%03d", $item['refid'] );
            $page          = 0;
            $fquantity     = sprintf( "%03d", $quantity );
            $filename      = $fquantity."-".$this->orderid."-".$unique."-".$sartnr."-".$page."."."jpg";
            
            // User wish for redeye correction?
            if( count( $item['redeyeremoval'] ) > 0 ) {
               $redeye = true;
            } else {
               $redeye = false;
            }

            // Update object and save it
            $orderobject               = new OrderTemplate();
            $orderobject->orderid      = $this->orderid;
            $orderobject->artnr        = $item['refid'];
            $orderobject->templateid   = $templateid;
            $orderobject->lot          = $project->id;
            $orderobject->page         = 0;
            $orderobject->imageid      = $xml->image[0]['imageid'];
            $orderobject->quantity     = $item['quantity'];
            $orderobject->filename     = $filename;
            $orderobject->text         = '';
            $orderobject->user_mod     = true;
            $orderobject->redeye       = $redeye;
            $orderobject->save();
            
            
            return true;
            
         }
         
         
         return false;
         
      }
      
      /**
      * Creates the necessary file paths
      * for later production export.
      *
      * @param string $type
      * @param array $item
      * 
      * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
      */
      private function createFilePaths( $item ) {
         
         $orderdirectory = "$this->downloadpath/".$this->today;

         if( !is_dir( $orderdirectory ) ) {
            mkdir( $orderdirectory, 0750 );
	      }
	
	      $orderdirectory.= "/$this->orderid";
	      if( !is_dir( $orderdirectory ) ) {
	         mkdir( $orderdirectory, 0750 );
	      }
	      
	      if( !is_dir( "$orderdirectory/autoedit" ) ) {
	         mkdir( "$orderdirectory/autoedit", 0750 );
	      }

	      if( is_dir( "$orderdirectory/autoedit" ) ) {

	         $fp = fopen( "$orderdirectory/README.txt", "w" );
	         fputs( $fp, "This order is not complete until a file named COMPLETED is in this directory" );
	         fclose($fp);

	         $tmpfile = "autoedit/xfiles.txt ";
	         $fp = fopen( "$orderdirectory/autoedit/xfiles.txt","a" );
	         fputs( $fp, "$tmpfile\n" );
	         fclose($fp);

	      }

	      exec( "touch $orderdirectory/WAITINGFORFILES" );
	      
      }
      
      
   }


?>