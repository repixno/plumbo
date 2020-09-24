<?PHP
   
   import( 'website.mediacliporder' );
   library( 'xml.photobookbeta' );

   class CreateAssemblyWizard extends WebPage implements IView {
      
      
      protected $background = "{library}Backgrounds\PlainColors\aPlainColors01.jpg";
      
      
      
        public function Execute( $albumid = null ) {
                 
            $productid = '891000';
            $themeUrl = '{library}Photobook\Themes\A4 Stående\02_Classic\02_White\theme.xml';

            $string = file_get_contents('/var/www/repix/sites/website/views/create/prefill/text.xml');
            $string  = str_replace(':', '' , $string );
            $xml = simplexml_load_string($string);
         
            $cover = $xml->apiproject->photobookphotobook->photobookprintableCover;
            
            
            $width = (int)$cover['width'];
            $height = (int)$cover['height'];
            $bleed = (string)$cover['bleed'];
            $project = new ProjectXML('<?xml version="1.0" encoding="utf-8"?><orderRequest/>');
        
            $project->productid = $productid;
            $project->width = $width;
            $project->height = $height;
         
            $project->addProjectAttr();
                
            $projects = $project->addChild('projects');
            $photobook = $projects->addChild('hack:photobook:photobook');
            $photobook['productId'] = $productid;
            $photobook['plu'] = $productid;
        
            $photobook->AddState( $themeUrl);
         
            $printableCover = $photobook->addChild('hack:photobook:printableCover');
            $printableCover['bleed'] = 225;
            $printableCoverFront = $printableCover->addChild('hack:photobook:front');
            
            $printableCoverFront->addAttribute('width', (int)$cover->photobookfront['width']);
            $printableCoverFront->addAttribute( 'height', (int)$cover->photobookfront['height']);
            $printableCoverFront->addBackground( $this->background, $cover->photobookfront->collagebackgroundElement );
            //$printableCoverFront->AddPhoto();
            $printableCoverSide = $printableCover->addChild('hack:photobook:side' );
            $printableCoverSide->addAttribute('width', (int)$cover->photobookside['width']);
            $printableCoverSide->addAttribute('height',(int)$cover->photobookside['height']);
            $printableCoverSide->addBackground( $this->background , $cover->photobookside->collagebackgroundElement);
            $printableCoverBack = $printableCover->addChild('hack:photobook:back' );
            $printableCoverBack->addAttribute( 'width', (int)$cover->photobookback['width']);
            $printableCoverBack->addAttribute('height', (int)$cover->photobookback['height']);
            $printableCoverBack->addBackground( $this->background, $cover->photobookback->collagebackgroundElement );
            $pages = $photobook->addChild('hack:photobook:pages');

        
            $pagecount = 10;
        
            $id = "60f250eb-6716-4f90-bd2e-6ee7682b50fe";
            $id = $albumid;
            $albumjson = file_get_contents( 'https://a0.cptr.no/st/4/jobs/' . $id . '/changes?serial=1&key=KVEz-WDMsj' );
            $imagesjson = json_decode( $albumjson );

            $images = array();
            foreach($imagesjson as $ret ){
                
                if( count( $images[$ret->id] ) ){
                    if( $images[$ret->id]->comment ){
                        $images[$ret->id]->comment .= "\n" . $ret->comment;
                    }else{
                        $images[$ret->id]->comment = $ret->comment;
                    }
                }else{
                    $images[$ret->id] = $ret;
                }
            }
        
            $tmpimages = $images;
            foreach(  $tmpimages as $image ){
                $images[] = $image;
            }
        
            $photobookpages = $xml->apiproject->photobookphotobook->photobookpages;

            $bleed = (int)$photobookpages['bleed'];
            $pages->addAttribute('bleed', $bleed );
        
            $pagecount = 30;

            
            $tempplatesites = count($photobookpages->photobookpage);
            
            $tp = 0;
            $imagecount = 0;
            
            
            for ($i = 1; $i <= $pagecount; $i++) {
                
                if( $tp >= $tempplatesites ){
                    $tp = 0;
                }
                $photobookpage = $photobookpages->photobookpage[$tp];
                
                $element = $photobookpage->collagephotoElement;
                $backgroundElement = $photobookpage->collagebackgroundElement;
                $textElement = $photobookpage->collagetextElement;
                
                //$borders = $photobookpage->collagebackgroundElement;
                
                
                $width = (int)$photobookpage['width'];
                $height = (int)$photobookpage['height'];
                
                $page = $pages->addChild('hack:photobook:page');
                $page->addAttribute('width', $width );
                $page->addAttribute('height', $height );
                $page->addAttribute('style', 'a' );
             
                $page->addBackground( $this->background, $backgroundElement );
                
                $countelement =  count( $element );
                
                for ($c = 0; $c < $countelement; $c++) {
                    if( $images[$imagecount] ){
                        $image = $images[$imagecount];
                    }else{
                        $image = array(
                            'placeholder' => "{resources}",
                            'x' => "1396",
                            'y' => "1047",
                            'width' => "2000",
                            'height' => "2000"
                       );
                    }
                    $page->addPhotoMinSky($image, $id, $element[$c]);
                    
                    $texttest = $textElement[$c];
                    
                    
                    $text ="{insertYourTextHere}";
                    
                    try{
                        if( $image->comment && $texttest['text'] ){
                            $text = $image->comment;
                        }
                    }catch(Exception $e){
                        
                    }
                    
                    //Util::Debug($text);
                    
                    if( $texttest ){
                        $page->AddText( $texttest, $text );
                    }
                    
                    
                    
                    $imagecount++;
                }
                
                
                for ($t = 0; $t < $countelement; $t++) {
                    
                    //$page->AddText( $textElement[$t] ); 
    
                }
                
                $tp++;
                    
            }
        

         $xml =  $project->asXML();
        
        /*
        header("Content-type: text/xml");
        echo $xml;
        exit;*/
        
        
         $productid = $productid;
         $productoptionid = $productid;
         
         
         
         if( Login::isLoggedIn() ){
            $userid = Login::userid();
         }else{
            $userid = 639866;
         }
         
         $type = 'Photobook';

   
         try {

            $buffer = $xml;
            
            $projectid = DB::query( 'INSERT INTO
                                       mediaclip_projects ( id, user_id, title, description, created, saved, type ,predefinert, predefined_project_id, productid, productoptionid, project_xml )
                                    VALUES
                                       ( DEFAULT, ?, ?, ?, NOW(), NOW(), ?, NULL, NULL, ?, ?, ? )
                                    RETURNING id;',
                                       $userid, $projecttitle, '', $type, $productid, $productoptionid,  trim( $buffer ) 
                                 )->fetchSingle();
            
            relocate( '/create/%s/edit/%d', $type, $projectid );
            
            die();
            
         } catch( Exception $e ) {
            
            echo $e->getMessage();
            die();
            
         }
         
         
         
      }
      
      
      public function album( $albumid ) {
        
        
        
            $string = file_get_contents('/var/www/repix/sites/website/views/create/prefill/text.xml');         
            
            $string  = str_replace(':', '' , $string );
         
            $xml = simplexml_load_string($string);
        
            
        
            $productid = '891000';
            $themeUrl = '{library}Photobook\Themes\A4 Stående\02_Classic\02_White\theme.xml';
            
            $width = 2598;
            $height = 3484;
        
        
            $project = new ProjectXML('<?xml version="1.0" encoding="utf-8"?><orderRequest/>');
        
            $project->productid = $productid;
            $project->width = $width;
            $project->height = $height;
            
            $project->addProjectAttr();
        
        
            $projects = $project->addChild('projects');
            $photobook = $projects->addChild('hack:photobook:photobook');
            $photobook['productId'] = $productid;
            $photobook['plu'] = $productid;
            
            $photobook->AddState( $themeUrl);
        
        
            $printableCover = $photobook->addChild('hack:photobook:printableCover');
            $printableCover['bleed'] = 225;
        
        
            $printableCoverFront = $printableCover->addChild('hack:photobook:front');
            //$printableCoverFront->addBackground( $this->background );
            //$printableCoverFront->AddPhoto();
        
            $printableCoverSide = $printableCover->addChild('hack:photobook:side');
            //$printableCoverSide->addBackground( $this->background );
        
            $printableCoverBack = $printableCover->addChild('hack:photobook:back');
            //$printableCoverBack->addBackground( $this->background );
            $pages = $photobook->addChild('hack:photobook:pages');
      
            $photobookpages = $xml->apiproject->photobookphotobook->photobookpages;

            $bleed = (int)$photobookpages['bleed'];
            $pages->addAttribute('bleed', $bleed );
        
            $pagecount = 30;
        
            $album = new Album( $albumid );
            $images = $album->getImages();
            
            $tempplatesites = count($photobookpages->photobookpage);
            
            $tp = 0;
            $imagecount = 0;
            
            
            for ($i = 1; $i <= $pagecount; $i++) {
                
                if( $tp >= $tempplatesites ){
                    $tp = 0;
                }
                $photobookpage = $photobookpages->photobookpage[$tp];
                
                $element = $photobookpage->collagephotoElement;
                $backgroundElement = $photobookpage->collagebackgroundElement;
                $textElement = $photobookpage->collagetextElement;
                
                //$borders = $photobookpage->collagebackgroundElement;
                
                
                $width = (int)$photobookpage['width'];
                $height = (int)$photobookpage['height'];
                
                $page = $pages->addChild('hack:photobook:page');
                $page->addAttribute('width', $width );
                $page->addAttribute('height', $height );
                $page->addAttribute('style', 'a' );
             
                $page->addBackground( $this->background, $backgroundElement );
                
                $countelement =  count( $element );
                
                for ($c = 0; $c < $countelement; $c++) {
                    if( $images[$imagecount] ){
                        $image = $images[$imagecount];
                    }else{
                        $image = array(
                            'placeholder' => "{resources}",
                            'x' => "1396",
                            'y' => "1047",
                            'width' => "2000",
                            'height' => "2000"
                       );
                    }
                    $page->AddPhoto($image, $element[$c]); 
                    $imagecount++;
                }
                
                
                for ($t = 0; $t < $countelement; $t++) {
                    
                    
                    $page->AddText( $textElement[$t] ); 
    
                }
                
                $tp++;
                    
            }
        

        $xml =  $project->asXML();
        
        
         $productid = $productid;
         $productoptionid = $productid;
         
         $userid = Login::userid();
         
         $type = 'Photobook';

   
         try {

            $buffer = $xml;
            
            $projectid = DB::query( 'INSERT INTO
                                       mediaclip_projects ( id, user_id, title, description, created, saved, type ,predefinert, predefined_project_id, productid, productoptionid, project_xml )
                                    VALUES
                                       ( DEFAULT, ?, ?, ?, NOW(), NOW(), ?, NULL, NULL, ?, ?, ? )
                                    RETURNING id;',
                                       $userid, $projecttitle, '', $type, $productid, $productoptionid,  trim( $buffer ) 
                                 )->fetchSingle();
            
            relocate( '/create/%s/edit/%d', $type, $projectid );
            
            die();
            
         } catch( Exception $e ) {
            
            echo $e->getMessage();
            die();
            
         }
            

         
      }
   
   }
   
?>