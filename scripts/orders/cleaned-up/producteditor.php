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
   model( 'producteditor.templates');

   class ProducteditrScript extends Script {
      
      public $orderfolder = '/home/produksjon/diverse/teste_php/producteditor/';
      public $backgroundfolder = '/data/global/producteditor/print/backgrounds/';
      
      
      
      
      
      Public function Main(){
            
            //Util::debug($this->webspoolFolder);
            
            
            $productorder = new DBproductedtorOrder(35);
            
            
            $template = new DBproducteditorTemplates($productorder->malid);
            
            Util::Debug($template);
            
            $pages = DB::query( "SELECT * FROM producteditor_order_page WHERE refid = ?", $productorder->id )->fetchAll( DB::FETCH_ASSOC );
            
            foreach( $pages as $key=>$page ){
                
                
                $editorwidth = 900;
                $editorheight = 515;
                
                
                $ratio = $template->printwidth / $editorwidth;
                
                Util::Debug($ratio);
                
                $pagesinfo = unserialize( $page['content'] );
                
                Util::Debug( $pagesinfo );
                
                $backgroundfile = $this->backgroundfolder  . basename( $pagesinfo['backgroundImage']['src'] );
                
                $canvas = new Imagick();
                //$canvas->newImage(350, 70, "white");
                $canvas->newImage($template->printwidth, $template->printheight, 'white' );
                
                if( !is_dir($backgroundfile) ){
                  $background = new Imagick( $backgroundfile );
                
                
                  $bgwidth = $pagesinfo['backgroundImage']['width'] * $ratio;
                  $bgheight = $pagesinfo['backgroundImage']['height'] * $ratio;
                
                  $background->resizeImage( (int)$bgwidth , (int)$bgheight , Imagick::FILTER_LANCZOS, 1 );
                
                  //$bgratio = $background->getImageWidth() / $pagesinfo['backgroundImage']['width'];
                
                  $top = $pagesinfo['backgroundImage']['top'] * $ratio;
                  $left = $pagesinfo['backgroundImage']['left'] * $ratio;
                
                  //Util::Debug( $pagesinfo );
                  $canvas->compositeImage( $background, $background->getImageCompose() , (int)$left , (int)$top );
               }
                
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
                
                
               Util::Debug( $orderfolder . $key . '_mal_with_image.jpg' );
               
                
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
            //Util::Debug($link);
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
            Util::Debug( $e->getMessage() );
            Util::Debug($link);
            die();
        }

        return $text;       

    }
    
    
   
   
   }
   

   CLI::Execute();

?>