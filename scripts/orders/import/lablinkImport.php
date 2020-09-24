<?PHP

/******************************************
* Script for handling Fovea modules
***************************************/

chdir( dirname( __FILE__ ) );
include '../../../bootstrap.php';

config( 'website.config' );
config( 'production.lablink');
config( 'production.lablinkpartner');
import( 'system.cli' );
model( 'production.lablink' );
import( 'website.order.manual.lablink' );


class LablinkImportScript extends Script {

    private $importfolder = "/home/produksjon/teste_php/LabLink";
    private $orderfolder = "/mnt/netlife/orderxml-processed/";
	
	private $userid = 1029655;
    
    Public function Main(){
		
        $productkobling = Settings::Get( 'lablink' , 'article' );
		$partnerarray =  Settings::Get( 'lablink' , 'partner' );

        foreach( glob($this->orderfolder   . '*') as $order ){
			$i = 1;
            $xmlfile = $order;
			
            if (file_exists( $xmlfile )){
				
				$orderxml = simplexml_load_file( $xmlfile );
				
				$partner = $orderxml->partner->name;
				$lablinkid = (string)$orderxml['uuid'];
				$lablinkorderid =  (string)$orderxml['worknr'] . ' - ' . substr(  $lablinkid , 0 , 8 ) ;
				$lablinkorderid2 =  (string) substr(  $lablinkid , 0 , 8 ) ;
				$comment = "Lablinkid: " . $lablinkorderid . "\n";
			
				$ordercheck = DB::query( 'SELECT id FROM production_lablink WHERE lablinkid = ? AND deleted is null', $lablinkid )->fetchSingle();
				//$ordercheck = DB::query( 'SELECT id FROM production_lablink WHERE lablinkid = ? AND eforderid = 1799831', $lablinkid )->fetchSingle();
				
				if( $ordercheck ){
					$now = time();
					if ($now - filemtime($xmlfile) >= 60 * 60 * 24 * 10){
						rename($xmlfile, "/mnt/netlife/orderxml-processed-old/" . basename($xmlfile) );
					}
					continue;
				}
				
				foreach( $orderxml->orderlines->orderline  as $orderline ){
					
					$producer = $orderline->product->producer;
					
					
					Util::Debug($orderline);
					
					Util::Debug($producer['you']);
					
					if( $producer['you'] != 'true' ){
						
						Util::Debug("NOT YUE");
						
						continue;
					}
					
					Util::Debug("YOU");
					
					$filefolder = (string)$orderline->files['folder'];
					$filefolder = str_replace( 'N:\\', '/home/produksjon/', $filefolder );
					$filefolder = str_replace( '\\', '/', $filefolder );
					$productid = $productkobling[ (string)$orderline->product['uuid'] ];
					$quantity = (int)$orderline->quantity;
					$title = (string)$orderline->product->productstring;
					$title2 = (string)$orderline->description;
					
					
					/*if ( $this->endsWith($filefolder,'/') == false) 
					{
						$filefolder = $filefolder."/";
					}
					*/
					
					//Util::Debug( $filefolder );
					$baseprice=(string)$orderxml->pricesummary->baseprice;
					$startprice=(string)$orderxml->pricesummary->startprice;
					$delivery=(string)$orderxml->pricesummary->deliveryprice;
					$totalprice=(string)$orderxml->pricesummary->totalprice;		
						
					
					if( $orderline->product['uuid'] && !$productid ){
						$productid = 7378;
					}
					
					
					//util::debug($orderline);
					//util::debug((string)$orderline->product['uuid']);
					$price = (float)$orderline->avgprice;
					
					//exit;
					
					if( $productid == 7439 ){
						$pages[] = array( 'filename' => 'nofile', 'index' => 0 );
						$comment = "Lablinkid: " . $lablinkorderid . "\n" . $title2 . "\n";
						$articles['gifts'][$productid][$i] = array(
												  'prodno' =>  $productid,
												  'quantity' => $quantity,
												  'referenceid' => $i,
												  'fitin' =>  $orderline['Adaption'],
												  'text' => $title,
												
												  'pages' => $pages,
												  'unitprice' => $price
												 );
			
						//util::debug($title);
							//util::debug($comment);
						//exit;
						
					}
					else if( $productid == 482 ){
						$articles['goods'][] = array(
											'prodno' =>  $productid,
											'quantity' => $quantity,
											'file' => 'nofile',
											'fitin' => $orderlines['Adaption'],
											'text' => $title,
											'unitprice' => $price
										   );
					}
					else if( $productid && !( (int)$orderline->lineprice < 0 ) ){
						
						if( file_exists( $filefolder ) || $productid == 7484 || $productid == 480 ){
							
							$productinfo = ProductOption::fromRefId( $productid );
							
							if( $productinfo->hasTag('print')  || $productinfo->hasTag('enlargement') ){
								
									$file = (string)$orderline->files->file->path;
									
									
									if( is_dir( $file ) ){
									
										foreach( glob( $filefolder   . '*') as $file ){
											$articles['prints'][] = array(
															  'prodno' =>  $productid,
															  'quantity' => $quantity,
															  'file' => $file,
															  'fitin' => $orderlines['Adaption'],
															  'text' => $title,
															  'unitprice' => $price
															 );
										}
											
									}
									else{
										$fitin = (string)$orderline->files->file->adaption;
										
										
										$file = str_replace( 'N:\\', '/home/produksjon/', $file );
										$file = str_replace( '\\', '/', $file );
										
										
										$articles['prints'][] = array(
														  'prodno' =>  $productid,
														  'quantity' => $quantity,
														  'file' => $file,
														  'fitin' => $fitin,
														  'text' => $title,
														  'unitprice' => $price
														 );
										}
							}
							/*else if( $productinfo->hasTag('enlargement') ){
								foreach( glob( $filefolder   . '*') as $file ){
									$articles['prints'][] = array(
													  'prodno' =>  $productid,
													  'quantity' => $quantity,
													  'file' => $file,
													  'fitin' => $orderlines['Adaption'],
													  'text' => $title,
													  'unitprice' => $price
													 );
								}
							}*/
							else if( $productinfo->hasTag('gift') ){
								
								
								$pages = array();
								$count = 0;
								foreach( glob( $filefolder   . '*') as $file ){
									$pages[] = array( 'filename' => $file, 'index' => $count );
									$count++;
								}
								
								if( $orderline->product->subproduct ){
									$title .= " - " . (string)$orderline->product->subproduct;
								}
								
								
								$articles['gifts'][$productid][$i] = array(
												  'prodno' =>  $productid,
												  'quantity' => $quantity,
												  'referenceid' => $i,
												  'fitin' =>  $orderline['Adaption'],
												  'text' => $title,
												  'pages' => $pages,
												  'unitprice' => $price
												 );
							}
							else if( $productinfo->hasTag('mediaclip') ){
								//if( pathinfo( $file, PATHINFO_EXTENSION )  == 'pdf' ){
								//$count = 0;
								foreach( glob( $filefolder   . '*') as $file ){
									$pages[] = array( 'filename' => $file, 'index' => $count );
									$count++;
								}
								
								$articles['gifts'][$productid][$i] = array(
												  'prodno' =>  $productid,
												  'quantity' => $quantity,
												  'referenceid' => $i,
												  'fitin' =>  $orderline['Adaption'],
												  'text' => $title,
												  'pages' => $pages,
												  'unitprice' => $price
												 );
								//}		
							}
						}
						$i++;
					}
					
					
				}
				
				
				if( (string)$orderxml->portal['uuid'] == 'c3fc27bc-a319-c6ee-50fc-90bd203ab340' ){
					$merchantid= "60581";
					
					
				}
				
				
				$klarna=$orderxml->delivery['type'];
				
				//Util::Debug($articles);
				$orderdata = new DBLablink();
				$orderdata->lablinkid = $lablinkid;
				$orderdata->downloaded = date( 'Y-m-d H:i:s' );
				$orderdata->projecttype = (string)$orderxml->partner->name;
				$orderdata->partner = $partner;
				$orderdata->lablinkorderid = $lablinkorderid2;
				$orderdata->payment = $klarna;
				$orderdata->merchantid = $merchantid;

				if( (string)$orderxml->delivery['type'] == 'INSTORE' ){
					$comment .= "levering i butikk.\n";
					//$comment .= (string)$orderxml->user->name . ', ' . (string)$orderxml->user->email . ', ' . (string)$orderxml->user->mobile;
					$comment .= (string)$orderxml->order->comment . ', ' . (string)$orderxml->user->name . ', ' .(string)$orderxml->user->email . ', ' . (string)$orderxml->user->mobile;
				}
				
				
				

				
				$this->userid = $partnerarray[(string)$orderxml->portal['uuid']];
				
				
				if( $articles ){
					
					
					$articles['productionmethod'][] = 128;		
					$articles['papertype'][] = 10;
					
				
					$data = array(
						   'userid' => $this->userid,
						   'kampanje' => 'Netlife',
						   'fullname' => (string)$orderxml->delivery->address->name,
						   'address'  => (string)$orderxml->delivery->address->address1,
						   'zipcode'  => (string)$orderxml->delivery->address->postalcode,
						   'city'  => (string)$orderxml->delivery->address->city,
						   'delivery'  => (string)$orderxml->pricesummary->deliveryprice,
                           'mobile_phone_number'  => (string)$orderxml->user->mobile,
						 // 'basecost'  => (string)$orderxml->pricesummary->startprice,
						  'totalprice'  => (string)$orderxml->pricesummary->totalprice,
						   'startprice'  => (string)$orderxml->pricesummary->startprice,	  
						   'article' => $articles,
						   'comment' => $comment
						 );
				
					Util::Debug($articles);
					Util::Debug($comment);
					Util::Debug($data);
					Util::Debug('Baseprice:'. $baseprice);
                   
					Util::Debug('Delivery:'. $delivery);
					Util::Debug('Startprice:'. $startprice);
					Util::Debug('Totalprice:'. $totalprice);
					Util::Debug('Netlife Ordrenr:'. $klarna);
					Util::Debug('Merchant nr:'. $merchantid);
					
					
					
				//	exit;
	
					$order = new ManualOrder();
					$orderid = $order->executeManualOrder( $data );
						
					$orderdata->eforderid =  $orderid;
				
				}
				else{
					$orderdata->eforderid =  0;
				}
				$orderdata->save();
				
				
				Util::Debug($orderid);
				
				die();
                
            } 
            
        }
    
    }
	
	public function endsWith($FullStr, $needle)
    {
        $StrLen = strlen($needle);
        $FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
        return $FullStrEnd == $needle;
    }
}


CLI::Execute();

?>
