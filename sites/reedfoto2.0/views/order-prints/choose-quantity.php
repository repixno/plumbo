<?PHP

   /**
    *
    * Get data about all images available for purchase
    * made so by the user.
    *
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'website.image' );
   import( 'session.usersessionarray' );
   import( 'website.product' );
   import( 'website.uploadedimagesarray' );
   import( 'website.cart' );

   class OrderprintsChooseQuantity extends UserPage implements IView {

      protected $template = 'order-prints.choose-quantity';

      public function Execute( $view = '', $edit = null ) {

         if( isset( $edit ) && $edit != 'edit' ) $edit = null;

         if( !empty( $view ) && $view == 'each-print' && $_POST ) {

            $this->template = 'order-prints.change-each-print';
            //util::Debug( $_POST );
            //die();

         }

         if( !empty( $_POST ) && is_array( $_POST ) && $view != 'each-print') {
            UserSessionArray::clearItems( 'choosenimages' );
            $albums = $_POST["albums"];
            $singleImages = $_POST["images"];

            //$images = UserSessionArray::getItems( "choosenimages" );
            $images = $images[0];

            if( !is_array( $images ) ) {
               $images = array();
            }

            $images = $this->updateChoosenImages( $albums,  $singleImages, $images );
            UserSessionArray::clearItems( "choosenimages" );
            UserSessionArray::addItem( "choosenimages", $images );

         }


         // Get the images available for purchase here
         $choosenImages = UserSessionArray::getItem( "choosenimages", 0 );
         
         if( !is_array( $choosenImages ) ) $choosenImages = array();
         

         // New upload
         if(count( $choosenImages ) == 0){
            $uploadedImages = $_SESSION["client_info"]["upload_order"]["this_session"];
         }
         unset($_SESSION["client_info"]["upload_order"]["this_session"]);
         // Old upload without login
         // Put old and new upload together in same array
         if( UploadedImagesArray::count() ) {
            
            $tmpImages = UploadedImagesArray::getOriginal();

            foreach( $tmpImages as $okey => $ovalue ) {
               $uploadedImages[$okey] = 1;
            }

         }

         if( count( $uploadedImages ) ) {
            
            foreach( $uploadedImages as $okey => $ovalue ) {
                  $uploadedImages[$okey] = 1;
            }
         }

         // Get number of images
         $numUploadedImages = (int)count( $uploadedImages );
         $numChoosenImages = (int)count( $choosenImages );

         // Get all print products
         $res = DB::query( "SELECT prodno, tags FROM site_product_option WHERE tags LIKE( '%print%' ) OR tags LIKE( '%enlargement%' )" );

         $prodnos = array();
         $qualityProdnos = array();
         while( list( $prodno, $tags ) = $res->fetchRow() ) {

            if ( strpos( $tags, 'print' ) !== false ) {
            
               $prodnos[$prodno] = 0;
               $qualityProdnos[$prodno] = 0;
               
            }
            
            if ( strpos( $tags, 'enlargement' ) !== false ) {
            
               $qualityProdnos[$prodno] = 0;
               
            }

         }

         $imageObjects = array();
         $qualityCountMatrix = null;

         if( $edit != 'edit' ) {
            
           // Parse the uploaded images
            if( $numUploadedImages > 0 ) {
               
               $choosenSize = $_POST;

               $keys = array_keys( $uploadedImages );
               
               if( count( $keys ) > 0 )
               foreach( $keys as $imageid ) {

                  try {
                     $image = new Image( $imageid );

                     $prodQualities = array();
                     foreach ( $qualityProdnos as $prodno=>$v ) {

                        $quality = $image->qualityRating( $prodno );
                        $prodQualities[] = array( 'prodno' => $prodno, 'quality' => $quality );
                        $qualityCountMatrix = $image->populateCountMatrix( $qualityCountMatrix, $quality, $prodno );

                     }
                     
                     $imageObjects[$imageid] = $image->asArray();
                     
                     $default_info = $this->default_artnr($image->y, $image->x);

                     $imageObjects[$imageid]['qualities'] = $prodQualities;
                     $imageObjects[$imageid]['artnr'] = sprintf('%04d' , $default_info['default_artnr'] );
                     $imageObjects[$imageid]['std_quantity'] = $choosenSize['0001'];
                     $imageObjects[$imageid]['articletitle'] = $default_info['title'];


                     
                     if( !isset( $imageObjects[$imageid]["quantity"] ) ) {
                        $imageObjects[$imageid]["quantity"] = $prodnos;
                     }
                  } catch ( Exception $e ) {}

               }

            }

            // parse the choosen images
            if( $numChoosenImages > 0 ) {
               
               $choosenSize = $_POST;

               if( count( $choosenImages ) > 0 )
               foreach( $choosenImages as $imageId => $imageData ) {

                  $prodQualities = array();
                  try {
                     $image = new Image( $imageId );

                     $prodQualities = array();
                     foreach ( $qualityProdnos as $prodno=>$v ) {

                        $quality = $image->qualityRating( $prodno );
                        $prodQualities[] = array( 'prodno' => $prodno, 'quality' => $quality );
                        $qualityCountMatrix = $image->populateCountMatrix( $qualityCountMatrix, $quality, $prodno );

                     }
                  } catch ( Exception $e ) {}
                  
                  
                  if($choosenSize['std_choice'] == 'auto' ){
                     $default_info = $this->default_artnr($image->y, $image->x);
                  }
                  else{
                     $default_info['default_artnr'] = $choosenSize['std_choice'] ;
                  }                  

                  $imageObjects[$imageId] = $imageData;
                  $imageObjects[$imageId]['qualities'] = $prodQualities;
                  $imageObjects[$imageId]['artnr'] = sprintf('%04d' ,$default_info['default_artnr']);
                  $imageObjects[$imageId]['std_quantity'] = $choosenSize['0001'];
                  $imageObjects[$imageId]['articletitle'] = $default_info['title'];
                                          
                  if($image->x < $image->y ){
                     $imageObjects[$imageId]['orientation'] = 'portrait';
                  }
                  else{
                     $imageObjects[$imageId]['orientation'] = 'landscape';
                  }
                  
                  $imageObjects[$imageId]['ratio'] = round( $image->x / $image->y, 4 );
                  
      


                  //$imageObjects[$imageId]["quantity"] = 1;
                  if( !isset( $imageObjects[$imageId]["quantity"] ) ) {
                     $imageObjects[$imageId]["quantity"] = array();
                  }

               }

            }

         } else {

            $this->setTemplate( 'order-prints.edit-order' );

            $changeimages = array();
            $cart = new Cart();
            $cartitems = $cart->enum();

            // Regular prints
            $printsfromcart = array();
            $printsfromcart = $cartitems['items']['prints'];

            if( count( $printsfromcart ) > 0 )
            foreach( $printsfromcart as $prodno => $printproduct ) {

               $changeimages = $printproduct['images'];

               if( count( $changeimages ) > 0 )
               foreach( $changeimages as $imageid => $quantity ) {

                  $image = null;
                  if( !isset( $imageObjects[$imageid] ) ) {

                     try {

                        $image = new Image( $imageid );
                        $prodQualities = array();
                        foreach ( $qualityProdnos as $pno=>$v ) {
   
                           $quality = $image->qualityRating( $pno );
                           $prodQualities[] = array( 'prodno' => $pno, 'quality' => $quality );
                           $qualityCountMatrix = $image->populateCountMatrix( $qualityCountMatrix, $quality, $pno );
   
                        }

                        $imageObjects[$imageid] = $image->asArray();
                        $imageObjects[$imageid]['edit'] []= array(
                           'prodno' => $prodno,
                           'optionid' => $printproduct['optionid'],
                           'quantity' => $quantity,
                           'product' => $printproduct['product'],
                           'refid' => $printproduct['refid'],
                           'qualities' => $prodQualities
                        );
                     } catch( Exception $e ) {}

                  } else {

                     $quality = null;
                     try {

                        $image = new Image( $imageid );
                        $prodQualities = array();
                        foreach ( $qualityProdnos as $pno=>$v ) {
   
                           $quality = $image->qualityRating( $pno );
                           $prodQualities[] = array( 'prodno' => $pno, 'quality' => $quality );
                           $qualityCountMatrix = $image->populateCountMatrix( $qualityCountMatrix, $quality, $pno );
   
                        }

                     } catch( Exception $e ) {}

                     $imageObjects[$imageid]['edit'] []= array(
                        'prodno' => $prodno,
                        'optionid' => $printproduct['optionid'],
                        'quantity' => $quantity,
                        'product' => $printproduct['product'],
                        'refid' => $printproduct['refid'],
                        'qualities' => $prodQualities
                     );

                  }

               }

            }

            // Enlargements
            $changeimages = array();
            $printsfromcart = array();
            $printsfromcart = $cartitems['items']['enlargements'];
            if( count( $printsfromcart ) > 0 )
            foreach( $printsfromcart as $prodno => $printproduct ) {

               $changeimages = $printproduct['images'];
               if( count( $changeimages ) > 0 )
               foreach( $changeimages as $imageid => $quantity ) {

                  $image = null;
                  if( !isset( $imageObjects[$imageid] ) ) {

                     try {

                        $image = new Image( $imageid );
                        $prodQualities = array();
                        foreach ( $qualityProdnos as $pno=>$v ) {
   
                           $quality = $image->qualityRating( $pno );
                           $prodQualities[] = array( 'prodno' => $pno, 'quality' => $quality );
                           $qualityCountMatrix = $image->populateCountMatrix( $qualityCountMatrix, $quality, $pno );
   
                        }

                        $imageObjects[$imageid] = $image->asArray();
                        $imageObjects[$imageid]['edit'] []= array(
                           'prodno' => $prodno,
                           'optionid' => $printproduct['optionid'],
                           'quantity' => $quantity,
                           'product' => $printproduct['product'],
                           'refid' => $printproduct['refid'],
                           'qualities' => $prodQualities
                        );
                     } catch( Exception $e ) {}

                  } else {

                     $quality = null;
                     try {

                        $image = new Image( $imageid );
                        $prodQualities = array();
                        foreach ( $qualityProdnos as $pno=>$v ) {
   
                           $quality = $image->qualityRating( $pno );
                           $prodQualities[] = array( 'prodno' => $pno, 'quality' => $quality );
                           $qualityCountMatrix = $image->populateCountMatrix( $qualityCountMatrix, $quality, $pno );
   
                        }

                     } catch( Exception $e ) {}

                     $imageObjects[$imageid]['edit'] []= array(
                           'prodno' => $prodno,
                           'optionid' => $printproduct['optionid'],
                           'quantity' => $quantity,
                           'product' => $printproduct['product'],
                           'refid' => $printproduct['refid'],
                           'qualities' => $prodQualities
                     );

                  }

               }

            }

         }

         if( $edit != 'edit' ) {
            $order = array(
               "numimages"    => ( $numChoosenImages + $numUploadedImages ),
               "imageobjects" => $imageObjects,
               'correctionmethod' => $_POST['correctionmethod'],
               'paperquality' => $_POST['paperquality'],
               'productionmethod' => $_POST['productionmethod'],
            );
         } else {
            $tmpcart = $cart->asArray();
            $order = array(
               "numimages"    => ( count( $imageObjects ) ),
               "imageobjects" => $imageObjects,
               'paperquality' => $tmpcart['items']['paperquality'],
               'correctionmethod' => $tmpcart['items']['correctionmethod'],
               'productionmethod' => $tmpcart['items']['productionmethod'],
            );
            $this->editorder = true;
         }
         
         $this->order = $order;
         
         list( $pqbad, $pqmedium, $pqok, $pqgood ) = $qualityCountMatrix[ $this->defaultPrintProduct( $order ) ];
         $this->qualitycountmatrix = array(
            'bad' => $pqbad,
            'medium' => $pqmedium,
            'ok' => $pqok,
            'good' => $pqgood,
         );
         
         UserSessionArray::clearItems( "printorder" );
         UserSessionArray::addItem( "printorder", $order );


         // Setup all product data for template. Use product tagged as print
         $res = DB::query( "
            SELECT po.prodno
            FROM site_product_option AS po LEFT JOIN site_textentity AS te ON po.id = te.id
            WHERE ( po.tags NOT LIKE( '%notforsale%' ) ) AND 
            ( po.tags LIKE( '%print%' ) OR
               ( po.tags LIKE( '%enlargement%' ) AND te.deleted IS NULL ) OR
               ( po.tags LIKE( '%productionmethod%' ) AND te.deleted IS NULL ) OR
               ( po.tags LIKE( '%correctionmethod%' ) AND te.deleted IS NULL ) OR
               ( po.tags LIKE( '%paperquality%' ) AND deleted IS NULL ) )" );
         
         

         $prints = array();
         $enlargements = array();
         $productionmethods = array();
         $correctionmethod = array();
         $paperquality = array();
         
       

         while( list( $prodno ) = $res->fetchRow() ) {

            $product = ProductOption::fromProdNo( "$prodno" );
            if( $product->isLoaded() && $product instanceof ProductOption ) {

               $tmpproduct = new Product( $product->productid );
               if( $tmpproduct->isLoaded() ) {
                  $producttitle = $tmpproduct->title;
                  $productbody = $tmpproduct->body;
                  $productingress = $tmpproduct->ingress;
               }

               $productarray = $product->asArray();
               
               
               $productarray["title"] = $producttitle;
               $productarray["body"] = $productbody;
               $productarray["ingress"] = $productingress;
               if($_POST["$prodno"]){
                  $productarray['quantity'] = $_POST["$prodno"];
               }
               else{
                  $productarray['quantity'] = 0;
               }

               if( stristr( $product->tags, 'print' ) ) {
                  $prints []= $productarray;
               }
               if( stristr( $product->tags, 'enlargement' ) ) {
                  $enlargements []= $productarray;
               } else if( stristr( $product->tags, 'productionmethod' ) ) {
                  $productionmethods []= $productarray;
               } else if( stristr( $product->tags, 'correctionmethod' ) ) {
                  $correctionmethod []= $productarray;
               } else if( stristr( $product->tags, 'paperquality' ) ) {
                  $paperquality []= $productarray;
               }

            }

         }
         
         
         usort($enlargements, function($a, $b) {
            return $a['title'] - $b['title'];
         });
         
         if( count( $prints ) > 0 ) {
            $this->prints = $prints;
         }
         if( count( $enlargements ) > 0 ) {
            $this->enlargements = $enlargements;
         }

         if( count( $productionmethods ) > 0 ) {
            $this->productionmethods = $productionmethods;
         }

         if( count( $correctionmethod ) > 0 ) {
            $this->correctionmethods = $correctionmethod;
         }

         if( count( $paperquality ) > 0 ) {
            $this->paperquality = $paperquality;
            
         }

         if( count( $order["imageobjects"] ) > 0 ) {
            $this->defaultprodno = $this->defaultPrintProduct( $this->order );
         }

      }



      private function updateChoosenImages( $albums = array(), $singleImages = array(), $oldImages = array() ) {

         if( count( $albums ) ) {

            $images = new Image();
			   foreach( $images->collection( 'bid', array( 'aid' => array( 'IN', $albums), 'deleted_at' => null, 'quarantined_at' => null ) )->fetchAll() as $row ) {

			      try {
   			      list( $imageid ) = $row;
   			      $image = new Image( $imageid );
   			      $oldImages[$image->bid] = $image->asArray();
			      } catch( Exception $e ) {}

			   }

         }

         if( count( $singleImages ) > 0 ) {

            $images = new Image();
            foreach( $images->collection( 'bid', array( 'bid' => array( 'IN', $singleImages ), 'deleted_at' => null, 'quarantined_at' => null ) )->fetchAll() as $row ) {

               try{
                  list( $imageid ) = $row;
			         $image = new Image( $imageid );
			         $oldImages[$image->bid] = $image->asArray();
               } catch( Exception $e ) {}

			   }

         }

         return $oldImages;

      }



      /**
       * Calculate default size from image sizes
       * Reused from old EF 2.5
       *
       * @param array
       * @return string
       */
      private function defaultPrintProduct( $order = array() ) {

         $imagedata = $order["imageobjects"];
      	$tenthirteen = 0;
      	$tenfifteen = 0;

      	/* Check each image for best default artnr 10x13 or 10x15 */
      	if( count( $imagedata ) > 0 ) {

      	   foreach( $imagedata as $id => $data ) {

      	      if( isset( $data['x'] ) && isset( $data['y'] ) ) {

      	         if( $data['x'] > $data['y'] ){
            			$forhold = $data['x'] / $data['y'];
                  }
               	else{
               		$forhold = $data['y'] / $data['x'];
               	}
               	$delta1 = $forhold - 1.3;
               	$delta2 = $forhold - 1.5;
               	if( $delta1 < 0){
               		$delta1 = $delta1 * -1;
               	}
               	if( $delta2 < 0){
               		$delta2 = $delta2 * -1;
               	}
               	if( $delta1 < $delta2){

               	   $tenthirteen++;

               	} else{

               	   $tenfifteen++;

               	}

      	      }

      	   }

      	}


      	if( $tenfifteen > $tenthirteen ) {

      	   return '0001';

      	} else {

      	   return '0419';

      	}

      }
      
      public function default_artnr($x, $y){

         $size = array($x, $y);
         
         $aspect_ratio = max($size) / min($size);
         
         if($aspect_ratio > 1.4){
            $default_artnr = "0001";
         }
         else{
            $default_artnr = "0419";
         }
         
         $product = ProductOption::fromProdNo( "$default_artnr" );
         
         $tmpproduct = new Product( $product->productid );
         
         $returnarray  = array(
               "default_artnr" => $default_artnr,
               "title" => $tmpproduct->title
         );
         
         
         return $returnarray;
         
      }

   }

?>