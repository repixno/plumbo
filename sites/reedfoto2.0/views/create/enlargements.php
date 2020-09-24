<?php
   
   import( 'website.uploadhelper' );
   import( 'website.album' );
   import( 'website.image' );
   
   import( 'legacy.ef2x' );
   
   class EnlargementsCreator extends WebPage implements IView {
      
      private $productoptions;
      private $gifttemplates;
      
      protected $template = 'create.enlargements';
      
      public function Execute( $productid = 0, $productoptionid = 0, $gifttemplateid = 0, $imageid = 0 ) {
         
         if( UploadHelper::iOsDetection() && !Login::isLoggedIn() ){
            
            $reloacteurl= base64_encode('/create/enlargements');
            
            relocate('/login/?ref=' . $reloacteurl);
         }
         
         $this->batchid = UploadHelper::getBatchId();

         
         //$this->albums = Album::enum();
         //$this->sharedAlbums = Album::enumSharedToMe( true );

         if(Login::userid()){
            $lastimages = new Image();         
            $imageid = $lastimages->collection( 'bid', $where = array( 'owner_uid' => Login::userid(), 'deleted_at' => NULL ), 'dato DESC, time DESC' )->fetchSingle();
         }
         
         if($imageid > 0){
            $image = new Image( $imageid );
            
            $this->selected = array(
               'imageid' => $imageid,
               'image' => $image->asArray(),
            );
         }
         
      }
	  
	  public function stoydempa(){
		 $this->template = 'create.stoydempa';
		 $this->Execute();
	  }
	  
	  public function stoydempende(){
		 $this->template = 'create.stoydempa';
		 $this->Execute();
	  }
      

		/**
		 * Save the editor data to correct tables
		 * 
		 */
		public function save() {
		   
		   function zIndexSort( $a, $b ) {
            if( $a->zindex == $b->zindex ) {
               return 0;
            }
            return ( $a->zindex < $b->zindex ) ? -1 : 1;
		   }
		   
		   $this->setTemplate( '' );
         import( 'website.giftpagetemplate' );
         import( 'website.giftordertemplate' );
         import( 'website.giftordertext' );
         import( 'website.giftorderclipart' );
         import( 'website.product' );
         import( 'website.cart' );
         
         $templateOrder = new GiftOrderTemplate();
		   
         $webcoords = isset( $_POST['web'] ) ? true : false;
		   $editordata = (string)$_POST["pages"];
		   
		   $redeye = false;
		   $varnish = false;
		   
		   $editordata = urldecode( stripslashes( $editordata ) );
		   $editorObject = json_decode( $editordata );
		   
		   $lastProductOptionid = 0;
		   
		   // Setup page per page of editor data
		   if( count( $editorObject ) > 0 ) {
		      
		      foreach( $editorObject as $pageId => $page ) {
		         

      		   $quantity = $page->giftQuantity;
      		   if( $quantity == 0 ) $quantity = 1;
      		   $productOptionId = $page->productoptionid;
      		   $productId = $page->productid;
      		   
      		   $lastProductOptionid = $productOptionId;
      		   
       
      		   // Add gift to cart
      		  $productOption = new ProductOption( $productOptionId );
		  $refId = $productOption->refid;
               	
              
                    
      		   $images = $page->image;
      	
      		   // first, rescale the fullsize a bit
      		   $scale_x = $page->image->fullsize_x / $page->image->editor_x;
      		   $scale_y = $page->image>fullsize_y / $page->image->editor_y;
      		   
      		   // Set page/template data
      		   $templateOrder->userid     = Login::userid();
      		   
      		   $templateOrder->templateid = 0;
      		   $templateOrder->refid      = $refId;
      		   $templateOrder->pageid     = $pageId;
      		   $templateOrder->imageid    = $page->image->bid;
      		   $templateOrder->x          = $webcoords ? $page->image->x : ( round( ( $page->image->x / $scale_x ) ) );
      		   $templateOrder->y          = $webcoords ? $page->image->y : ( round( ( $page->image->y / $scale_y ) ) );
      		   $templateOrder->dx         = $webcoords ? $page->image->dx : round( $page->image->dx / $scale_x );
      		   $templateOrder->dy         = $webcoords ? $page->image->dy : round( $page->image->dy / $scale_y );
      		   $templateOrder->editor_x   = $page->image->editor_x;
      		   $templateOrder->editor_y   = $page->image->editor_y;
      		   $templateOrder->printsize_x   = $page->image->printsize_x;
      		   $templateOrder->printsize_y   = $page->image->printsize_y;
      		   $templateOrder->rotate     = isset( $page->image->rotate ) ? $page->image->rotate : 0;
      		   $templateOrder->printtype = $page->image->printtype;

      		   // Save page data to db
      		   $templateOrder->save();
      		   
      		   // set red-eye
      		  $redeye = $page->redeye;
		  $varnish = $page->varnish;
		  $upgrade = $page->upgrade;
      		   
		  }
		       
		       
		  if( $templateOrder->id > 0 ) {
		     
		  $cart = new Cart();
		  $cart->addItemByProductOptionId( $lastProductOptionid, 
		      $quantity, 
		      array( 
			  "templateorderid" => $templateOrder->id,
			  "redeyeremoval" 	=> $redeye ? true : false,
			  "varnish"		=> $varnish? true : false,
			  "upgrade"		=> $upgrade
		      )
		  );
   		      
               $cart->save();
		      
		       echo "OK";
		       die();
		       
		      } else {
		         
		         throw new Exception( 'Failed to save gifteditor data. Missing templateorderid. Productoptionid = '.$lastProductOptionid );
		         
		      }
		      
		   }
		   
		   echo "FAILED";
		   die();
		   
		}

       
   }
   
?>