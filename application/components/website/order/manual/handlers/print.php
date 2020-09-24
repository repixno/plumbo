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
   
   import( 'website.order.manual.handlers.base' );
   import( 'website.order.manual.image' );
   
   class ManualOrderHandlerPrints extends ManualOrderHandlerBase {
      
      private $correctionmethod;
      private $productionmethod;
      private $paperquality;
      
      public function Process( ManualOrder $order, $type, $item ) {
         
         parent::Process( $order, $type, $item );
         
         // Is this a print order row?
         // Do not do this if it's methods
         if( $type == 'prints' || $type == 'enlargements' ){
            $this->parseItem( $item );
            $credit = $this->checkCredit( $item );            
         }
         
         $this->Finalize();
         
         // Did product have credits?
         if( is_array( $credit ) ) {
            $this->finalizeCredits( $credit, $item);
         }
         
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
         
         if( !is_array( $images ) ){
            return;
         }
         
         
         foreach( $images as $imageid => $res ) {
            
            $imagepath = $res[ 'path' ];
            $quantity = $res['quantity'];
            
            if( $res['fit-in'] == 'true' ){
               $fitin = 1;
            }else{
               $fitin = null;
            }
            
            if( !is_numeric( $quantity ) ) $quantity = 0;
            
            if( $quantity > 0 ) {
            
               // Load image object
               try {
                  
                  //$image = new Imagick( $imagepath );
                  
                  if( is_dir( $imagepath) ){
                     
                     $imagepaths = $imagepath;
                     foreach( glob( $imagepaths . '/*') as $imagepath ){
                        
                        
                        try{
                           $exifdata = exif_read_data( $imagepath );
                        }catch ( Exception  $e ){
                           
                        }
                        
                        $fileinfo = pathinfo( $imagepath );
                        
                        $filetype = $fileinfo['extension'];
                        $title = $fileinfo['basename'];
                        
                        $exifdate = $exifdata['DateTimeOriginal'];
                        
                        if( $unixtime = strtotime( $exif_date ) ) {
                           $exifdate = strftime( "%Y-%m-%dT%H:%M:%S", $unixtime );
                        }
                        if(  $exifdate < '1967-10-28T13:14:15' ) {
                           $exifdate = '1967-10-28T13:14:15';
                        }
                        
                        $unique  = sprintf( "%03d", $this->order->getStartLotNr() );  // Lot number on whole order
                        $currlot = sprintf( "%03d", $imagenum ); // Seems to be lotnr on single artnr
                        $sartnr  = sprintf( "%03d", $item['refid'] );
                        if( !is_numeric( $quantity ) ) $quantity = 0;
                        
                        if( !empty( $res['text'] ) ){
                           $imagedescription = $res['text'];
                        }else{
                           $imagedescription = '';
                        }
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
                        $oimage->manualpath  = $imagepath;
                        $oimage->fitin       = $fitin;
                        $oimage->text        = $imagedescription;
                        
                        $oimage->save();
                        
                        // Enum lot numbers
                        $imagenum++;
                        $this->order->increaseStartLotNr();
                        
                     }
                     
                     
                  }
                  else{
                     
                     try{
                        $exifdata = exif_read_data( $imagepath );
                     }catch ( Exception  $e ){
                        
                     }
                  
                     $fileinfo = pathinfo( $imagepath );
                     
                     $filetype = $fileinfo['extension'];
                     $title = $fileinfo['basename'];
                     
                     $exifdate = $exifdata['DateTimeOriginal'];
                     
                     if( $unixtime = strtotime( $exif_date ) ) {
                        $exifdate = strftime( "%Y-%m-%dT%H:%M:%S", $unixtime );
                     }
                     if(  $exifdate < '1967-10-28T13:14:15' ) {
                        $exifdate = '1967-10-28T13:14:15';
                     }
                     
                     $unique  = sprintf( "%03d", $this->order->getStartLotNr() );  // Lot number on whole order
                     $currlot = sprintf( "%03d", $imagenum ); // Seems to be lotnr on single artnr
                     $sartnr  = sprintf( "%03d", $item['refid'] );
                     if( !is_numeric( $quantity ) ) $quantity = 0;
                     
                     if( !empty( $res['text'] ) ){
                        $imagedescription = $res['text'];
                     }else{
                        $imagedescription = '';
                     }
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
                     $oimage->manualpath  = $imagepath;
                     $oimage->fitin       = $fitin;
                     $oimage->text        = $imagedescription;
                     
                     $oimage->save();
                     
                     // Enum lot numbers
                     $imagenum++;
                     $this->order->increaseStartLotNr();
                  }
                  
               } catch( Exception $e ) {
                  mail('tor.inge@eurofoto.no', 'Printorder failed', $e );
                  //throw new Exception("Printorder failed!"); 
                  throw new Exception($e); 
                  // What 2 do?
               }
            
            }
            
         }
         
      }
      
   }
   
?>