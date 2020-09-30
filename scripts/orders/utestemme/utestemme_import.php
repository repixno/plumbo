<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   import( 'website.order.manual.utestemme' );
   
   model( 'production.utestemme' );
   
   class OrderImportUtestemmeScript extends Script {
      
      //private $api_token = 'faf709a5-1caf-4233-af34-f96fbd175393';
      private $api_token = 'faf709a5-1caf-4233-af34-f96fbd175393';
	  //private $url = 'https://utestemme-staging.herokuapp.com/api/v1/orders/open?api_token=faf709a5-1caf-4233-af34-f96fbd175393';
	  
	  private $url = array();
	  
      public $tidspunkt = '';
      public $orderfolder = "/home/produksjon/utestemme/";
      
      Public function Main(){
		 
		 try{
		                          //http://www.utestemme.no/api/v1/orders/open?api_token=faf709a5-1caf-4233-af34-f96fbd175393
			$this->url['orders'] = 'http://www.utestemme.no/api/v1/orders/open?api_token=faf709a5-1caf-4233-af34-f96fbd175393';
			$this->url['project_orders'] = 'http://www.utestemme.no/api/v1/project_orders/open?api_token=faf709a5-1caf-4233-af34-f96fbd175393';
			//$this->url['merkelapp'] = 'http://fotobackend.herokuapp.com/api/v1/orders/open?api_token=59a95857-288c-4d88-b4f4-2ba69041131b';
			
			foreach( $this->url as $key=>$url ){
			   
			   $content = file_get_contents(  $url );
			   
			   //Util::Debug( $key );
			   //$content = json_decode( utf8_encode(  $content )  );
			   $content = json_decode( $content  );
			   //mail( 'til@gloppen.net', "test mail zetta", "test mail zettz!" );
			   if( $key == 'project_orders'){
				  //util::Debug($order);
				  $content = $content->project_orders;
			   }
			   foreach ( $content as $order ){
				  
				  $query = DB::query( "SELECT id FROM production_utestemme WHERE utestemmeid = ? AND projecttype = ?" ,  $order->id, $key  )->fetchSingle( );
				  //Util::debug( utf8_decode( $order->shipping_address->street ) );	               
				  //Util::debug( utf8_decode( $order->id ) ); 4523
				  $data = array();
				  if( !$query &&  count($order->pictures) > 0  ){
				   //if( DB::query( $query )->fetchSingle( ) ==  4523 &&  count($order->pictures) > 0  ){             
								 
					 Util::Debug( $order );
								 
					$orderinfo = '';
					$orderinfo .= "Ordrenr: " . $order->id . "\n";
					$orderinfo .= "Navn: " . $order->shipping_address->name . "\n";
					$orderinfo .= "Adresse: " . $order->shipping_address->street . "\n";
					$orderinfo .= "Postnummmer: " . $order->shipping_address->zip . "\n";
					$orderinfo .= "Epost: " . $order->shipping_address->email . "\n";
					$orderinfo .= "Mobilnr: " . $order->shipping_address->mobile_phone_number . "\n";
					$orderinfo .= "Stad: " . $order->shipping_address->city . "\n";
					$orderinfo .= "\nOrdrelinjer:\n\n";
					$ordertime = date( "Y-m-d" ,  strtotime( $order->date ) );
					$orderdatefolder = $this->orderfolder . $ordertime;
		   
					 if( !file_exists( $orderdatefolder ) ){
						mkdir( $orderdatefolder );
					 }
					 $orderdateidfolder = $orderdatefolder . "/" . $order->id;
					 if( !file_exists( $orderdateidfolder ) ){
						mkdir( $orderdateidfolder );
					 }
				  
					 $articles = array();
					   
					 foreach( $order->pictures  as $pictures ){
						
						$pathinfo = pathinfo ( $pictures->url ); 
						$imageid = explode( '/' , $pathinfo['dirname'] );
						$orderinfo .= sprintf( "Produkt: %s,  Antall: %s,  Størrelse: %s, Bildeid: %s  \n\n" , $pictures->product , $pictures->quantity, $pictures->size , end( $imageid ) );
						$productfolder = $orderdateidfolder . "/" . $pictures->type;
						if( !file_exists( $productfolder ) ){
						   mkdir( $productfolder );
						}
						$sizefolder = $productfolder . $pictures->size;
						if( !file_exists( $sizefolder ) ){
						   mkdir( $sizefolder );
						}
						
						if( strpos( $pictures->url , ".pdf" )  > 0 ){
						   $imagename = substr( $pictures->url , strpos( $pictures->url , "?") + 1);						   
						   $img =  $sizefolder . '/' .  $imagename . '.pdf';
						}
						else{
						   $img =  $sizefolder . '/' . end( $imageid ) . '.jpg';
						}
						
						file_put_contents($img, file_get_contents( $pictures->url ) );
						
						if( strpos( $pictures->comment , 'Fit-in' )  > 0 ){
						   $fitin = 1;
						}
						else{
						   $fitin = 0;
						}
				
						$prodno = $pictures->product;
					 
					 
					 $numero = $pictures->quantity;
					$fotocardfix = "{$numero}0";
					 
					  Util::Debug( $str );
					  
					
						if( $prodno == 7204 ){
						  $prodno = 7117;
						}
						
						
						
						
						// Legger til 0 i enden på antall om det er fotokort pakke
						
					if ($prodno == 7237) {
						$prodno = 522;
					$antall =$fotocardfix;
					}
					else {
					$antall=$numero;
					}


						$articles['prints'][] = array(
													  'prodno' =>  $prodno,
													  'quantity' => $antall,
													  'file' => $img,
													  'fitin' => $fitin
													 );
					 }
					 
					 file_put_contents( $orderdateidfolder . "/ordertext.txt" , $orderinfo );
					 
				//	 Util::Debug( '*************************TESTE******************' );
					 Util::Debug( $articles );
					// die();
			 
					 $orderdata = new DBUtestemme();
					 $orderdata->utestemmeid = $order->id;
					 $orderdata->downloaded = date( 'Y-m-d H:i:s' );
					 $orderdata->projecttype = $key;
					 //$orderdata->save();
					 
					 $articles['productionmethod'][] = 352;
					 $articles['papertype'][] = 11;
					 
					 $comment = "Utestemme ordrenr: " . $order->id;
					 
					 $data = array(
					   'userid' => 1370892,
					   'fullname' => $order->shipping_address->name,
					   'address'  => $order->shipping_address->street,
					   'email' => $order->shipping_address->email,
					   'zipcode'  => $order->shipping_address->zip,
					   'city'  => $order->shipping_address->city,
					    'mobile_phone_number'  => $order->shipping_address->mobile_phone_number,
					   'article' => $articles,
					   'comment' => $comment
					 );
					 
					// die;	 
					  
					 $order = new ManualOrder();
					 $orderid = $order->executeManualOrder( $data );
					 $orderdata->eforderid =  $orderid;    
					 $orderdata->save();
					 
					 Util::Debug( $data );
					 
					 //edi
					 				 
					 
					 die();
				  }
			   }
			   
			   
			}
			}catch( Exception $e ){
			   Util::debug( $e->getMessage() );
			}
         }
   }
   
   CLI::Execute();

?>
