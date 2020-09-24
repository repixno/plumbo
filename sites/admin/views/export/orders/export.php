<?PHP
   
   import( 'pages.json' );
   import( 'website.order.export' );
   config( 'website.countries' );
   
   class ExportNewOrders extends JSONPage implements IView {
      
      protected $template = false;
      
      public function Execute() {
         
         // fetch some information about previous order exports
         $lastorder = DB::query( 'SELECT MAX(lastorder) FROM order_export' )->fetchSingle();
         $nextbatch = DB::query( 'SELECT MAX(batchid)+1 FROM order_export' )->fetchSingle();
         
         // first, load up the country-list
         $countries = Settings::getSection( 'efcountries', array() );
         
         // query for new data
         $query = DB::query( "SELECT ho.ordrenr, 
                                     k.rubid, 
                                     ho.uid, 
                                     ho.kampanje_kode, 
                                     ho.tidspunkt, 
                                     ho.pris,
                                     hd.deliverymethod,
                                     hd.paymentmethod,
                                     rt.rate,
                                     hol.id,
                                     hol.pris, 
                                     hol.artikkelnr, 
                                     hol.antall, 
                                     hol.tekst,
                                     k.country,
                                     fbt.objectid,
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
                                     hk.dmphone
                                FROM historie_ordrelinje hol
                           LEFT JOIN historie_ordre ho
                                  ON ho.ordrenr = hol.ordrenr
                           LEFT JOIN kunde k
                                  ON ho.uid = k.uid
                           LEFT JOIN historie_delivery hd 
                                  ON hd.ordrenr = ho.ordrenr
                           LEFT JOIN finance_bbs_transaction fbt
                                  ON fbt.orderid = ho.ordrenr
                           LEFT JOIN region_delivery rd
                                  ON rd.deliveryid = hd.deliverymethod
                           LEFT JOIN region_tax rt 
                                  ON rt.regionid = rd.regionid
                           LEFT JOIN historie_kunde hk
                                  ON hk.ordrenr = ho.ordrenr
                               WHERE ho.to_production IS NOT NULL 
                                 AND ho.deleted IS NULL 
                                 AND ho.ordrenr > ?
                            ORDER BY ho.ordrenr", $lastorder );
	      
         while( list( $orderno, 
                      $customerno, 
                      $customerid, 
                      $portalcode, 
                      $ordertime, 
                      $grossamount,
                      $deliverymethod,
                      $paymentmethod,
                      $vatpst,
                      $lineid, 
                      $lineprice, 
                      $linearticleno, 
                      $linequantity, 
                      $linetext,
                      $country,
                      $transactionid,
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
         ) = $query->fetchRow() ) {
            
            // skip lines with zero quantity
            if( !$linequantity ) continue;
            
            // patch up any missing line text
            if( $linearticleno && !$linetext ) {
               $linetext = DB::query( 'SELECT tekst FROM artikkeloversikt WHERE artikkelnr = ?', $linearticleno )->fetchSingle();
            }
            
            // qualify the vatpst
            if( !$vatpst ) $vatpst = 25;
            
            // update the order object
            $orders[$orderno]['customerid'] = $customerid;
            $orders[$orderno]['customerno'] = $customerno;
            $orders[$orderno]['portalcode'] = $portalcode ? $portalcode : 'EF-997';
            $orders[$orderno]['orderno']    = $orderno;
            $orders[$orderno]['ordertime']  = date( 'Y-m-d H:i:s', strtotime( $ordertime ) );
            $orders[$orderno]['grossamount']= $grossamount;
            $orders[$orderno]['paymethod']  = $transactionid ? 'card' : 'bank';
            $orders[$orderno]['cardtrans']  = $transactionid;
            $orders[$orderno]['countrycode']= $countries[$country]['2char'];
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

         // now, create a new export object
         $batch = new OrderExport(); $key = 0;
         $batch->generated = 'now';
         
         // now, create the XML file to go
         $xml = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8" ?><orders />' );
         foreach( $orders as $order ) {
            
            $key++;
            if( !$batch->firstorder ) $batch->firstorder = $order['orderno'];
            if( $key == count( $orders ) ) {
               $batch->lastorder = $order['orderno'];
            }
            
            $ordernode = $xml->addChild('order');
            foreach( $order as $key => $val ) {
               if( $key != 'lines' && $key != 'invoiceaddress' && $key != 'deliveryaddress' ) {
                  $ordernode->$key = (string) $val;
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
                  $linenode->$key = $val;
               }
            }
            
         }
         
         $batch->save();
         
         header( 'content-type: text/xml' );
         echo $xml->asXML(); 
         die();
         
         util::Debug( $xml );
         die();
         
      }
      
   }
   
?>