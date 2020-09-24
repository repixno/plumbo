<?PHP
   
   config( 'website.countries' );
   
   import( 'services.navipartner.ftppush' );
   
   import( 'website.order.export' );
   import( 'pages.admin' );
   
   class NavisionExportScript extends WebPage implements IView {
      
      protected $template = false;
      
      public function Execute( $key = '', $batchno = 0, $autoupload = 1 ) {
         
         if( $key != 'auch3Ca6Op1chhaihmae1Veefeighei1C' ) die();
         header( 'Content-type: text/plain' );
         
         echo "Finding batch information...\n";
         
         // fetch some information about previous order exports
         $batch = new OrderExport( $batchno );
         if( !$batch->isLoaded() ) {
            echo "Unable to load batch!";
            die();
         }
         
         // first, load up the country-list
         $countries = Settings::getSection( 'efcountries', array() );
         
         echo "Querying new order data...\n";
         
         echo "Querying new order data...\n";
         
         // query for new data
         $query = DB::query( "SELECT DISTINCT(hol.id),
                                     ho.ordrenr, 
                                     k.rubid, 
                                     ho.uid, 
                                     ho.kampanje_kode, 
                                     ho.tidspunkt, 
                                     ho.pris,
                                     ho.logingroup,
                                     ho.trackingcode,
                                     hd.deliverymethod,
                                     hd.paymentmethod,
                                     hd.shopid,
                                     rt.rate,
                                     hol.pris, 
                                     hol.artikkelnr, 
                                     hol.antall, 
                                     hol.tekst,
                                     k.country,
                                     fbtl.transactionid,
                                     fbt.merchantid,
                                     hk.epost,
                                     hk.navn,
                                     hk.adresse1,
                                     hk.adresse2,
                                     hk.postnr,
                                     hk.sted,
                                     hk.land,
                                     hk.telefon,
                                     hk.mphone,
                                     hk.depost,
                                     hk.dnavn,
                                     hk.dadresse1,
                                     hk.dadresse2,
                                     hk.dpostnr,
                                     hk.dsted,
                                     hk.dland,
                                     hk.dtelefon,
                                     hk.dmphone,
				     k.krid,
				     k.contactemail
                                FROM historie_ordrelinje hol
                           LEFT JOIN historie_ordre ho
                                  ON ho.ordrenr = hol.ordrenr
                           LEFT JOIN kunde k
                                  ON ho.uid = k.uid
                           LEFT JOIN historie_delivery hd 
                                  ON hd.ordrenr = ho.ordrenr
                           LEFT JOIN finance_bbs_transaction fbt
                                  ON fbt.orderid = ho.ordrenr
                           LEFT JOIN finance_bbs_transaction_log fbtl
                                  ON fbtl.transactionid = fbt.objectid
                                 AND fbtl.operation = 'auth'
                                 AND fbtl.responsecode = 'OK'
                           LEFT JOIN region_delivery rd
                                  ON rd.deliveryid = hd.deliverymethod
                           LEFT JOIN region_tax rt 
                                  ON rt.regionid = rd.regionid
                           LEFT JOIN historie_kunde hk
                                  ON hk.ordrenr = ho.ordrenr
                               WHERE ho.to_production IS NOT NULL 
                                 AND ho.deleted IS NULL 
                                 AND ho.deleted IS NULL 
                                 AND ho.ordrenr BETWEEN ? AND ?
                            ORDER BY ho.ordrenr", $batch->firstorder, $batch->lastorder );
	      
         while( list( $lineid, 
                      $orderno, 
                      $customerno, 
                      $customerid, 
                      $portalcode, 
                      $ordertime, 
                      $grossamount,
                      $logingroup,
                      $trackingcode,
                      $deliverymethod,
                      $paymentmethod,
                      $shopid,
                      $vatpst,
                      $lineprice, 
                      $linearticleno, 
                      $linequantity, 
                      $linetext,
                      $country,
                      $transactionid,
                      $merchantid,
                      $invoiceemail,
                      $invoicerecipient,
                      $invoiceaddress1,
                      $invoiceaddress2,
                      $invoicezipcode,
                      $invoicecity,
                      $invoicecountry,
                      $invoicephone,
                      $invoicecell,
                      $deliveryemail,
                      $deliveryrecipient,
                      $deliveryaddress1,
                      $deliveryaddress2,
                      $deliveryzipcode,
                      $deliverycity,
                      $deliverycountry,
                      $deliveryphone,
                      $deliverycell,
		      $krid,
		      $contactemail
         ) = $query->fetchRow() ) {
            
            // skip lines with zero quantity
            if( !$linequantity ) continue;
            
            // patch up any missing line text
            if( $linearticleno && !$linetext ) {
               $linetext = DB::query( 'SELECT tekst FROM artikkeloversikt WHERE artikkelnr = ?', $linearticleno )->fetchSingle();
            }
            
            // qualify the vatpst
            if( !$vatpst ) $vatpst = 25;
            $portalcode = $portalcode ? $portalcode : 'EF-997';
	    
	    
            // update the order object
            $orders[$orderno]['customerid'] = $customerid;
            $orders[$orderno]['customerno'] = $customerno;
            $orders[$orderno]['portalcode'] = $portalcode;
            $orders[$orderno]['orderno']    = $orderno;
            $orders[$orderno]['ordertime']  = date( 'Y-m-d H:i:s', strtotime( $ordertime ) );
            $orders[$orderno]['grossamount']= $grossamount;
            $orders[$orderno]['paymethod']  = $transactionid ? 'card' : 'bank';
            $orders[$orderno]['cardtrans']  = $transactionid;
            $orders[$orderno]['captured']   = $transactionid ? ($merchantid != '317810' ? 'no' : 'yes') : 'bank';
            $orders[$orderno]['merchantid'] = $transactionid ? ($merchantid != '317810' ? (int) $merchantid : 0) : 0;
            $orders[$orderno]['countrycode']= $countries[$country]['2char'];
            $orders[$orderno]['logingroup'] = $logingroup;
            $orders[$orderno]['trackingcode']= $trackingcode;
            $orders[$orderno]['shopid']= $shopid;
            
            
            
            try{
               $newsletter = DB::query( "SELECT newsletter FROM kunde where uid = ?", $customerid )->fetchSingle();
            }catch ( Exception $e ){
               mail( 'tor.inge@eurofoto.no', "bugs med nesletter" , $e->getMessage() );
            }
            
            if( $newsletter == 't' ){
               $newsletter = 1;
            }else{
               $newsletter = 0;
            }
            
	    if( $portalcode == 'TK-001' ){
	       $invoiceemail = $contactemail;
	       $deliveryemail = $contactemail;
	    }
            
            $orders[$orderno]['invoiceaddress'] = array(
               'email' => $invoiceemail,
               'recipient' => ucwords( preg_replace( '/\s+/', ' ', $invoicerecipient ) ),
               'address1' => ucfirst( $invoiceaddress1 ),
               'address2' => ucfirst( $invoiceaddress2 ),
               'zipcode' => $invoicezipcode,
               'city' => ucfirst( $invoicecity ),
               'countrycode' => $countries[$invoicecountry]['2char'],
               'phone' => $invoicephone,
               'cellphone' => $invoicecell,
               'newsletter' => $newsletter,
	       'krid'	=> $krid,
	       'portalcode' => $portalcode
            );
            
            $orders[$orderno]['deliveryaddress'] = array(
               'email' => $deliveryemail,
               'recipient' => ucwords( preg_replace( '/\s+/', ' ', $deliveryrecipient ) ),
               'address1' => ucfirst( $deliveryaddress1 ),
               'address2' => ucfirst( $deliveryaddress2 ),
               'zipcode' => $deliveryzipcode,
               'city' => ucfirst( $deliverycity ),
               'countrycode' => $countries[$deliverycountry]['2char'],
               'phone' => $deliveryphone,
               'cellphone' => $deliverycell,
            );            
            
            $netunitprice = $lineprice / ( 1 + ( $vatpst / 100 ) );
            
            $orders[$orderno]['lines'][] = array(
               'lineid'    => $lineid,
               'articleno' => $linearticleno,
               'freetext'  => $linetext,
               'unitcount' => $linequantity,
               'unittype'  => 'stk',
               'unitprice' => $netunitprice,
               'netamount' => $netunitprice * $linequantity,
               'vatamount' => $netunitprice * $linequantity * ( $vatpst / 100 ),
               'grossamount'=> $lineprice * $linequantity,
               'vatpst'    => $vatpst,
            );
            
         }
         
         // did we find any orders?
         if( count( $orders ) ) {
            
            echo "Generating batch...\n";
            
            // now, create a new export object
            // $batch->generated = 'now';
            
            // now, create the XML file to go
            $xml = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><orders />' );
            foreach( $orders as $order ) {
               
               if($order['portalcode'] != 'SS-333'){
               
                  $ordernode = $xml->addChild('order');
                  foreach( $order as $key => $val ) {
                     if( $key != 'lines' && $key != 'invoiceaddress' && $key != 'deliveryaddress' ) {
                        $ordernode->$key = utf8_encode( (string) $val );
                     }
                  }
                  
                  $deliveryaddressnode = $ordernode->addChild('deliveryaddress');
                  foreach( $order['deliveryaddress'] as $key => $val ) {
                     $deliveryaddressnode->$key = utf8_encode( $val );
                  }
                  
                  $invoiceaddressnode = $ordernode->addChild('invoiceaddress');
                  foreach( $order['invoiceaddress'] as $key => $val ) {
                     $invoiceaddressnode->$key = utf8_encode( $val );
                  }
                  
                  $linenodes = $ordernode->addChild('lines');
                  foreach( $order['lines'] as $line ) {
                     $linenode = $linenodes->addChild('line');
                     foreach( $line as $key => $val ) {
                        $linenode->$key = utf8_encode( $val );
                     }
                  }
               }
               
            }
            
            echo "Saving batch...\n";
            $batch->save();
            
            echo "Saving XML...\n";
            $outputfile = sprintf( '%s/data/finance/%s-%06d-navision-export.xml', getRootPath(), date( 'Y-m-d', strtotime( $batch->generated ) ), $batch->batchid );
            $remotefile = basename( $outputfile );
            file_put_contents( $outputfile, $xml->asXML() );
            
            if( $autoupload ) {
               echo "Uploading XML ($remotefile)...\n";
               if( FTPPush::Send( $outputfile, $remotefile ) ) {
                  
                  $batch->uploaded = 'now';
                  $batch->save();
                  
               }
            }
            echo "Done!\n";
            
         } else {
            
            echo "No new orders.\n";
            
         }
         
      }
      
   }
   
?>