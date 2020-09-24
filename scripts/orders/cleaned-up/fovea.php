<?PHP
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   config( 'production.settings');
   import( 'system.cli' );
   import( 'website.order' );
   import( 'website.order.manual.default' );
   
   library( 'pdf.fpdf' );
   
   model( 'production.fovea' );
   
   class OrderImportUtestemmeScript extends Script {
      
      public $tidspunkt = '';
      public $orderfolder = "/data/pd/fovea/";
      
      public $foveazetta = "/home/produksjon/fovea/";
      
      private $userid =  983136;
      
      Public function Main(){
           
            $orders = DB::query( "SELECT * FROM production_fovea WHERE toproduction IS NULL order by id desc" )->fetchAll( DB::FETCH_ASSOC );
            foreach( $orders as $order ){
               
               try{
               
                  $foveaorder = new DBFovea( $order['id'] );
                  
                  $orderlocation = $this->orderfolder . date( 'Y-m-d', strtotime( $foveaorder->downloaded  ) ) . '/' . $foveaorder->foveaid ;
                  
                  $xmlstr = file_get_contents( $orderlocation . '/'. $foveaorder->foveaid . '.txt' );
                  $orderxml = new SimpleXMLElement( $xmlstr );
  
                  $comment = "Fovea ordrenr: " . $foveaorder->id;
                  
                  $orderfolder = $this->foveazetta . date( 'Y-m-d', strtotime( $foveaorder->downloaded  ) );
                  
                  if( !file_exists( $orderfolder ) ){
                      mkdir( $orderfolder, 0755, true );
                      sleep(1);
                  }
                  
                  $orderfolder .= "/" .  $foveaorder->foveaid;
                  
                  if( !file_exists( $orderfolder ) ){
                      mkdir( $orderfolder, 0755, true );
                      sleep(1);
                  }
                  
                  $articles = array();
                  
                  $counter = 0;
                  
                  foreach( $orderxml->products->product  as $article ){
                      $counter++;
                      $imagefolder = $orderfolder . "/" .  (int)$article->articleId;
                      
                      if( !file_exists( $imagefolder ) ){
                          mkdir( $imagefolder, 0777 , true );
                          sleep(1);
                      }

                     $arr = parse_url((string)$article->imageUrl );
                     
                     if( strpos( $arr['query'], 'motive') ){
                        parse_str($arr['query']);
                     }else{
                        $motive = "gruppe";
                     }
                     
                     $img = sprintf( "%s/%s-%s-%04d-a%sm%sc%s.jpg" ,$imagefolder , $foveaorder->foveaid, (int)$article->articleId, (int)$article->designId , (int)$article->quantity, $motive, $counter  );
                     
                     file_put_contents($img, file_get_contents((string)$article->imageUrl));
                      
                     $articles['prints'][] = array(
                                                  'description' => (string)$article->description,
                                                  'designid' => (int)$article->designId,
                                                  'prodno' =>  (int)$article->articleId,
                                                  'quantity' => (int)$article->quantity,
                                                  'file' =>  $img,
                                                  'fitin' => $fitin,
                                                  'design' => (string)$article->description,
                                                  'motive' => (string)$motive
                                              );
                  }
                     
                  $data = array(
                      'userid' => $this->userid,
                      'foveaid' => $foveaorder->foveaid,
                      'customerId' => (string)$orderxml->customerId,
                      'fullname' => (string)$orderxml->shippingAddress->name,
                      'address'  => (string)$orderxml->shippingAddress->addressLine1,
                      'zipcode'  => (string)$orderxml->shippingAddress->zipCode,
                      'city'  => (string)$orderxml->shippingAddress->area,
                      'country'  => (string)$orderxml->shippingAddress->country,
                      'article' => $articles,
                      'comment' => $comment,
                      'date' => $foveaorder->downloaded
                  );
                  
                  try{
                     $this->createpdf( $data , $orderfolder );
                  }catch( Exception $e ){
                     util::Debug( "KA I HELVETE ");
                     util::Debug( $e );
                     
                  }
                  
                  
                  
                  $foveaorder->toproduction = date( 'Y-m-d H:i:s' );
                  
                  
                  $foveaorder->save();
                  
                  //$order = new ManualOrder();
                  //$orderid = $order->executeManualOrder( $data );
                  
                  //print_r( $data );
                
                
               }catch( Exception $e ){
                  $foveaorder->toproduction = date( 'Y-d-m H:i:s' );
                  $foveaorder->save();
                  mail( 'tor.inge@eurofoto.no' , 'foveabug', print_r( $e , true ) );
               }
            }
           
           
           die();
           
         }
         
         
         
         
         
         
         
      public function createpdf( $data, $orderfolder ){
            
            
            
            if( strtolower( $data['country'] )  ==  strtolower( "Sverige" ) ){
            
               
               $textarray = array(
                     'ordre' => 'Order',
                     'kundeid' => "Kundeid",
                     'ordredato' => 'Orderdatum',
                     'toptext' => 'Din beställning från www.fovea.se har blivit levererad',
                     'produkt' => 'Produkt',
                     'varekode' => 'Varukod',
                     'antall' => 'Antal',
                     'bottomtext' => 'Tack för Er beställning.'
               );
               
               Util::Debug( $textarray );
               
            }else{
               $textarray = array(
                        'ordre' => 'ORDRE',
                        'kundeid' => "Kundeid",
                        'ordredato' => 'Ordredato',
                        'toptext' => 'Din bestilling fra www.fovea.no er levert',
                        'produkt' => 'Produkt',
                        'varekode' => 'Varekode',
                        'antall' => 'Antall',
                        'bottomtext' => 'TAKK FOR HANDELEN!'
                  ); 
            }
            
            
            
            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->SetFont('times','',10);
            $pdf->SetY(20);
            $pdf->MultiCell(100,5, $textarray['ordre'] . ' # ' .  $data['foveaid'] . "\n" . $textarray['kundeid']  . " # "  . $data['customerId']  . "\n". $textarray['ordredato'] . ": " .  date( 'd/m Y', strtotime( $data['date'] ) )  );
            
            $pdf->SetY(50);
            $pdf->SetX(15);
            
            $pdf->SetFont('courier','',12);
            $pdf->MultiCell(100,5, utf8_decode( $data['fullname']  ) . "\n" .  utf8_decode( $data['address'] )  . "\n" . $data['zipcode']  . " " . utf8_decode( $data['city'] . "\n" . $data['country'] ) );
            
            $pdf->SetY(100);
            $pdf->SetX(10);
               
            $pdf->SetFont('times','',10);        
            $pdf->MultiCell(100,5, utf8_decode( $textarray['toptext'] ) );
            
            
            
            $header = array($textarray['produkt'], $textarray['varekode'], "Motiv" ,$textarray['antall'] );
            
            $orderlines = array();
            foreach( $data['article']['prints'] as $article  ){
               
               $orderlines[] = array(  utf8_decode( $article['description'] ) , $article['prodno'] . "-" . $article['designid'], $article['motive'],$article['quantity'] );
            }
            
            $pdf->FancyTable($header,$orderlines);
            
            
            $y = $pdf->GetY();
            
            $pdf->SetY( $y + 30);
            $pdf->SetX(10);
            $pdf->SetFont('times','',10);        
            $pdf->MultiCell(100,5, utf8_decode( $textarray['bottomtext'] ) );
            
            $content = $pdf->Output($orderfolder."/".  $data['foveaid'] .".pdf",'F');

            sleep( 5 );
            system( "lp -n 2 -d obelix " . $orderfolder."/".  $data['foveaid'] .".pdf" );


            
            
         }
         
   }
   
   class PDF extends FPDF{
      
      // Colored table
      function FancyTable($header, $data){
          // Colors, line width and bold font
          $this->SetFillColor(220);
          $this->SetTextColor(0);
          $this->SetDrawColor(190);
          $this->SetLineWidth(.3);
          $this->SetFont('','B');
          // Header
          $w = array(80, 50, 25, 25);
          for($i=0;$i<count($header);$i++)
              $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
          $this->Ln();
          // Color and font restoration
          $this->SetFillColor(240);
          //$this->SetDrawColor(255);
          $this->SetTextColor(0);
          $this->SetFont('');
          // Data
          $fill = false;
          foreach($data as $row)
          {
              $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
              $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
              $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
              $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
              $this->Ln();
              $fill = !$fill;
          }
          // Closing line
          $this->Cell(array_sum($w),0,'','T');
      }
   }
   
   CLI::Execute();
   
   


?>