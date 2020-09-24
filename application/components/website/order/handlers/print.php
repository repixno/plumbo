<?PHP


   /**
    * 
    * Handle prints in order
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    * @todo Needs to remove credits from table when used
    * 
    */
   
   import( 'website.order.handlers.base' );
   import( 'website.order.image' );
   
   class OrderHandlerPrints extends OrderHandlerBase {
      
      private $correctionmethod;
      private $productionmethod;
      private $paperquality;
      
      public function Process( UserOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         // Is this a print order row?
         // Do not do this if it's methods
         if( $type == 'prints' || $type == 'enlargements' ) {
            
            $this->createFilePaths( $item );
            $this->parseItem( $item );
            $credit = $this->checkCredit( $item );
            
         }
         $this->checkLicense( $item );
         $this->Finalize();
         
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
         
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
         
         $orderdirectory = "$this->downloadpath/$this->today";
         
         if( !is_dir( $orderdirectory ) ) {
            mkdir( $orderdirectory, 0750 );
	      }
	
	      $orderdirectory.= "/$this->orderid";
	      if( !is_dir( $orderdirectory ) ) {
	         mkdir( $orderdirectory, 0750 );
	      }
	      
	      exec( "touch $orderdirectory/WAITINGFORFILES" );
	      
      }
      
      
      
      /**
       * Setup and Update print item tables
       *
       * @param array $item
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function parseItem( $item ) {
         
         
         $images = array();
         $images = $item['images'];
         
         $crop = $item['images']['crop'];
         


         $imagenum = 0;
         $this->checkCredit( $item );
         
         foreach( $images as $imageid => $quantity ) {
            
            if( !is_numeric( $quantity ) ) $quantity = 0;
            
            if( $quantity > 0 ) {
            
               // Load image object
               try {
                  
                  try{
                     //$image = new Image( $imageid );
                     $image = new DBObject( $imageid );
                  }catch (Exception $e){
                     mail('tor.inge@eurofoto.no', "Image $imageid bugs.", $e );
                     continue;
                  }

                  $filetype = $image->filetype;
                  $title = $image->title;
                  $exifdate = $image->exif_date;
                  
                  if( $unixtime = strtotime( $exif_date ) ) {
                     $exifdate = strftime( "%Y-%m-%dT%H:%M:%S", $unixtime );
                  }
                  if( !$exifdate ) {
                     $exifdate = '1967-10-28T13:14:15';
                  }
                  
                  $unique  = sprintf( "%03d", $this->order->getStartLotNr() );  // Lot number on whole order
                  $currlot = sprintf( "%03d", $imagenum ); // Seems to be lotnr on single artnr
                  $sartnr  = sprintf( "%03d", $item['refid'] );
                  if( !is_numeric( $quantity ) ) $quantity = 0;
                  
                  
                  // I need to checkup what the standard is here. What do they mean.
                  $filename = sprintf( "%03d", $quantity )."-".$this->orderid."-".$unique."-".$sartnr."-".$currlot.".".$filetype;
                  
                  $oimage = new DBOrderImage();
                  $oimage->orderid     = $this->orderid;
                  $oimage->artnr       = $item['refid'];
                  $oimage->lot         = $currlot;
                  $oimage->bid         = $imageid;
                  $oimage->quantity    = $quantity;
                  $oimage->cartversion = 1;
                  $oimage->filename    = $filename;
                  $oimage->title       = $title;
                  $oimage->exifdate    = $exifdate;
                  
 
                  
                  if( is_array( $crop[$imageid] ) ){
                     $cropimage = $crop[$imageid];

                     
                     if( $cropimage['width'] ){
                        $prinratio = $image->x / $cropimage['width'];
                     }
                     
                     $oimage->x = $cropimage['x1'] * $prinratio;
                     $oimage->y = $cropimage['y1'] * $prinratio;
                     $oimage->dy = $cropimage['dy'] * $prinratio;
                     $oimage->dx = $cropimage['dx'] * $prinratio;
                     $oimage->crop_width = $cropimage['width'];
                     $oimage->crop_height = $cropimage['height'];
                     $oimage->fitin = $cropimage['fitin'];
                     
                  }

                  $oimage->save();


                  $this->produceImage( $oimage );
                  
                  // Enum lot numbers
                  $imagenum++;
                  $this->order->increaseStartLotNr();
                  
               } catch( Exception $e ) {
                  mail('tor.inge@eurofoto.no', 'Printorder failed', $e );
                  throw new Exception("Printorder failed!"); 
                  // What 2 do?
               }
            
            }
            
         }
         
      }
      
      
      
      /**
       * Links an image to the production directory
       *
       * @param DBOrderImage $oimage
       * @return boolean
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      private function produceImage( DBOrderImage $oimage ) {
         
         $refid            = $oimage->artnr;
         $imageid          = $oimage->bid;
         $new_filename     = $oimage->filename;
         $orderdirectory   = "$this->downloadpath/$this->today";
         
         if( !is_dir( $orderdirectory."/".$refid ) ) {
            mkdir( $orderdirectory."/".$refid, 0750 );
         }
         
         set_time_limit( 30 );
         //$image = new Image( $imageid );
         $image = new DBObject( $imageid );
         if( !$image instanceof Image && !$image->isLoaded() ) {
            $oimage->not_exists = true;
            $oimage->save();
            return false;
         }
         
         $filename = $image->filename;

         $partition = explode( "/", $filename );
         $partition = $partition[0];
         $orderdir = $this->imagepath."/".$partition."/"."print_download";
         
         if( !is_dir( $orderdir ) ) {
            mkdir( $orderdir, 0750 );
         }
         
         $orderdir.= "/".$this->today;
         if( !is_dir( $orderdir ) ) {
            mkdir( $orderdir, 0750 );
         }
         
         $orderdir.= "/$this->orderid";
         if( !is_dir( $orderdir ) ) {
            mkdir( $orderdir, 0750 );
         }
         
         $orderdir.= "/$refid";
         if( !is_dir( $orderdir ) ) {
            mkdir( $orderdir, 0750 );
         }
         
         
         if( strlen( $new_filename ) > 0 ) {
            
            if( !file_exists( $orderdir."/".$new_filename ) ) {
               
               if( !file_exists( $this->imagepath."/".$filename ) ) {
                  $oimage->not_exists = true;
                  $oimage->save();
               }
               else{ // Everythings ok. Link file
                  link( $this->imagepath."/".$filename, $orderdir."/".$new_filename );
                  chmod( $orderdir."/".$new_filename, 0664 );
               }
               
            }
            
         } 
         
         
         return true;

      }
      
   }
   
?>