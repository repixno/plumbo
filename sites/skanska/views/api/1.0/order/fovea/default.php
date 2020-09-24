<?php
   /**
    * 
    * Receive orders from fovea
    * @author Tor Inge Lovland <tor.inge@eurofoto.no>
    * 
    */
   import( 'pages.json' );
   model( 'production.fovea' );
   
   class APIOrderFovea extends JSONPage implements NoAuthRequired,IView {
      
      //private $secureid = '6d84b982490ccb2551ee76d6762757f451949b542c50e0';
      
      private $secureid = 'hW5jk1WnO4L3l07nhH0Nghf7bvB0NAlNnEoNNXshyRU8BZ1YSnjn6dPn0jswIhk';
      
      
      private $path = "/data/pd/fovea/";
      
      /**
       * Receive orders from the fovea
       * 
       * @api-name order.fovea
       * @api-auth not required
       * @api-post-required securecode Secure code for auth
       * @api-post-required orderid Order id from Fovea
       * @api-file-required xml Xml file from fovea
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
      */       
      
      public function Execute() {
         
         $securecode = isset( $_POST['securecode'] ) ? $_POST['securecode'] : null;
         $orderid = isset( $_POST['orderid'] ) ? $_POST['orderid'] : '';
         $xmlstring = isset( $_POST['xmlstring'] ) ? $_POST['xmlstring'] : '';
         $xml = isset(  $_FILES['xml'] ) ?  $_FILES['xml'] : null;
         
         $this->result = false;
         $ipadress =  $_SERVER['REMOTE_ADDR'];
         
         if( $securecode == $this->secureid ){
            
            if( DB::query( "SELECT id FROM production_fovea WHERE foveaid = ?", $orderid )->fetchSingle() ){
               $this->message = "Order Exists";
               return false;
            }
            
            if( !empty( $xmlstring) ){
               try{
                  
                  //$xmlfile = file_get_contents( $xml['tmp_name'] );
                  //$xmlcontent = new SimpleXMLElement( file_get_contents( $xml['tmp_name'] ) );
                  
                  //$json = json_encode( $xmlcontent );
                  //$array = json_decode( $json, TRUE );
                  $xmlstring = urldecode(  $xmlstring );
                  $xml = new SimpleXMLElement( "<?xml version='1.0' standalone='yes'?>\n" . $xmlstring );
                  //file_put_contents( "/home/toringe/foveatest/" . $orderid . ".xml" ,  $xmlfile );
                  $save_path =  $this->path . date( 'Y-m-d' ) . "/" . $orderid . "/";
                  
                  if( !file_exists( $save_path ) ){
                     mkdir( $save_path, 0755, true );
                  }

                  file_put_contents( $save_path . $orderid . ".txt" ,  $xmlstring );
                  //file_put_contents(  $save_path . "ip_" .  $orderid . ".xml" ,  $ipadress );
                  
                  $shippingAddress = $xml->shippingAddress;
                  $shippingMethod = $xml->shippingMethod;
                  $ordertext = "ORDRE $orderid \n";
                  
                  $ordertext .= sprintf( "Navn: %s\n", $shippingAddress->name );
                  $ordertext .= sprintf( "%s\n", $shippingAddress->addressLine1 );
                  if( !empty( $shippingAddress->addressLine2) ){
                     $ordertext .= sprintf( "%s\n", $shippingAddress->addressLine2 );
                  }
                  $ordertext .= sprintf( "%s %s\n\n", $shippingAddress->zipCode, $shippingAddress->area );
                  
                  $ordertext .= sprintf( "Leverignsmetode %s\n", $shippingMethod );
                  
                  $products = $xml->products->product;
                  $ordertext .= "Ordrelinjer\n";
                  
                  foreach( $products as $product ){
                     
                     $filename = sprintf( "%s-%s-%s-%s.jpg" , $orderid, $product->articleId, $product->designId, $product->quantity );
                     $ordertext .= sprintf( "Articleid: %s, Desingid: %s, quantity: %s, imagename: %s\n" , $product->articleId, $product->designId, $product->quantity, $filename );
                     
                     try{
                        $content = file_get_contents( $product->imageUrl );
                        file_put_contents( $save_path . $filename , $content  );
                     }catch( Exception $e ){
                        mail( 'tor.inge@eurofoto.no', 'Fovea hente bilde bug', print_r( $e->getMessage() , true ) );
                     }
                  
                  }
                  file_put_contents( $save_path . "ordretext.txt", $ordertext );
                  try{
                     $newFoveaImport = new DBFovea();
                     $newFoveaImport->foveaid = $orderid;
                     $newFoveaImport->downloaded = date( 'Y-m-d H:i:s' );
                     $newFoveaImport->save();
                  }catch( Exception $e ){
                     mail( 'tor.inge@eurofoto.no', 'Fovea bug', print_r( $e->getMessage() , true ) );
                     $this->message = $e->getMessage();
                     return false;
                  }
                  
               }
               catch( Exception $e ){
                  $this->message = $e->getMessage();
                  return false;
               }
            }else{
               $this->message = "Missing xml";
               return false;
            }
   
            $this->result = true;
            $this->message = 'OK';
            return true;
         }else{
            $this->message = "Not authorized";
            return false;
         }
         
      }
      
   }

   


?>
