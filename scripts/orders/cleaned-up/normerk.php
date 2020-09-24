<?PHP

   /******************************************
    *TEST SCRIPT FOR ADDING CUTMARKS
    ***************************************/
   chdir( dirname( __FILE__ ) );
   include '../../bootstrap.php';

   config( 'website.config' );
   config( 'website.countries' );
   import( 'system.cli' );
   import( 'website.order' );
   model( 'producteditor.producteditororder');
   model( 'producteditor.producteditororderpage');

   class ProducteditrScript extends Script {
      
      public $orderfolder = '/home/produksjon/stempel/';
      
      
      Public function Main(){
         
         $orders = DB::query( "SELECT * FROM producteditor_order WHERE orderid > 0 and downloaded is null " )->fetchAll( DB::FETCH_ASSOC );
         
         //$orders = DB::query( "SELECT * FROM producteditor_order WHERE orderid = 1823377 " )->fetchAll( DB::FETCH_ASSOC );
         
         foreach(  $orders as $order ){
            
            
            $ordrenr = $order['orderid'];
         
            $orderinfo = DB::query( "SELECT * FROM historie_ordre WHERE ordrenr = ?", $ordrenr )->fetchAll( DB::FETCH_ASSOC );
            
            $pages = DB::query( "SELECT * FROM producteditor_order_page WHERE refid = ?", $order['id'] )->fetchAll( DB::FETCH_ASSOC );
            
            foreach( $pages as $page ){
               
               //Util::Debug( $page );
               
               try{
               
                  //$file = file_get_contents( "http://normerk.eurofoto.no/api/1.0/tedit/createstamp/" . $page['id'] );
                  
                  
                  $pagearray = unserialize( $page['content'] );
                  
                  
                  Util::Debug("http://normerk.eurofoto.no/api/1.0/tedit/createstamp/" . $page['id']);
                  
                  $handle = fopen("http://normerk.eurofoto.no/api/1.0/tedit/createstamp/" . $page['id'] , 'rb');
                  
                  $image = new Imagick();
                  
                  $image->readImageFile( $handle );
                  $image->setColorspace(imagick::COLORSPACE_RGB);
                  
                  $image->setResolution(300,300);
                  
                  

                  

                  $artnr = DB::query( "SELECT productid FROM producteditor_templates WHERE id = ?", $order['malid'] )->fetchSingle() + 10000;
    
                  $antall = DB::query( "SELECT antall FROM historie_ordrelinje WHERE malid = ? AND artikkelnr = ?", $order['id'], $artnr )->fetchSingle();
                   
                  $orderfolder = $this->orderfolder . date( 'Y-m-d', strtotime( $orderinfo[0]['tidspunkt'] ) ) . "/" . $ordrenr . "/"  . $artnr . "/"; 
   
                  if( !file_exists($orderfolder ) ){
                     
                     mkdir( $orderfolder, 0775, true );
                     
                  }
                  
                  
                  foreach( $pagearray['objects'] as $pg ){
                     
                     if( $pg['type'] == 'image'){
                        Util::Debug( $pg );
                     
                        $imgwidth = $pg['width'] * $pg['scaleX'];
                        $imgheight = $pg['height'] * $pg['scaleY'];
                     
                        $left = $pg['left'] - ( $imgwidth / 2 );
                        $top = $pg['top'] - ( $imgheight / 2 ); 
                        
                        $handle = fopen($pg['src'] , 'rb');
                        
                        //$url = $pg['src'] ;
                        
                        $img = new Imagick();
                        $img->readImageFile($handle);
                        $img->setColorspace(imagick::COLORSPACE_RGB);
                        $img->resizeImage($imgwidth, $imgheight,Imagick::FILTER_LANCZOS,1);
                        
                        $image->compositeImage($img, $img->getImageCompose(), $left,  $top);
                        
                        
                     }
                     
                  }
                  
                  
                  $pfile = $orderfolder . $page['id'] . "-" . $antall . "-" . $artnr . ".png";
   
                  Util::Debug($pfile);
                  
                  $image->writeImage( $pfile );
               }
               catch( Exception $e ){
                  
                  Util::Debug( $e->getMessage() );
                  
               }
               
               
               //file_put_contents( $orderfolder . $page['id'] . "-" . $antall . "-" . $artnr . " .png" , $file );
                   
            }
            
            DB::query( 'UPDATE producteditor_order set downloaded = now() WHERE id = ?', $order['id'] )->fetchSingle();
              
         }
            
         
      exit;
            
            //Util::debug($this->webspoolFolder);
            
            
            $productorder = new DBproductedtorOrder(40);
            
            
            $template = new DBproducteditorTemplates($productorder->malid);
            
            $pages = DB::query( "SELECT * FROM producteditor_order_page WHERE refid = ?", $productorder->id )->fetchAll( DB::FETCH_ASSOC );
            
            
            foreach( $pages as $key=>$page ){
                
                
                
                util::debug( unserialize( $page['content'] ));
                
                
                file_put_contents( '/home/produksjon/diverse/test/test40.svg',  unserialize( $page['content'] ) );
                
                exit;
                
                $pagesinfo = unserialize( $page['content'] );
                
                   foreach( $pagesinfo['objects'] as $object ){
                    
                     $text = $object['text'];
                     
                     Util::Debug($text);
                     
                     //$pdf = new FPDF();
                     $pdf = new FPDF('P','mm',array(175,150));
                     $pdf->AddPage();
                     $pdf->SetFont('Arial','B',36);
                     $pdf->Multicell(0,5, $text, 0 );
                     
                     $filename="/home/produksjon/diverse/test/test.pdf";
                     $pdf->Output($filename,'F');
                     //$pdf->Output();
                    
                   }
                    
            }
            
            exit;
            
            foreach( $pages as $key=>$page ){
                
                
                $editorwidth = 900;
                $editorheight = 515;
                
                
                $ratio = $template->printwidth / $editorwidth;
                
                Util::Debug($ratio);
                
                $pagesinfo = unserialize( $page['content'] );
                
                $backgroundfile = $this->backgroundfolder  . basename( $pagesinfo['backgroundImage']['src'] );
                
                $canvas = new Imagick();
                //$canvas->newImage(350, 70, "white");
                $canvas->newImage($template->printwidth, $template->printheight, 'white' );
                
                $background = new Imagick( $backgroundfile );
                
                
                $bgwidth = $pagesinfo['backgroundImage']['width'] * $ratio;
                $bgheight = $pagesinfo['backgroundImage']['height'] * $ratio;
                
                $background->resizeImage( (int)$bgwidth , (int)$bgheight , Imagick::FILTER_LANCZOS, 1 );
                
                //$bgratio = $background->getImageWidth() / $pagesinfo['backgroundImage']['width'];
                
                $top = $pagesinfo['backgroundImage']['top'] * $ratio;
                $left = $pagesinfo['backgroundImage']['left'] * $ratio;
                
                //Util::Debug( $pagesinfo );
                $canvas->compositeImage( $background, $background->getImageCompose() , (int)$left , (int)$top );

                
                foreach( $pagesinfo['objects'] as $object ){
                    
                    

                    
                    if( $object['type'] == 'i-text' ){
                        $text = $this->drawtext( $object, $ratio );
                        $canvas->compositeImage( $text, $text->getImageCompose() , 0 , 0 );
                    }
                    else if( $object['type'] == 'newimage' &&  $object['id']  == 'clipart' ){
                        $image = $this->drawclipart( $object, $ratio );
                        $canvas->compositeImage( $image, $image->getImageCompose() , 0 , 0 );
                    }
                    else if( $object['type'] == 'newimage' && is_numeric( $object['id'] ) ){
                        $image = $this->drawimage( $object, $ratio );
                        $canvas->compositeImage( $image, $image->getImageCompose() , 0 , 0 );
                    }
                    
                    
                   
                    
                }
                
                

               $orderfolder = $this->orderfolder . $productorder->id . '/';
               
               if( !file_exists ($orderfolder ) ){
                  mkdir($orderfolder);
               }
                
                
         
                
                $canvas->writeImage( $orderfolder . $key . '_mal_with_image.jpg' );
                
                 
                   
            }
      }
      
      
      
    private function drawimage( $object, $ratio  ) {
        
        $send = json_encode( $object,JSON_UNESCAPED_UNICODE );
        $send = urlencode( $send ) ;
        
        
        
        $link = sprintf( 'http://marie.eurofoto.no/api/1.0/producteditor/node_image?object=%s&ratio=%s' , $send, $ratio );
        
        try{
            $image = new Imagick($link);
        }catch( Exception $e ){
            Util::Debug( $e );
            Util::Debug($link);
            die();
        }

        return $image;       

    }
    
    private function drawclipart( $object, $ratio  ) {
        
        $send = json_encode( $object,JSON_UNESCAPED_UNICODE );
        $send = urlencode( $send ) ;
        
        $image = new Imagick();

        $link = sprintf( 'http://marie.eurofoto.no/api/1.0/producteditor/node_clipart?object=%s&ratio=%s' , $send, $ratio );          
        
        try{
            //$f = fopen( $link , 'rb');
            
            //$f = file_get_contents($link);
            
            $image = new Imagick($link);
        }catch( Exception $e ){
            Util::Debug( $e->getMessage() );
            Util::Debug($link);
            die();
        }

        return $image;       

    }
    
    private function drawtext( $object, $ratio  ) {
        
        $send = json_encode( $object,JSON_UNESCAPED_UNICODE );
        $send = urlencode( $send ) ;
        
        $text = new Imagick();
  
        $link = sprintf( 'http://marie.eurofoto.no/api/1.0/producteditor/node_text?object=%s&ratio=%s' , $send, $ratio );          
        
        try{
            $f = fopen( $link , 'rb');
            $text->readImageFile($f);
        }catch( Exception $e ){
            //Util::Debug( $e );
            //Util::Debug($link);
            die();
        }

        return $text;       

    }
    
    
   
   
   }
   

   CLI::Execute();

?>