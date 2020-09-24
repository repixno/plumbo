<?php

   //config( 'local.mediaclip' );
   import( 'website.project' );
   import( 'website.product' );
   import('website.projectorder');
   
   class MediaclipOrderRequest {

      private $userid = null;
      private $realprojectid = null;
      private $productid = null;
      private $refid = null;
      private $quantity = null;
      private $numextrapages = null;
      private $sheetcount = null;
      private $ordername = null;
      private $title = null;
      private $xml = null;
      private $server = null;
      private $color = null;
      private $keyhole = null;
      private $textcolor = null;
      private $productionid = null;
      private $orderid = null;

      private $project = null;
      
      private $mediaclipfolder = 'ECommerceBridge';

      public function __construct( $orderdata = array() ) {

         if( count( $orderdata ) ) {
            
            $this->userid = (int) $orderdata['userid'];
            $this->realprojectid = (int) $orderdata['realprojectid'];
            $this->productid = (int) $orderdata['productid'];
            $this->quantity = (int) $orderdata['quantity'];
            $this->numextrapages = (int) $orderdata['numextrapages'];
            $this->sheetcount = (int) $orderdata['sheetcount'];
            $this->ordername = urldecode( $orderdata['ordername'] );
            $this->productionid = (int) $orderdata['production_id'];
            $this->project = new Project( $this->realprojectid );
            $this->refid = Project::productInfo($orderdata['productid']);
            $this->title = $this->project->title;
            $this->textcolor = Project::productInfo($this->productid, 'color');
            $this->keyhole = Project::productInfo($this->productid, 'keyhole');
            
            $this->server = Settings::Get( 'mediaclip', 'server', 'sandra.eurofoto.no' );
            
            $this->mediaclipfolder = Settings::Get( 'mediaclip', 'folder', 'ECommerceBridge' );  
            
            // Get the xml from mediaclip server
            try {

               if ( isset( $orderdata[ 'projectxml' ] ) ) {

                  $xml = $orderdata[ 'projectxml' ];

               } else {

                  try{
                     $xml = file_get_contents('http://' . $this->server . '/' . $this->mediaclipfolder . '/cart/' . $this->ordername.'.xml');
                     $xml = substr_replace( $xml, '', 0, 3 );
                  }catch( Exception $e ){
                     mail( 'tor.inge@eurofoto.no', "mediaclipbug", "server " . $this->server . "\nOrdername " . $this->ordername );
                     
                     if( $this->server == "jasmin.eurofoto.no" ){
                        $xml = file_get_contents('http://sandra.eurofoto.no/ECommerceBridge/cart/'.$this->ordername.'.xml');
                        $xml = substr_replace( $xml, '', 0, 3 );
                     }else if( $this->server == "sandra.eurofoto.no" ){
                        $xml = file_get_contents('http://jasmin.eurofoto.no/ECommerceBridge/cart/'.$this->ordername.'.xml');
                        $xml = substr_replace( $xml, '', 0, 3 );
                     }
                  }
                  
               }

               $this->xml = $xml;

            } catch( Exception $e ) {
               mail( 'tor.inge@eurofoto.no', "missing xml bug", $e->getMessage()  );
               throw new Exception( 'Missing xml on disk.' );

            }

         }

      }


      public function asArray() {

         return array(
            'userid' => $this->userid,
            'realprojectid' => $this->realprojectid,
            'productid' => $this->productid,
            'refid' => $this->refid,
            'quantity' => $this->quantity,
            'numextrapages' => $this->numextrapages,
            'sheetcount' => $this->sheetcount,
            'ordername' => $this->ordername,
            'title' => $this->title,
            'server' => $this->server,
            'textcolor' => $this->textcolor,
            'keyhole' => $this->keyhole,
         );

      }


      /**
       * Check if necessary properties are set
       *
       * @return boolean
       *
       */
      public function valid() {

         $orderData = MediaclipOrder::get();
         if( $this->userid > 0 && $this->realprojectid > 0 && $this->quantity > 0 ) {

            // Check for closing tag
            $pos = strrpos( $this->xml, "</orderRequest>" );
            if($pos > 0){
               return true;
            }
         }

         return false;

      }


      public function getUserId() {
         return $this->userid;
      }

      public function getRefId() {
         return $this->refid;
      }

      public function getRealProjectId() {
         return $this->realprojectid;
      }

      public function getExtraPages() {
         return $this->numextrapages;
      }

      public function getQuantity() {
         return $this->quantity;
      }

      public function getOrderId() {
         //return $this->realprojectid;  // Remove this line later. Only temporary
         return $this->orderid;
      }

      public function getProductOptionId() {

         if( $this->project instanceof Project && $this->project->isLoaded() ) {
            return $this->project->productoptionid;
         }

         return 0;

      }
      public function getProductionId() {
         
         $unique = DB::query( "SELECT max(id) FROM mediaclip_unique WHERE project = ?;", $this->realprojectid )->fetchSingle();
         
         return $unique;
      }

      public function getTitle() {
         return $this->title;
      }
      

      public function save() {

         $projectorder = new ProjectOrder();
         $projectorder->user_id = $this->userid;
         $projectorder->product_id = $this->productid;
         $projectorder->project_id = $this->realprojectid;
         $projectorder->article_id = $this->refid;
         $projectorder->quantity = $this->quantity;
         $projectorder->title = $this->title;
         $projectorder->xml = $this->xml;
         $projectorder->xtra = $this->numextrapages;
         $projectorder->keyhole = $this->keyhole;
         $projectorder->color = $this->textcolor;
         $projectorder->sheetcount = $this->sheetcount;
         $projectorder->production_id = $this->getProductionId();
         $projectorder->redeye = 0;
         $projectorder->save();
         
         $projectorder->used_images = $projectorder->usedImages();
         $projectorder->save();
         $this->saveThumb();
         
         $this->orderid = $projectorder->id;
         
         $this->project->saveThumb();
         $this->project->projectxml = $this->xml;
         $this->project->productid = $this->productid;
         $this->project->saved = date( 'Y-m-d H:i:s' );
         $this->project->xtra = $this->numextrapages;
         $this->project->sheetcount = $this->sheetcount;
         $this->project->save();

      }

      public function saveThumb(){
      
         $mediaclip_server = Settings::Get( 'mediaclip', 'server', 'jasmin.eurofoto.no' );      
         $thumbname = md5($this->getProductionId());
         $org_thumb = md5($this->realprojectid);
         $dir = sprintf( '/data/pd/ef28/mediaclip/cart/%s/', $this->productid);
         
         if(!file_exists($dir)){
            mkdir($dir);
         }
         
         try{
            try{
               $url = 'http://' . strtolower($mediaclip_server) . '/' .  $this->mediaclipfolder . '/cart/' . $org_thumb .'.jpg';
               file_put_contents($dir . $thumbname . ".jpg", file_get_contents($url));
            }catch( Exception $e ){
               if( $this->server == "jasmin.eurofoto.no" ){
                  $url = 'http://sandra.eurofoto.no/ECommerceBridge/temp/' . $org_thumb .'.jpg';
                  file_put_contents($dir . $thumbname . ".jpg", file_get_contents($url));
               }else if( $this->server == "sandra.eurofoto.no" ){
                  $url = 'http://jasmin.eurofoto.no/ECommerceBridge/temp/' . $org_thumb .'.jpg';
                  file_put_contents($dir . $thumbname . ".jpg", file_get_contents($url));
               }
               
            }
           
         }catch(Exeption $e){
            throw new Exception( 'Missing thumb on disk.' );
         }
      
      }

   }


?>
