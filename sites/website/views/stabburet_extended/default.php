<?php
   
   import( 'website.product' );
   import( 'website.gifttemplate' );
   import( 'website.uploadhelper' );
   import( 'website.album' );
   import( 'website.image' );
   
   import( 'legacy.ef2x' );
   
   class GiftCreator extends WebPage implements IView {
      
      private $productoptions;
      private $gifttemplates;
      
      protected $template = 'create.stabburet';
      
      public function Execute( $productid = 2736, $productoptionid = 2737, $gifttemplateid = 2553, $imageid = 0 ) {
         
         if( date('Y-m-d') > '2012-12-31' ){
            
            $this->template = 'create.stabburet-avslutta';
            
         }else{
            
        
         Login::logout();
         $this->batchid = UploadHelper::getBatchId();
         
         
         $stabburet_cart = Session::get( 'Stabburet-cart' );
         
         
         if( $stabburet_cart['value'] >= 4 ){
            $this->overmaxorders = "true";
         }
         
         
         if( !$productid ) {
            
            if( !$productoptionid ) {
               
               if( !$gifttemplateid ) {
                  
                  throw new CriticalException( 'You need to specify at least one parameter (product, option, template)' );
                  
               }
               
               $productoptionid = $this->productOptionIdFromGiftTemplateId( $gifttemplateid );
               
            }
            
            $productid = $this->productIdFromProductOptionId( $productoptionid );
            
         }
         
         if( !$productoptionid && $gifttemplateid ) {
            
            if( $productid ) {
            
               $productoptionid = $this->productOptionIdFromGiftTemplateId( $gifttemplateid, $productid );
               
            } else {
               
               $productoptionid = $this->productOptionIdFromGiftTemplateId( $gifttemplateid );
               
            }
            
         }
         
         $this->cacheProductOptions( $productid );
         
         if( !$productoptionid ) {
            
            if( !count( $this->productoptions ) ) {
               
               throw new CriticalException( 'The product you have specified does not have any product options' );
               
            }
            
            list( $productoptionid ) = $this->productoptions;
            
         }
         
         $productoption = new ProductOption( $productoptionid );
         if( $productoption->productid != $productid ) {
            
            throw new CriticalException( 'The product option you have specified does not match the given product' );
            
         }
         
         $this->cacheGiftTemplates( $productoption->refid );
         
         if( !$gifttemplateid ) {
            
            if( !count( $this->gifttemplates ) ) {
               
               throw new CriticalException( 'The product option you have specified does not have any templates' );
               
            }
            
            list( $gifttemplateid ) = $this->gifttemplates;
            
         }
         
         $template = new GiftTemplate( $gifttemplateid );
         if( $template->articleid != $productoption->refid ) {
            
            throw new CriticalException( 'The template you have specified does not match the given product option' );
            
         }
         


         $product = new Product( $productid );
         
         if( !$imageid ) $imageid = Image::selected();
         
         $image = new Image( $imageid );
         
         $this->selected = array(
            'templateid' => $gifttemplateid,
            'template' => $template->asArray(),
            'productoptionid' => $productoptionid,
            'productoption' => $productoption->asArray(),
            'productid' => $productid,
            'product' => $product->asArray(),
            'imageid' => $imageid,
            'image' => $image->asArray(),
         );
         
         $alloptions = array();
         $alltemplates = array();
         
         foreach( $this->gifttemplates as $gifttemplateid ) {
            
            $gifttemplate = new GiftTemplate( $gifttemplateid );
            $alltemplates[] = $gifttemplate->asArray();
            
         }
         
         foreach( $this->productoptions as $productoptionid ) {
            
            $productoption = new ProductOption( $productoptionid );
            $alloptions[] = $productoption->asArray();
            
         }
         
                  
         $clipart = array();
         foreach( DB::query( 'SELECT clipcatid, name FROM clipart_category ORDER BY sorting' )->fetchAll() as $row ) {
            
            list( $categoryid, $nameid ) = $row;
            $category = array(
               'id' => $categoryid,
               'name' => EF2x::getLanguageResource( $nameid ),
            );
            
            $items = array();
            foreach( DB::query( 'SELECT clipid, filtype FROM clipart WHERE category = ?', $categoryid )->fetchAll() as $sub ) {
               $items[] = array(
                  'id' => array_shift( $sub ),
                  'extension' => array_shift( $sub ),
               );
            }
            
            $category['items'] = $items;
            $clipart[] = $category;
            
         }
         
         $this->collections = array(
            'gifttemplates' => $alltemplates,
            'productoptions' => $alloptions,
            'clipart' => $clipart,
         );
         
         
         
         $this->albums = Album::enum();
         $this->sharedAlbums = Album::enumSharedToMe( true );
          }
         
      }
      
      
      public function Finished( $orderid = 0){
         $this->template = 'create.stabburetfinish';
         
         $this->orderid = $orderid;
      }
      
      private function productOptionIdFromGiftTemplateId( $gifttemplateid, $productid = 0 ) {
         
         $artnr = (int) DB::query( 'SELECT artikkelnr FROM mal WHERE malid = ?', $gifttemplateid )->fetchSingle();
         if( $productid ) {
            return (int) DB::query( 'SELECT id FROM site_product_option WHERE refid = ? AND productid = ?', $artnr, $productid )->fetchSingle();
         } else {
            return (int) DB::query( 'SELECT id FROM site_product_option WHERE refid = ?', $artnr )->fetchSingle();
         }
         
      }
      
      private function productIdFromProductOptionId( $productoptionid ) {
         
         return (int) DB::query( 'SELECT productid FROM site_product_option WHERE id = ?', $productoptionid )->fetchSingle();
         
      }
      
      private function cacheGiftTemplates( $productoptionrefid ) {
         
         $this->gifttemplates = array();
         $collection = DB::query( 'SELECT malid FROM mal WHERE artikkelnr = ? AND visible = true ORDER BY priority', $productoptionrefid );
         while( list( $gifttemplateid ) = $collection->fetchRow() ) {
            $this->gifttemplates[] = $gifttemplateid;
         }
         
         return true;
         
      }
      
      private function cacheProductOptions( $productid ) {
         
         $this->productoptions = array();
         $product = new Product( $productid );
         foreach( $product->options as $option ) {
            $this->productoptions[] = $option->id;
         }
         
         return true;
         
      }

		/**
		 * Save the editor data to correct tables
		 * 
		 * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
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
		   
		   //file_put_contents( "/tmp/debug_180811.txt", print_r( $editordata, true ) );
		   
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
      		   $texts = $page->texts;
      		   $cliparts = $page->cliparts;
      		   
      		   /* FORMULAS
      		   sx = ( edit_image_dx / fullsize_x * scale630 );
      		   sy = ( edit_image_dy / fullsize_y * scale630 );
      		   x = edit_image_x + ( png_x * sx );
               y = edit_image_y + ( png_y * sy );
               dx = png_dx * sx;
               dy = png_dy * sy;
               */
      		   
      		   // load the gift template
      		   $giftpagetemplate = new GiftPageTemplate( $page->malpageid );
      		   
      		   
      		   //file_put_contents( "/tmp/webcoords_180811.txt", print_r(  ( $page->image->x / $scale_x ) + $giftpagetemplate->edit_mal_x , true ) );
      		   
      		   // Set page/template data
      		   $templateOrder->userid     = Login::userid();
      		   $templateOrder->templateid = $page->malid;
      		   $templateOrder->refid      = $refId;
      		   $templateOrder->pageid     = $pageId;
      		   $templateOrder->imageid    = $page->image->bid;
      		   $templateOrder->x          = $page->image->x;
      		   $templateOrder->y          = $page->image->y;
      		   $templateOrder->dx         = $page->image->dx;
      		   $templateOrder->dy         = $page->image->dy;
      		   $templateOrder->rotate     = isset( $page->image->rotate ) ? $page->image->rotate : 0;
      		   $templateOrder->editor_x   = $page->editor_x;
      		   $templateOrder->editor_y   = $page->editor_y;
      		   
      		   
      		   
      		   // Save page data to db
      		   $templateOrder->save();
      		   
      		   // Setup text data
      		   if( count( $texts ) > 0 ) {
      		      
      		      usort( $texts, 'zIndexSort' );
      		      
      		      foreach( $texts as $zindex => $textdata ) {
      		         
      		         $text = new GiftOrderText();
      		         
      		         $text->id = $templateOrder->id;
      		         $text->text = base64_decode( $textdata->text ); //str_replace( "XXNYLINJEXX","\n",  );
      		         $text->color = $textdata->color;
      		         $text->font = $textdata->font;
      		         $text->gravity = $textdata->gravity;
      		         /*
      		         $text->x = round( ( $textdata->x * $scale_x ) );
      		         $text->y = round( ( $textdata->y * $scale_y ) );
      		         $text->dx = round( $textdata->dx * $scale_x );
      		         $text->dy = round( $textdata->dy * $scale_y );
      		         */
      		         $text->x  = $textdata->x; //  + $giftpagetemplate->edit_mal_x 
            		   $text->y  = $textdata->y; //  + $giftpagetemplate->edit_mal_x 
            		   $text->dx = $textdata->dx;
            		   $text->dy = $textdata->dy;

      		         $text->zindex = $zindex;
      		         $text->pageid = $pageId;
      		         
      		         // Save the text to db
      		         $text->save();
      		         
      		      }
      		      
      		   }

      		   // Setup cliparts
      		   if( count( $cliparts ) > 0 ) {
      		      
      		      usort( $cliparts, 'zIndexSort' );
      		      
      		      foreach( $cliparts as $zindex => $clipartdata ) {
      		         
      		         $clipart = new GiftOrderClipart();
      		         
      		         $clipart->id = $templateOrder->id;
      		         $clipart->clipid = $clipartdata->id;
      		         /*
      		         $clipart->x = round( ( $clipartdata->x * $scale_x ) );
      		         $clipart->y = round( ( $clipartdata->y * $scale_y ) );
      		         $clipart->dx = round( $clipartdata->dx * $scale_x );
      		         $clipart->dy = round( $clipartdata->dy * $scale_y );
      		         */
      		         $clipart->x  = $clipartdata->x; //  + $giftpagetemplate->edit_mal_x 
            		   $clipart->y  = $clipartdata->y; //  + $giftpagetemplate->edit_mal_y 
            		   $clipart->dx = $clipartdata->dx;
            		   $clipart->dy = $clipartdata->dy;
                     
            		   $clipart->zindex = $zindex;
      		         $clipart->page = $pageId;
      		         
      		         // Save clipart to db
      		         $clipart->save();
                     
      		      }
      		      
      		   }
      		   
      		   // set red-eye
      		   $redeye = $page->redeye;
      		   
		      }
		      
		      if( $templateOrder->id > 0 ) {
		      
   		      $cart = new Cart();
   		      $cart->addItemByProductOptionId( $lastProductOptionid, 
   		          $quantity, 
   		          array( 
   		             "templateorderid" => $templateOrder->id,
   		             "redeyeremoval" => $redeye ? true : false,
   		          )
   		      );
   		      
               $cart->save();
		      
		       //echo "OK";
		       echo json_encode("OK");
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