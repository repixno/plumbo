<?php
   
   import( 'website.product' );
   import( 'website.gifttemplate' );
   import( 'website.uploadhelper' );
   import( 'website.album' );
   import( 'website.image' );
   model( 'order.leverpostei' );
   
   class StabburetEditor extends WebPage implements IView {
      
      
      protected $template = "stabburetold.preindex";
      protected $datestr="2014-08-04 22:00:00";
      
      public function Execute(){
         
         if( $this->datecheck() ){
            $this->template = "stabburetold.bekreft";
         }
         //Convert to date
         /*$date=strtotime($this->datestr);//Converted to a PHP date (a second count)
         //Calculate difference
         $diff=$date-time();//time returns current time in seconds
         if( $diff < 0 ) {
            $this->totalleft = "00:00:00:00";
         }
         else{
            $days=floor($diff/(60*60*24));//seconds/minute*minutes/hour*hours/day);
            $hours=floor(($diff-$days*60*60*24)/(60*60));
            $minutes=floor( ( $diff-$days*60*60*24-$hours*60*60 ) / ( 60) );
            $seconds=round( ( $diff-$days*60*60*24-$hours*60*60-$minutes*60 ));
            $days = sprintf( "%02d",$days );
            $hours = sprintf( "%02d",$hours );
            $minutes = sprintf( "%02d",$minutes );
            $seconds = sprintf( "%02d",$seconds );
            $this->totalleft = "$days:$hours:$minutes:$seconds";
            
         }
         if( time() > $date ){
            $this->template = "stabburet.index";
         } */
      }
      
      public function Bekreft(){
         if( $this->datecheck() ){
            $this->template = "stabburetold.bekreft";
         }
      }
      
      public function Editor( $startover = null ){

         if( $this->datecheck() ){         
            if( !$_POST  && $startover != 'startover'){
               relocate( '/bestill/bekreft' );
            }else{
               Session::set( 'purchaseinfo' , sprintf( "[%s,%s,%s]", $_POST['month'], $_POST['kommune'], $_POST['store'] ) );
            }
            $this->template = "stabburetold.editor";
         }
            
      }
      
      
      public function EditorBeta(){
         $this->template = "stabburetold.mobileditor";
      }
      
      public function Thumb( $id ){
         
         $folder = '/data/pd/stabburet/lokk/share/';
         $this->template = null;
         $img = new Imagick();
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         $filecontent = base64_decode( $filecontent );
         header("Content-type: application/octet-stream ");
         echo  $filecontent;
         
      }
      
      
      public function Download( $id ){
         
         $folder = '/data/pd/stabburet/lokk/share/';
         $this->template = null;
         $img = new Imagick();
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         $filecontent = base64_decode( $filecontent );
         header("Content-type: image/jpeg");
         echo  $filecontent;
         
      }
      
      
      public function ThumbShare( $id ){
         
         $folder = '/data/pd/stabburet/lokk/share/';
         $this->template = null;
         
         $template = new Imagick();
         $template->newImage(840, 439, new ImagickPixel(none));
         $template->setImageFormat('jpeg');
         
         $img = new Imagick('/var/www/repix/sites/website/webroot/stabburetstatic/lokkbg.png');
         
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         //$filecontent = base64_decode( $filecontent );         
         $imageBlob = base64_decode($filecontent);
         
         $thumb = new Imagick();
         $thumb->readImageBlob($imageBlob);
         
         $template->compositeImage($thumb, $thumb->getImageCompose(), 200, 0);
         $template->compositeImage($img, $img->getImageCompose(), 0, 0);
         
         header("Content-type: image/jpeg");
         echo  $template;
         
      }
      
      
      public function Thumbface( $id ){
         
         $folder = '/data/pd/stabburet/lokk/';
         $date = DB::query( "SELECT created FROM leverpostei_order WHERE thumbid ilike ?" , $id )->fetchSingle();
         $date = date( 'Y-m-d' , strtotime( $date ) );
         $folder .= $date . '/';
         $this->template = null;
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         
         $template = new Imagick();
         $template->newImage(840, 439, new ImagickPixel(none));
         $template->setImageFormat('jpeg');
         
         $img = new Imagick('/var/www/repix/sites/website/webroot/stabburetstatic/lokkbg.png');
         
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         
         //$filecontent = base64_decode( $filecontent );         
         $imageBlob = base64_decode($filecontent);
         
         $thumb = new Imagick();
         $thumb->readImageBlob($imageBlob);
         $thumb->thumbnailImage( 439, 439 , false );
         
         $template->compositeImage($thumb, $thumb->getImageCompose(), 200, 0);
         $template->compositeImage($img, $img->getImageCompose(), 0, 0);
         
         header("Content-type: image/jpeg");
         echo  $template;
         
      }
      
      public function Thumbint( $id ){
         
         $folder = '/data/pd/stabburet/lokk/';
         $date = DB::query( "SELECT created FROM leverpostei_order WHERE thumbid ilike ?" , $id )->fetchSingle();
         $date = date( 'Y-m-d' , strtotime( $date ) );
         $folder .= $date . '/';
         $this->template = null;
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
        
         $image = new Imagick();
         $image->newImage(190, 190, new ImagickPixel(none));
         $image->setImageFormat('png');
         
         $img = new Imagick();
         
         $filecontent = base64_decode( $filecontent );
         $img->readimageblob($filecontent);
         
         $foreground = new Imagick( '/var/www/repix/sites/website/webroot/stabburetstatic/mal/thumbmask.png' );
         $img->thumbnailImage( 190, 190 , false );
         $image->compositeImage( $img, $img->getImageCompose(), 0, 0 ); 
         $image->compositeImage( $foreground, $foreground->getImageCompose(), 0, 0 ); 
         
         header('Content-type: image/png');
         echo $image;
         //echo $filecontent;
         
      }
      
      public function Thumbmob( $id ){
         
         $folder = '/data/pd/stabburet/lokk/';
         $date = DB::query( "SELECT created FROM leverpostei_order WHERE thumbid ilike ?" , $id )->fetchSingle();
         $date = date( 'Y-m-d' , strtotime( $date ) );
         $folder .= $date . '/';
         $this->template = null;
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
        
         $image = new Imagick();
         $image->newImage(220, 220, new ImagickPixel(none));
         $image->setImageFormat('png');
         
         $img = new Imagick();
         
         $filecontent = base64_decode( $filecontent );
         $img->readimageblob($filecontent);
         
         $foreground = new Imagick( '/var/www/repix/sites/website/webroot/stabburetstatic/mal/thumbmaskmobil.png' );
         $img->thumbnailImage( 192, 192 , false );
         $image->compositeImage( $img, $img->getImageCompose(), 9, 9 ); 
         $image->compositeImage( $foreground, $foreground->getImageCompose(), 0, 0 ); 
         
         header('Content-type: image/png');
         echo $image;
         //echo $filecontent;
      }
      
      public function StreamThumb( $id ){
         
         $folder = '/data/pd/stabburet/lokk/';
         $date = DB::query( "SELECT created FROM leverpostei_order WHERE thumbid ilike ?" , $id )->fetchSingle();
         $date = date( 'Y-m-d' , strtotime( $date ) );
         $folder .= $date . '/';
         $this->template = null;
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         //$filecontent = base64_decode( $filecontent );
         $foreground = new Imagick( '/var/www/repix/sites/website/webroot/stabburetstatic/mal/Leverpostei_bestill.png' );
         $image = new Imagick();
         $image->newImage(300, 230, new ImagickPixel('white'));
         $image->setImageFormat('jpeg');
         
         $img = new Imagick();
         $filecontent = base64_decode( $filecontent );
         $img->readimageblob($filecontent);
         $img->thumbnailImage( 185, 190 , false );
         
         $controlPoints = array( 0, 0, 20, 20,

                        0, $img->getImageHeight() + 20,
                        0, $img->getImageHeight() + 20,

                        $img->getImageWidth(), 0,
                        $img->getImageWidth(), 0,

                        $img->getImageWidth() , $img->getImageHeight() ,
                        $img->getImageWidth(), $img->getImageHeight() );
         
         
         //$img->setimagebackgroundcolor( new ImagickPixel('white') );
         //$img->setImageVirtualPixelMethod( imagick::VIRTUALPIXELMETHOD_BACKGROUND );
         $img->distortImage( Imagick::DISTORTION_PERSPECTIVE, $controlPoints, TRUE );
         
         $image->compositeImage( $img, $img->getImageCompose(), 13, 15 ); 
         $image->compositeImage( $foreground, $foreground->getImageCompose(), 0, 0 ); 
         
         header('Content-type: image/jpeg');
         echo $image;
         //echo $filecontent;
         
      }
      
      
      public function Thumbnail($id){
         
         $folder = '/data/pd/stabburet/lokk/';
         $date = DB::query( "SELECT created FROM leverpostei_order WHERE thumbid ilike ?" , $id )->fetchSingle();
         $date = date( 'Y-m-d' , strtotime( $date ) );
         $folder .= $date . '/';
         $this->template = null;
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         $filecontent = str_replace( 'data:image/png;base64,', '' , $filecontent );
         $filecontent = str_replace( 'data:image/jpeg;base64,', '' , $filecontent );
         $filecontent = base64_decode( $filecontent );
         header('Content-type: image/jpeg');
         echo  $filecontent;
      }
      
      
      public function Share( $id ){
         $folder = '/data/pd/stabburet/lokk/';
         $date = DB::query( "SELECT created FROM leverpostei_order WHERE thumbid ilike ?" , $id )->fetchSingle();
         $date = date( 'Y-m-d' , strtotime( $date ) );
         $folder .= $date . '/';
         $this->template = 'stabburet.share';
         $img = new Imagick();
         $filecontent = file_get_contents( $folder . $id . '.txt' );
         $this->imagefile = $filecontent;
         
      }
      
      public function facebook( $id  = 0){
         
         $this->template = "stabburet.facebook2";
         $this->id = $id;
      }
      
      public function ShareFacebook( $id  = 0){
         $this->template = "stabburet.facebook";
         $this->id = $id;
      }
      
      
      public function Order( $id = 0 ){
         $this->template = 'stabburet.order';
         
         $lokkid =  Session::get( 'stabburet-lokkid' );
         $leverpostei = new DBLeverpostei( $lokkid );
         $this->lokk  = array(
               'id' => $leverpostei->id,
               'thumbid' => $leverpostei->thumbid,
               'imageid' => $leverpostei->imageid,
               'imagepos' => $leverpostei->imagepos,
               'name' => $leverpostei->name,
               'year' => $leverpostei->year,
               'malsize' => $leverpostei->malsize,
         );
               
         $productarray_mobile = array( 3320, 3318 ,3322, 3326 );

         $product_mobile = array();
         foreach ( $productarray_mobile as $ret ){
            $productcontainer = new Product( $ret );             
            $product_mobile[] = $productcontainer->asArray();
         }
         $this->sections = array(
            'products_mobile' => $product_mobile
         );
         
         if( Login::isLoggedIn() ) {
            $user = new User( Login::userid() );
         } else {
             $user = new User();
         }   
         $this->user = $user->asArray();
         
      }
      
      public function Checkout(){
         
         $this->template = 'stabburet.checkout';
         $cart =  new Cart();
         
         
      }
      

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
         import( 'website.cart' );
         
         $templateOrder = new GiftOrderTemplate();
	 $editordata = (string)$_POST["pages"];	   
	 $redeye = false;

	 $editordata = urldecode( stripslashes( $editordata ) );
	 $editorObject = json_decode( $editordata );
         
	 $lastProductOptionid = 0;
         $cart = new Cart();
	 // Setup page per page of editor data
         
         
         
         
         
	 if( count( $editorObject ) > 0 ) {
            foreach( $editorObject as $pageId => $page ) {
               
               $cartarray = $cart->asArray();
               if(   $cartarray['items']['gifts'][$page->prodno] && $page->prodno != 7307 ){
                  foreach( $cartarray['items']['gifts'][$page->prodno]  as $key=>$res ){
                     if( $page->giftQuantity == 0 ){
                        $cart->removeItem( $page->prodno, $key );
                        $cart->save();
                     }
                     else if(  $page->giftQuantity > 0  ){
                        $cart->setItemQuantity( $page->prodno,  $page->giftQuantity, $key );
                        $cart->save();
                     }
                  }
                  
                     
                  echo json_encode("OK");
                  die();
               }
               else if( $cartarray['items']['gifts'][$page->prodno]  ){
                  
                  foreach( $cartarray['items']['gifts'][$page->prodno]  as $key=>$res ){
                     
                        if( $page->productoptionid == $res['optionid'] && $page->giftQuantity == 0 ){
                           $cart->removeItem( $page->prodno, $key );
                           $cart->save();
                        }
                        else if( $page->productoptionid == $res['optionid'] && $page->giftQuantity > 0  ){
                           $cart->setItemQuantity( $page->prodno,  $page->giftQuantity, $key );
                           $cart->save();
                        }else if( $page->giftQuantity > 0 ){
                           //Util::Debug( $page  );
                              $lokkinfo = new DBLeverpostei( $page->lokkid );
                              //$pages = new DB::query("SELECT * FROM leverpostei_order where id = ?",  $page->lokkid)->fetchAll( DB::FETCH_ASSOC );
                              //Util::Debug( $lokkinfo );
                              
                                 $quantity = $page->giftQuantity;
                                 if( $quantity == 0 ) $quantity = 1;
                                 $productOptionId = $page->productoptionid;
                                 $productId = $page->productid;		   
                                 $lastProductOptionid = $productOptionId;
                                  
                                 // Add gift to cart
                                 $productOption = new ProductOption( $productOptionId );
                                 $refId = $productOption->refid;
                                 $images = $page->image;
                                  
                                 //load the gift template
                                 //$giftpagetemplate = new GiftPageTemplate( $page->malpageid );
                                 //Set page/template data
                                 $imgpos = str_replace(  array( '"[', ']"' ) ,'', $lokkinfo->imagepos );
                                 $coordinates = explode( ',', $imgpos );
                                 
                                 $templateOrder->userid     = Login::userid();
                                 $templateOrder->templateid = $page->malid;
                                 $templateOrder->refid      = $page->prodno;
                                 $templateOrder->pageid     = $pageId;
                                 $templateOrder->imageid    = $lokkinfo->imageid;
                                 $templateOrder->x          = $coordinates[0];
                                 $templateOrder->y          = $coordinates[1];
                                 $templateOrder->dx         = $coordinates[2];
                                 $templateOrder->dy         = $coordinates[3];
                                 $templateOrder->rotate     = $coordinates[4];
                                 $templateOrder->editor_x   = $lokkinfo->malsize;
                                 $templateOrder->editor_y   = $lokkinfo->malsize;
                                 $templateOrder->printtype   = "stabburet_" . $lokkinfo->id;
                                 $templateOrder->text       =  $lokkinfo->name . '-|-' . $lokkinfo->year;
                                  // Save page data to db
                                  $templateOrder->save();
                                  // set red-eye
                                  $redeye = $page->redeye;
                                  
                                 if( $templateOrder->id > 0 ) {
                                    //$cart = new Cart();
                                    $cart->addItemByProductOptionId(
                                       $lastProductOptionid, 
                                       $quantity, 
                                            array( 
                                               "templateorderid" => $templateOrder->id,
                                               "redeyeremoval" => false,
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
                     
                     
                  }
                  
                  die();
                  
                  echo json_encode("tskjoret");
                  die();
               }
               else if( $page->giftQuantity > 0 ) {
                  //Util::Debug( $page  );
                  $lokkinfo = new DBLeverpostei( $page->lokkid );
                  //$pages = new DB::query("SELECT * FROM leverpostei_order where id = ?",  $page->lokkid)->fetchAll( DB::FETCH_ASSOC );
                  //Util::Debug( $lokkinfo );
                  
                     $quantity = $page->giftQuantity;
                     if( $quantity == 0 ) $quantity = 1;
                     $productOptionId = $page->productoptionid;
                     $productId = $page->productid;		   
                     $lastProductOptionid = $productOptionId;
                      
                     // Add gift to cart
                     $productOption = new ProductOption( $productOptionId );
                     $refId = $productOption->refid;
                     $images = $page->image;
                      
                     //load the gift template
                     //$giftpagetemplate = new GiftPageTemplate( $page->malpageid );
                     //Set page/template data
                     $imgpos = str_replace(  array( '"[', ']"' ) ,'', $lokkinfo->imagepos );
                     $coordinates = explode( ',', $imgpos );
                     
                     $templateOrder->userid     = Login::userid();
                     $templateOrder->templateid = $page->malid;
                     $templateOrder->refid      = $page->prodno;
                     $templateOrder->pageid     = $pageId;
                     $templateOrder->imageid    = $lokkinfo->imageid;
                     $templateOrder->x          = $coordinates[0];
                     $templateOrder->y          = $coordinates[1];
                     $templateOrder->dx         = $coordinates[2];
                     $templateOrder->dy         = $coordinates[3];
                     $templateOrder->rotate     = $coordinates[4];
                     $templateOrder->editor_x   = $lokkinfo->malsize;
                     $templateOrder->editor_y   = $lokkinfo->malsize;
                     $templateOrder->printtype   = "stabburet_" . $lokkinfo->id;
                     $templateOrder->text       =  $lokkinfo->name . '-|-' . $lokkinfo->year;
                      // Save page data to db
                      $templateOrder->save();
                      // set red-eye
                      $redeye = $page->redeye;
                      
                     if( $templateOrder->id > 0 ) {
                        //$cart = new Cart();
                        $cart->addItemByProductOptionId(
                           $lastProductOptionid, 
                           $quantity, 
                                array( 
                                   "templateorderid" => $templateOrder->id,
                                   "redeyeremoval" => false,
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
                
               }
            }  
		   
	    echo "FAILED";
	    die();
		   
	 }
         
         
         public function datecheck(){

            $date=strtotime($this->datestr);//Converted to a PHP date (a second count)
            //Calculate difference
            $diff=$date-time();//time returns current time in seconds
            if( $diff < 0 ) {
               return true;
            }else{
               relocate( '/' );
               die();
            }
            
         }
         
         
         public function test(){
            die();
            import( 'website.uploadedimagesarray' );
            
            
            $this->template = null;
            
            $cart = new Cart();
            $cartarray = $cart->asArray();
            Util::Debug( $cartarray['items']['gifts'] );
            
            
            /*
            Util::Debug( UploadedImagesArray::get() );
            */
         }
   }